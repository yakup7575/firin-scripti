<?php
/**
 * Order Model
 */

require_once '../config/database.php';

class Order {
    private $conn;
    private $table = 'orders';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll($page = 1, $limit = 10, $search = '', $status = null, $date_from = null, $date_to = null) {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (o.order_number LIKE :search OR c.name LIKE :search OR c.email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if ($status) {
            $whereClause .= " AND o.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date_from) {
            $whereClause .= " AND DATE(o.order_date) >= :date_from";
            $params[':date_from'] = $date_from;
        }
        
        if ($date_to) {
            $whereClause .= " AND DATE(o.order_date) <= :date_to";
            $params[':date_to'] = $date_to;
        }
        
        $query = "SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone
                  FROM " . $this->table . " o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  $whereClause 
                  ORDER BY o.order_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $query = "SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address
                  FROM " . $this->table . " o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  WHERE o.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getOrderItems($orderId) {
        $query = "SELECT oi.*, p.name as current_product_name, p.image as product_image
                  FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function create($orderData, $orderItems) {
        try {
            $this->conn->beginTransaction();
            
            // Generate order number
            $orderNumber = $this->generateOrderNumber();
            
            // Insert order
            $orderQuery = "INSERT INTO " . $this->table . " 
                          (customer_id, order_number, total_amount, tax_amount, discount_amount, status, 
                           payment_status, payment_method, delivery_date, delivery_address, notes) 
                          VALUES (:customer_id, :order_number, :total_amount, :tax_amount, :discount_amount, :status, 
                                  :payment_status, :payment_method, :delivery_date, :delivery_address, :notes)";
            
            $orderStmt = $this->conn->prepare($orderQuery);
            
            $orderStmt->bindParam(':customer_id', $orderData['customer_id']);
            $orderStmt->bindParam(':order_number', $orderNumber);
            $orderStmt->bindParam(':total_amount', $orderData['total_amount']);
            $orderStmt->bindParam(':tax_amount', $orderData['tax_amount']);
            $orderStmt->bindParam(':discount_amount', $orderData['discount_amount']);
            $orderStmt->bindParam(':status', $orderData['status']);
            $orderStmt->bindParam(':payment_status', $orderData['payment_status']);
            $orderStmt->bindParam(':payment_method', $orderData['payment_method']);
            $orderStmt->bindParam(':delivery_date', $orderData['delivery_date']);
            $orderStmt->bindParam(':delivery_address', $orderData['delivery_address']);
            $orderStmt->bindParam(':notes', $orderData['notes']);
            
            $orderStmt->execute();
            $orderId = $this->conn->lastInsertId();
            
            // Insert order items
            $itemQuery = "INSERT INTO order_items 
                         (order_id, product_id, product_name, quantity, price, subtotal) 
                         VALUES (:order_id, :product_id, :product_name, :quantity, :price, :subtotal)";
            
            $itemStmt = $this->conn->prepare($itemQuery);
            
            foreach ($orderItems as $item) {
                $itemStmt->bindParam(':order_id', $orderId);
                $itemStmt->bindParam(':product_id', $item['product_id']);
                $itemStmt->bindParam(':product_name', $item['product_name']);
                $itemStmt->bindParam(':quantity', $item['quantity']);
                $itemStmt->bindParam(':price', $item['price']);
                $itemStmt->bindParam(':subtotal', $item['subtotal']);
                
                $itemStmt->execute();
                
                // Update product stock
                if ($item['product_id']) {
                    $this->updateProductStock($item['product_id'], $item['quantity']);
                }
            }
            
            $this->conn->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }
    
    public function updatePaymentStatus($id, $paymentStatus) {
        $query = "UPDATE " . $this->table . " SET payment_status = :payment_status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':payment_status', $paymentStatus);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        try {
            $this->conn->beginTransaction();
            
            // Get order items to restore stock
            $items = $this->getOrderItems($id);
            
            // Restore product stock
            foreach ($items as $item) {
                if ($item['product_id']) {
                    $this->updateProductStock($item['product_id'], -$item['quantity']);
                }
            }
            
            // Delete order items
            $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = :id";
            $deleteItemsStmt = $this->conn->prepare($deleteItemsQuery);
            $deleteItemsStmt->bindParam(':id', $id);
            $deleteItemsStmt->execute();
            
            // Delete order
            $deleteOrderQuery = "DELETE FROM " . $this->table . " WHERE id = :id";
            $deleteOrderStmt = $this->conn->prepare($deleteOrderQuery);
            $deleteOrderStmt->bindParam(':id', $id);
            $result = $deleteOrderStmt->execute();
            
            $this->conn->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function getCount($search = '', $status = null, $date_from = null, $date_to = null) {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (o.order_number LIKE :search OR c.name LIKE :search OR c.email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if ($status) {
            $whereClause .= " AND o.status = :status";
            $params[':status'] = $status;
        }
        
        if ($date_from) {
            $whereClause .= " AND DATE(o.order_date) >= :date_from";
            $params[':date_from'] = $date_from;
        }
        
        if ($date_to) {
            $whereClause .= " AND DATE(o.order_date) <= :date_to";
            $params[':date_to'] = $date_to;
        }
        
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  $whereClause";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    public function getRecentOrders($limit = 10) {
        $query = "SELECT o.*, c.name as customer_name 
                  FROM " . $this->table . " o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  ORDER BY o.order_date DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getOrderStats($dateFrom = null, $dateTo = null) {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if ($dateFrom) {
            $whereClause .= " AND DATE(order_date) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereClause .= " AND DATE(order_date) <= :date_to";
            $params[':date_to'] = $dateTo;
        }
        
        $query = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as total_revenue,
                    AVG(CASE WHEN status != 'cancelled' THEN total_amount ELSE NULL END) as avg_order_value,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                  FROM " . $this->table . " 
                  $whereClause";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    private function generateOrderNumber() {
        $prefix = 'ORD-';
        $year = date('Y');
        $month = date('m');
        
        // Get next order number for this month
        $query = "SELECT COUNT(*) + 1 as next_number 
                  FROM " . $this->table . " 
                  WHERE YEAR(order_date) = :year AND MONTH(order_date) = :month";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        
        $result = $stmt->fetch();
        $orderNumber = str_pad($result['next_number'], 4, '0', STR_PAD_LEFT);
        
        return $prefix . $year . $month . $orderNumber;
    }
    
    private function updateProductStock($productId, $quantity) {
        $query = "UPDATE products SET stock = stock - :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }
}
?>
<?php
/**
 * Customer Model
 */

require_once '../config/database.php';

class Customer {
    private $conn;
    private $table = 'customers';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query = "SELECT * FROM " . $this->table . " 
                  $whereClause 
                  ORDER BY created_at DESC 
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
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, phone, address, city, postal_code, birth_date, gender, notes) 
                  VALUES (:name, :email, :phone, :address, :city, :postal_code, :birth_date, :gender, :notes)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':birth_date', $data['birth_date']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':notes', $data['notes']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $setClause = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $setClause[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($setClause)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $setClause) . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        // Check if customer has orders
        $checkQuery = "SELECT COUNT(*) as count FROM orders WHERE customer_id = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            return ['error' => 'Cannot delete customer that has orders'];
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function getCount($search = '') {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " $whereClause";
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }
    
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function findByPhone($phone) {
        $query = "SELECT * FROM " . $this->table . " WHERE phone = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getCustomerStats($id) {
        $query = "SELECT 
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as total_spent,
                    MAX(o.order_date) as last_order_date,
                    AVG(o.total_amount) as avg_order_value
                  FROM orders o 
                  WHERE o.customer_id = :id AND o.status != 'cancelled'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getTopCustomers($limit = 10) {
        $query = "SELECT 
                    c.*,
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as total_spent
                  FROM " . $this->table . " c 
                  LEFT JOIN orders o ON c.id = o.customer_id 
                  WHERE o.status != 'cancelled' OR o.status IS NULL
                  GROUP BY c.id 
                  ORDER BY total_spent DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
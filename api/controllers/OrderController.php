<?php
/**
 * Order Controller
 */

require_once '../config/config.php';
require_once '../models/Order.php';
require_once '../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

class OrderController {
    private $order;
    
    public function __construct() {
        $this->order = new Order();
    }
    
    public function getAll() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : ITEMS_PER_PAGE;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
        $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;
        
        $orders = $this->order->getAll($page, $limit, $search, $status, $date_from, $date_to);
        $total = $this->order->getCount($search, $status, $date_from, $date_to);
        
        echo json_encode([
            'success' => true,
            'data' => $orders,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    public function getById($id) {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $order = $this->order->getById($id);
        
        if ($order) {
            $orderItems = $this->order->getOrderItems($id);
            $order['items'] = $orderItems;
            
            echo json_encode(['success' => true, 'data' => $order]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
        }
    }
    
    public function create() {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['items']) || empty($data['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Order items are required']);
            return;
        }
        
        // Calculate totals
        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $taxRate = 18; // 18% KDV
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $data['discount_amount'] ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;
        
        $orderData = [
            'customer_id' => $data['customer_id'] ?? null,
            'total_amount' => $totalAmount,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'status' => $data['status'] ?? 'pending',
            'payment_status' => $data['payment_status'] ?? 'pending',
            'payment_method' => $data['payment_method'] ?? 'cash',
            'delivery_date' => $data['delivery_date'] ?? null,
            'delivery_address' => $data['delivery_address'] ?? '',
            'notes' => $data['notes'] ?? ''
        ];
        
        // Prepare order items
        $orderItems = [];
        foreach ($data['items'] as $item) {
            $orderItems[] = [
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ];
        }
        
        try {
            $orderId = $this->order->create($orderData, $orderItems);
            
            if ($orderId) {
                $newOrder = $this->order->getById($orderId);
                echo json_encode(['success' => true, 'data' => $newOrder]);
            } else {
                throw new Exception('Failed to create order');
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public function updateStatus($id) {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Status is required']);
            return;
        }
        
        if ($this->order->updateStatus($id, $data['status'])) {
            $updatedOrder = $this->order->getById($id);
            echo json_encode(['success' => true, 'data' => $updatedOrder]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order status']);
        }
    }
    
    public function updatePaymentStatus($id) {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['payment_status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Payment status is required']);
            return;
        }
        
        if ($this->order->updatePaymentStatus($id, $data['payment_status'])) {
            $updatedOrder = $this->order->getById($id);
            echo json_encode(['success' => true, 'data' => $updatedOrder]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update payment status']);
        }
    }
    
    public function delete($id) {
        if (!AuthMiddleware::requireRole('admin')) {
            return;
        }
        
        try {
            if ($this->order->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
            } else {
                throw new Exception('Failed to delete order');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public function getRecent() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $orders = $this->order->getRecentOrders($limit);
        
        echo json_encode(['success' => true, 'data' => $orders]);
    }
    
    public function getStats() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
        $dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;
        
        $stats = $this->order->getOrderStats($dateFrom, $dateTo);
        
        echo json_encode(['success' => true, 'data' => $stats]);
    }
}

// Handle API requests
$controller = new OrderController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $controller->getById($_GET['id']);
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'recent':
                    $controller->getRecent();
                    break;
                case 'stats':
                    $controller->getStats();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint not found']);
            }
        } else {
            $controller->getAll();
        }
        break;
        
    case 'POST':
        $controller->create();
        break;
        
    case 'PUT':
        if (isset($_GET['id'])) {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'status':
                        $controller->updateStatus($_GET['id']);
                        break;
                    case 'payment-status':
                        $controller->updatePaymentStatus($_GET['id']);
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(['error' => 'Action not found']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Action is required']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID is required']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID is required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
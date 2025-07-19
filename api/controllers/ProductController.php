<?php
/**
 * Product Controller
 */

require_once '../config/config.php';
require_once '../models/Product.php';
require_once '../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

class ProductController {
    private $product;
    
    public function __construct() {
        $this->product = new Product();
    }
    
    public function getAll() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : ITEMS_PER_PAGE;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        
        $products = $this->product->getAll($page, $limit, $search, $category_id);
        $total = $this->product->getCount($search, $category_id);
        
        echo json_encode([
            'success' => true,
            'data' => $products,
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
        
        $product = $this->product->getById($id);
        
        if ($product) {
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    }
    
    public function create() {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['name', 'price', 'stock'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Field '$field' is required"]);
                return;
            }
        }
        
        // Set defaults
        $data['old_price'] = $data['old_price'] ?? 0;
        $data['min_stock'] = $data['min_stock'] ?? 5;
        $data['image'] = $data['image'] ?? '';
        $data['status'] = $data['status'] ?? 'active';
        $data['featured'] = isset($data['featured']) ? (bool)$data['featured'] : false;
        
        $productId = $this->product->create($data);
        
        if ($productId) {
            $newProduct = $this->product->getById($productId);
            echo json_encode(['success' => true, 'data' => $newProduct]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create product']);
        }
    }
    
    public function update($id) {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Remove empty values to avoid updating with nulls
        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });
        
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data provided']);
            return;
        }
        
        if ($this->product->update($id, $data)) {
            $updatedProduct = $this->product->getById($id);
            echo json_encode(['success' => true, 'data' => $updatedProduct]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product']);
        }
    }
    
    public function delete($id) {
        if (!AuthMiddleware::requireRole('admin')) {
            return;
        }
        
        if ($this->product->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product']);
        }
    }
    
    public function getLowStock() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $products = $this->product->getLowStockProducts();
        echo json_encode(['success' => true, 'data' => $products]);
    }
    
    public function updateStock($id) {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Quantity is required']);
            return;
        }
        
        $operation = $data['operation'] ?? 'add';
        $quantity = (int)$data['quantity'];
        
        if ($this->product->updateStock($id, $quantity, $operation)) {
            $updatedProduct = $this->product->getById($id);
            echo json_encode(['success' => true, 'data' => $updatedProduct]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update stock']);
        }
    }
    
    public function getFeatured() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $products = $this->product->getFeaturedProducts($limit);
        
        echo json_encode(['success' => true, 'data' => $products]);
    }
    
    public function searchBySKU($sku) {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $product = $this->product->searchBySKU($sku);
        
        if ($product) {
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    }
}

// Handle API requests
$controller = new ProductController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $controller->getById($_GET['id']);
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'low-stock':
                    $controller->getLowStock();
                    break;
                case 'featured':
                    $controller->getFeatured();
                    break;
                case 'search-sku':
                    if (isset($_GET['sku'])) {
                        $controller->searchBySKU($_GET['sku']);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'SKU is required']);
                    }
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
            if (isset($_GET['action']) && $_GET['action'] === 'update-stock') {
                $controller->updateStock($_GET['id']);
            } else {
                $controller->update($_GET['id']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Product ID is required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
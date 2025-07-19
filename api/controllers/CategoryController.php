<?php
/**
 * Category Controller
 */

require_once '../config/config.php';
require_once '../models/Category.php';
require_once '../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

class CategoryController {
    private $category;
    
    public function __construct() {
        $this->category = new Category();
    }
    
    public function getAll() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : ITEMS_PER_PAGE;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        
        $categories = $this->category->getAll($page, $limit, $search);
        $total = $this->category->getCount($search);
        
        echo json_encode([
            'success' => true,
            'data' => $categories,
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
        
        $category = $this->category->getById($id);
        
        if ($category) {
            echo json_encode(['success' => true, 'data' => $category]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Category not found']);
        }
    }
    
    public function getActive() {
        $categories = $this->category->getActive();
        echo json_encode(['success' => true, 'data' => $categories]);
    }
    
    public function create() {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        if (!isset($data['name']) || empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Category name is required']);
            return;
        }
        
        // Set defaults
        $data['description'] = $data['description'] ?? '';
        $data['image'] = $data['image'] ?? '';
        $data['status'] = $data['status'] ?? 'active';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        
        $categoryId = $this->category->create($data);
        
        if ($categoryId) {
            $newCategory = $this->category->getById($categoryId);
            echo json_encode(['success' => true, 'data' => $newCategory]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create category']);
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
        
        if ($this->category->update($id, $data)) {
            $updatedCategory = $this->category->getById($id);
            echo json_encode(['success' => true, 'data' => $updatedCategory]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update category']);
        }
    }
    
    public function delete($id) {
        if (!AuthMiddleware::requireRole('admin')) {
            return;
        }
        
        $result = $this->category->delete($id);
        
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400);
            echo json_encode(['error' => $result['error']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete category']);
        }
    }
    
    public function getWithProductCount() {
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $categories = $this->category->getCategoryWithProductCount();
        echo json_encode(['success' => true, 'data' => $categories]);
    }
    
    public function updateSortOrder($id) {
        if (!AuthMiddleware::requireRole('moderator')) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['sort_order'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Sort order is required']);
            return;
        }
        
        if ($this->category->updateSortOrder($id, $data['sort_order'])) {
            echo json_encode(['success' => true, 'message' => 'Sort order updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update sort order']);
        }
    }
}

// Handle API requests
$controller = new CategoryController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $controller->getById($_GET['id']);
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'active':
                    $controller->getActive();
                    break;
                case 'with-product-count':
                    $controller->getWithProductCount();
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
            if (isset($_GET['action']) && $_GET['action'] === 'sort-order') {
                $controller->updateSortOrder($_GET['id']);
            } else {
                $controller->update($_GET['id']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID is required']);
        }
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            $controller->delete($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID is required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
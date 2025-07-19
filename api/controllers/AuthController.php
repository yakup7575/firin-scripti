<?php
/**
 * Authentication Controller
 */

require_once '../config/config.php';
require_once '../models/User.php';
require_once '../utils/JWTAuth.php';

header('Content-Type: application/json');

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
        JWTAuth::init(JWT_SECRET);
    }
    
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }
        
        $user = $this->user->login($data['username'], $data['password']);
        
        if ($user) {
            $payload = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                'exp' => time() + JWT_EXPIRY
            ];
            
            $token = JWTAuth::encode($payload);
            
            echo json_encode([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'full_name' => $user['first_name'] . ' ' . $user['last_name']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }
    
    public function logout() {
        // In a more advanced implementation, you would invalidate the token
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    public function me() {
        require_once '../middleware/AuthMiddleware.php';
        
        if (AuthMiddleware::authenticate()) {
            $user = $GLOBALS['current_user'];
            echo json_encode(['success' => true, 'user' => $user]);
        }
    }
    
    public function changePassword() {
        require_once '../middleware/AuthMiddleware.php';
        
        if (!AuthMiddleware::authenticate()) {
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $GLOBALS['current_user'];
        
        if (!isset($data['old_password']) || !isset($data['new_password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Old password and new password are required']);
            return;
        }
        
        if (strlen($data['new_password']) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'New password must be at least 6 characters long']);
            return;
        }
        
        if ($this->user->changePassword($user['user_id'], $data['old_password'], $data['new_password'])) {
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Current password is incorrect']);
        }
    }
}

// Handle API requests
$controller = new AuthController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'login':
                    $controller->login();
                    break;
                case 'logout':
                    $controller->logout();
                    break;
                case 'change-password':
                    $controller->changePassword();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint not found']);
            }
        } else {
            $controller->login();
        }
        break;
        
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'me') {
            $controller->me();
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
?>
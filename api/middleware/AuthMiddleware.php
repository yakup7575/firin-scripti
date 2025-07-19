<?php
/**
 * Authentication Middleware
 */

require_once '../config/config.php';
require_once '../utils/JWTAuth.php';

class AuthMiddleware {
    
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            self::unauthorized('Token not provided');
            return false;
        }
        
        $token = $matches[1];
        JWTAuth::init(JWT_SECRET);
        
        $payload = JWTAuth::decode($token);
        
        if (!$payload) {
            self::unauthorized('Invalid or expired token');
            return false;
        }
        
        // Store user data in global variable for controllers to use
        $GLOBALS['current_user'] = $payload;
        
        return $payload;
    }
    
    public static function requireRole($requiredRole) {
        $user = self::authenticate();
        
        if (!$user) {
            return false;
        }
        
        $roleHierarchy = ['viewer' => 1, 'moderator' => 2, 'admin' => 3];
        $userLevel = $roleHierarchy[$user['role']] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
        
        if ($userLevel < $requiredLevel) {
            self::forbidden('Insufficient permissions');
            return false;
        }
        
        return true;
    }
    
    private static function unauthorized($message = 'Unauthorized') {
        http_response_code(401);
        echo json_encode(['error' => $message]);
        exit;
    }
    
    private static function forbidden($message = 'Forbidden') {
        http_response_code(403);
        echo json_encode(['error' => $message]);
        exit;
    }
}
?>
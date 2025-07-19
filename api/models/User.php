<?php
/**
 * User Model
 */

require_once '../config/database.php';

class User {
    private $conn;
    private $table = 'users';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function login($username, $password) {
        $query = "SELECT id, username, email, password, role, first_name, last_name, status 
                  FROM " . $this->table . " 
                  WHERE (username = :username OR email = :username) AND status = 'active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }
    
    public function getById($id) {
        $query = "SELECT id, username, email, role, first_name, last_name, status, created_at 
                  FROM " . $this->table . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT id, username, email, role, first_name, last_name, status, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, email, password, role, first_name, last_name) 
                  VALUES (:username, :email, :password, :role, :first_name, :last_name)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function update($id, $data) {
        $setClause = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            
            if ($key !== 'id') {
                $setClause[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($setClause)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $setClause) . ", updated_at = NOW() WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    public function changePassword($id, $oldPassword, $newPassword) {
        $user = $this->getById($id);
        
        if (!$user || !password_verify($oldPassword, $user['password'])) {
            return false;
        }
        
        return $this->update($id, ['password' => $newPassword]);
    }
    
    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        
        return $result['total'];
    }
}
?>
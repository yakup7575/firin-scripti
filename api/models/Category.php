<?php
/**
 * Category Model
 */

require_once '../config/database.php';

class Category {
    private $conn;
    private $table = 'categories';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll($page = 1, $limit = 10, $search = '') {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $query = "SELECT * FROM " . $this->table . " 
                  $whereClause 
                  ORDER BY sort_order ASC, created_at DESC 
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
    
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY sort_order ASC, name ASC";
        $stmt = $this->conn->query($query);
        
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, image, status, sort_order) 
                  VALUES (:name, :description, :image, :status, :sort_order)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':sort_order', $data['sort_order']);
        
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
        // Check if category has products
        $checkQuery = "SELECT COUNT(*) as count FROM products WHERE category_id = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch();
        
        if ($result['count'] > 0) {
            return ['error' => 'Cannot delete category that has products'];
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
            $whereClause .= " AND (name LIKE :search OR description LIKE :search)";
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
    
    public function getCategoryWithProductCount() {
        $query = "SELECT c.*, COUNT(p.id) as product_count 
                  FROM " . $this->table . " c 
                  LEFT JOIN products p ON c.id = p.category_id 
                  GROUP BY c.id 
                  ORDER BY c.sort_order ASC, c.name ASC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }
    
    public function updateSortOrder($id, $sortOrder) {
        $query = "UPDATE " . $this->table . " SET sort_order = :sort_order WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':sort_order', $sortOrder);
        
        return $stmt->execute();
    }
}
?>
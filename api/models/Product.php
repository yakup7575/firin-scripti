<?php
/**
 * Product Model
 */

require_once '../config/database.php';

class Product {
    private $conn;
    private $table = 'products';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAll($page = 1, $limit = 10, $search = '', $category_id = null) {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (p.name LIKE :search OR p.description LIKE :search OR p.sku LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if ($category_id) {
            $whereClause .= " AND p.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }
        
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  $whereClause 
                  ORDER BY p.created_at DESC 
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
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (category_id, name, description, price, old_price, stock, min_stock, image, sku, status, featured) 
                  VALUES (:category_id, :name, :description, :price, :old_price, :stock, :min_stock, :image, :sku, :status, :featured)";
        
        $stmt = $this->conn->prepare($query);
        
        // Generate SKU if not provided
        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSKU($data['name']);
        }
        
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':old_price', $data['old_price']);
        $stmt->bindParam(':stock', $data['stock']);
        $stmt->bindParam(':min_stock', $data['min_stock']);
        $stmt->bindParam(':image', $data['image']);
        $stmt->bindParam(':sku', $data['sku']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':featured', $data['featured'], PDO::PARAM_BOOL);
        
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
    
    public function getCount($search = '', $category_id = null) {
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE :search OR description LIKE :search OR sku LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if ($category_id) {
            $whereClause .= " AND category_id = :category_id";
            $params[':category_id'] = $category_id;
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
    
    public function getLowStockProducts() {
        $query = "SELECT * FROM " . $this->table . " WHERE stock <= min_stock AND status = 'active' ORDER BY stock ASC";
        $stmt = $this->conn->query($query);
        
        return $stmt->fetchAll();
    }
    
    public function updateStock($id, $quantity, $operation = 'add') {
        if ($operation === 'add') {
            $query = "UPDATE " . $this->table . " SET stock = stock + :quantity WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table . " SET stock = stock - :quantity WHERE id = :id AND stock >= :quantity";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }
    
    public function getFeaturedProducts($limit = 10) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.featured = 1 AND p.status = 'active' 
                  ORDER BY p.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    private function generateSKU($name) {
        $sku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 6));
        $sku .= '-' . time();
        
        return $sku;
    }
    
    public function searchBySKU($sku) {
        $query = "SELECT * FROM " . $this->table . " WHERE sku = :sku";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sku', $sku);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
?>
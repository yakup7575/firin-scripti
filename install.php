<?php
/**
 * Database Installation Script
 * Creates all required tables and inserts sample data
 */

require_once 'api/config/database.php';

class DatabaseInstaller {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function install() {
        try {
            $this->createTables();
            $this->insertSampleData();
            echo "<h2>Database installation completed successfully!</h2>";
            echo "<p><a href='admin/login.php'>Go to Admin Panel</a></p>";
        } catch (Exception $e) {
            echo "Installation failed: " . $e->getMessage();
        }
    }
    
    private function createTables() {
        $queries = [
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id INT PRIMARY KEY AUTO_INCREMENT,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'moderator', 'viewer') DEFAULT 'viewer',
                first_name VARCHAR(50),
                last_name VARCHAR(50),
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            // Categories table
            "CREATE TABLE IF NOT EXISTS categories (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                image VARCHAR(255),
                status ENUM('active', 'inactive') DEFAULT 'active',
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Products table
            "CREATE TABLE IF NOT EXISTS products (
                id INT PRIMARY KEY AUTO_INCREMENT,
                category_id INT,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                old_price DECIMAL(10,2),
                stock INT DEFAULT 0,
                min_stock INT DEFAULT 5,
                image VARCHAR(255),
                gallery TEXT,
                sku VARCHAR(50) UNIQUE,
                status ENUM('active', 'inactive') DEFAULT 'active',
                featured BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            )",
            
            // Customers table
            "CREATE TABLE IF NOT EXISTS customers (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE,
                phone VARCHAR(20),
                address TEXT,
                city VARCHAR(50),
                postal_code VARCHAR(10),
                birth_date DATE,
                gender ENUM('male', 'female', 'other'),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Orders table
            "CREATE TABLE IF NOT EXISTS orders (
                id INT PRIMARY KEY AUTO_INCREMENT,
                customer_id INT,
                order_number VARCHAR(20) UNIQUE NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                tax_amount DECIMAL(10,2) DEFAULT 0,
                discount_amount DECIMAL(10,2) DEFAULT 0,
                status ENUM('pending', 'preparing', 'ready', 'delivering', 'completed', 'cancelled') DEFAULT 'pending',
                payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
                payment_method ENUM('cash', 'card', 'online', 'bank_transfer') DEFAULT 'cash',
                order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                delivery_date DATETIME,
                delivery_address TEXT,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
            )",
            
            // Order items table
            "CREATE TABLE IF NOT EXISTS order_items (
                id INT PRIMARY KEY AUTO_INCREMENT,
                order_id INT NOT NULL,
                product_id INT,
                product_name VARCHAR(200) NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
            )",
            
            // Settings table
            "CREATE TABLE IF NOT EXISTS settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                description TEXT,
                category VARCHAR(50) DEFAULT 'general',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )",
            
            // Campaigns table
            "CREATE TABLE IF NOT EXISTS campaigns (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                discount_type ENUM('percentage', 'fixed') NOT NULL,
                discount_value DECIMAL(10,2) NOT NULL,
                min_order_amount DECIMAL(10,2) DEFAULT 0,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                status ENUM('active', 'inactive') DEFAULT 'active',
                usage_limit INT DEFAULT 0,
                usage_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // User sessions table
            "CREATE TABLE IF NOT EXISTS user_sessions (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                token VARCHAR(255) NOT NULL,
                expires_at DATETIME NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )",
            
            // Activity logs table
            "CREATE TABLE IF NOT EXISTS activity_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT,
                action VARCHAR(100) NOT NULL,
                table_name VARCHAR(50),
                record_id INT,
                old_data JSON,
                new_data JSON,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            )"
        ];
        
        foreach ($queries as $query) {
            $this->db->exec($query);
        }
    }
    
    private function insertSampleData() {
        // Insert default admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $this->db->exec("INSERT IGNORE INTO users (username, email, password, role, first_name, last_name) 
                        VALUES ('admin', 'admin@firin.com', '$adminPassword', 'admin', 'Admin', 'User')");
        
        // Insert default categories
        $categories = [
            ['Ekmekler', 'Taze günlük ekmek çeşitleri'],
            ['Pastalar', 'Özel günler için pastalar'],
            ['Börekler', 'Ev yapımı börek çeşitleri'],
            ['Tatlılar', 'Geleneksel Türk tatlıları'],
            ['Kurabiyeler', 'Çeşitli kurabiye türleri'],
            ['Sandviçler', 'Hazır sandviç seçenekleri']
        ];
        
        foreach ($categories as $category) {
            $this->db->exec("INSERT IGNORE INTO categories (name, description) VALUES ('{$category[0]}', '{$category[1]}')");
        }
        
        // Insert sample products
        $products = [
            [1, 'Beyaz Ekmek', 'Günlük taze beyaz ekmek', 3.50, 0, 50, 'ekmek1.jpg'],
            [1, 'Tam Buğday Ekmeği', 'Sağlıklı tam buğday ekmeği', 4.00, 0, 30, 'ekmek2.jpg'],
            [2, 'Doğum Günü Pastası', 'Özel tasarım doğum günü pastası', 150.00, 0, 5, 'pasta1.jpg'],
            [2, 'Çikolatalı Pasta', 'Çikolata severlere özel', 120.00, 0, 8, 'pasta2.jpg'],
            [3, 'Su Böreği', 'El açması su böreği', 45.00, 0, 10, 'borek1.jpg'],
            [4, 'Baklava', 'Antep fıstıklı baklava', 80.00, 0, 20, 'baklava.jpg']
        ];
        
        foreach ($products as $product) {
            $this->db->exec("INSERT IGNORE INTO products (category_id, name, description, price, old_price, stock, image) 
                            VALUES ({$product[0]}, '{$product[1]}', '{$product[2]}', {$product[3]}, {$product[4]}, {$product[5]}, '{$product[6]}')");
        }
        
        // Insert default settings
        $settings = [
            ['site_name', 'Fırın Pastane', 'Site adı'],
            ['site_email', 'info@firin.com', 'Site e-posta adresi'],
            ['site_phone', '+90 212 123 45 67', 'Site telefon numarası'],
            ['site_address', 'İstanbul, Türkiye', 'Site adresi'],
            ['currency', 'TRY', 'Para birimi'],
            ['tax_rate', '18', 'KDV oranı (%)'],
            ['min_order_amount', '25', 'Minimum sipariş tutarı']
        ];
        
        foreach ($settings as $setting) {
            $this->db->exec("INSERT IGNORE INTO settings (setting_key, setting_value, description) 
                            VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}')");
        }
    }
}

// Run installation if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) == 'install.php') {
    $installer = new DatabaseInstaller();
    $installer->install();
}
?>
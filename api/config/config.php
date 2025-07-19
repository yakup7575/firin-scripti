<?php
/**
 * General Configuration
 */

// Application settings
define('APP_NAME', 'Fırın Pastane Yönetim Sistemi');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');

// Security settings
define('JWT_SECRET', 'your-secret-key-here-change-in-production');
define('JWT_EXPIRY', 3600); // 1 hour
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@firin.com');
define('FROM_NAME', 'Fırın Pastane');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Currency
define('CURRENCY_SYMBOL', '₺');
define('CURRENCY_CODE', 'TRY');

// Time zone
date_default_timezone_set('Europe/Istanbul');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
?>
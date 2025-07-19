<?php
/**
 * File Upload Utility
 */

class FileUploader {
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    
    public function __construct($uploadDir = '../uploads/', $allowedTypes = null, $maxFileSize = null) {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->allowedTypes = $allowedTypes ?: ALLOWED_IMAGE_TYPES;
        $this->maxFileSize = $maxFileSize ?: UPLOAD_MAX_SIZE;
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function upload($file, $prefix = '') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('No file uploaded');
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception('File size too large. Maximum allowed: ' . $this->formatBytes($this->maxFileSize));
        }
        
        // Get file extension
        $pathInfo = pathinfo($file['name']);
        $extension = strtolower($pathInfo['extension']);
        
        // Check file type
        if (!in_array($extension, $this->allowedTypes)) {
            throw new Exception('File type not allowed. Allowed types: ' . implode(', ', $this->allowedTypes));
        }
        
        // Validate image file
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $imageInfo = getimagesize($file['tmp_name']);
            if (!$imageInfo) {
                throw new Exception('Invalid image file');
            }
        }
        
        // Generate unique filename
        $filename = $this->generateFilename($pathInfo['filename'], $extension, $prefix);
        $filepath = $this->uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to upload file');
        }
        
        // Optimize image if it's an image file
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $this->optimizeImage($filepath, $extension);
        }
        
        return $filename;
    }
    
    public function uploadMultiple($files, $prefix = '') {
        $uploadedFiles = [];
        
        for ($i = 0; $i < count($files['name']); $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            if ($file['error'] === UPLOAD_ERR_OK) {
                $uploadedFiles[] = $this->upload($file, $prefix);
            }
        }
        
        return $uploadedFiles;
    }
    
    public function delete($filename) {
        $filepath = $this->uploadDir . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    private function generateFilename($originalName, $extension, $prefix = '') {
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', $originalName);
        $cleanName = substr($cleanName, 0, 20);
        
        $timestamp = time();
        $random = substr(md5(mt_rand()), 0, 8);
        
        return $prefix . $cleanName . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
    
    private function optimizeImage($filepath, $extension) {
        $maxWidth = 1200;
        $maxHeight = 1200;
        $quality = 85;
        
        list($width, $height) = getimagesize($filepath);
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        
        if ($ratio < 1) {
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
            
            // Create new image
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Load original image
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $source = imagecreatefromjpeg($filepath);
                    break;
                case 'png':
                    $source = imagecreatefrompng($filepath);
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($filepath);
                    break;
                default:
                    return;
            }
            
            // Resize image
            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            
            // Save optimized image
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($newImage, $filepath, $quality);
                    break;
                case 'png':
                    imagepng($newImage, $filepath, 9);
                    break;
                case 'gif':
                    imagegif($newImage, $filepath);
                    break;
            }
            
            // Clean up memory
            imagedestroy($source);
            imagedestroy($newImage);
        }
    }
    
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    public function getUploadDir() {
        return $this->uploadDir;
    }
    
    public function getAllowedTypes() {
        return $this->allowedTypes;
    }
    
    public function getMaxFileSize() {
        return $this->maxFileSize;
    }
}
?>
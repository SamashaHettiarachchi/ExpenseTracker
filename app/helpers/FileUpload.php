<?php
/**
 * File Upload Helper Class
 * Handles image and file uploads with validation
 */

class FileUpload {
    
    /**
     * Upload file to specified directory
     * @param array $file - $_FILES array element
     * @param string $uploadDir - Directory name (profiles/receipts)
     * @return array - ['success' => bool, 'filename' => string, 'error' => string]
     */
    public static function upload($file, $uploadDir = 'receipts') {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'No file uploaded'];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => self::getUploadError($file['error'])];
        }
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'error' => 'File size exceeds maximum allowed (5MB)'];
        }
        
        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ALLOWED_EXTENSIONS)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS)];
        }
        
        // Validate MIME type
        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedMimes)) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }
        
        // Generate unique filename
        $filename = self::generateFilename($extension);
        
        // Set upload path
        $uploadPath = UPLOAD_PATH . $uploadDir . '/';
        
        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $destination = $uploadPath . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Failed to move uploaded file'];
        }
    }
    
    /**
     * Generate unique filename
     * @param string $extension
     * @return string
     */
    private static function generateFilename($extension) {
        return uniqid('img_', true) . '_' . time() . '.' . $extension;
    }
    
    /**
     * Delete uploaded file
     * @param string $filename
     * @param string $uploadDir
     * @return bool
     */
    public static function delete($filename, $uploadDir = 'receipts') {
        if (empty($filename) || $filename === 'default-avatar.png') {
            return false;
        }
        
        $filePath = UPLOAD_PATH . $uploadDir . '/' . $filename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Get upload error message
     * @param int $errorCode
     * @return string
     */
    private static function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION  => 'Upload stopped by extension'
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }
    
    /**
     * Get file URL
     * @param string $filename
     * @param string $uploadDir
     * @return string
     */
    public static function getUrl($filename, $uploadDir = 'receipts') {
        if (empty($filename)) {
            return $uploadDir === 'profiles' 
                ? APP_URL . '/images/default-avatar.png'
                : APP_URL . '/images/no-image.png';
        }
        
        return APP_URL . '/uploads/' . $uploadDir . '/' . $filename;
    }
}

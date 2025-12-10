<?php
/**
 * API Response Helper Class
 * Standardizes JSON responses for API endpoints
 */

class Response {
    
    /**
     * Send success response
     * @param mixed $data - Data to return
     * @param string $message - Success message
     * @param int $statusCode - HTTP status code
     */
    public static function success($data = null, $message = 'Success', $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], JSON_PRETTY_PRINT);
        
        exit;
    }
    
    /**
     * Send error response
     * @param string $message - Error message
     * @param int $statusCode - HTTP status code
     * @param array $errors - Validation errors
     */
    public static function error($message = 'Error', $statusCode = 400, $errors = []) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        
        exit;
    }
    
    /**
     * Send validation error response
     * @param array $errors - Array of validation errors
     */
    public static function validationError($errors) {
        self::error('Validation failed', 422, $errors);
    }
    
    /**
     * Send unauthorized response
     * @param string $message
     */
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    /**
     * Send forbidden response
     * @param string $message
     */
    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
    
    /**
     * Send not found response
     * @param string $message
     */
    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }
    
    /**
     * Send server error response
     * @param string $message
     */
    public static function serverError($message = 'Internal server error') {
        self::error($message, 500);
    }
}

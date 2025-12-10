<?php
/**
 * Authentication Helper Class
 * Handles user sessions, login checks, and user data management
 */

class Auth {
    
    /**
     * Start session if not already started
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Login user by setting session
     * @param array $user - User data from database
     * @param bool $remember - Whether to remember user
     */
    public static function login($user, $remember = false) {
        self::init();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_profile_pic'] = $user['profile_pic'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Remember me functionality
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + REMEMBER_ME_LIFETIME, '/');
            // Store token in database (implement in User model)
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public static function check() {
        self::init();
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time'])) {
            $elapsed = time() - $_SESSION['login_time'];
            if ($elapsed > SESSION_LIFETIME) {
                self::logout();
                return false;
            }
            // Update last activity time
            $_SESSION['login_time'] = time();
        }
        
        return true;
    }
    
    /**
     * Get logged in user data
     * @return array|null
     */
    public static function user() {
        self::init();
        
        if (!self::check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
            'profile_pic' => $_SESSION['user_profile_pic'] ?? 'default-avatar.png'
        ];
    }
    
    /**
     * Get logged in user ID
     * @return int|null
     */
    public static function id() {
        $user = self::user();
        return $user['id'] ?? null;
    }
    
    /**
     * Check if user is admin
     * @return bool
     */
    public static function isAdmin() {
        $user = self::user();
        return isset($user['role']) && $user['role'] === 'admin';
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::init();
        
        // Destroy session
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Remove remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    /**
     * Redirect to login if not authenticated
     */
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: ' . APP_URL . '/index.php');
            exit;
        }
    }
    
    /**
     * Redirect to dashboard if already authenticated
     */
    public static function requireGuest() {
        if (self::check()) {
            header('Location: ' . APP_URL . '/dashboard.php');
            exit;
        }
    }
    
    /**
     * Require admin role
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: ' . APP_URL . '/dashboard.php');
            exit;
        }
    }
}

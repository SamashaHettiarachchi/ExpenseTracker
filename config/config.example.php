<?php
/**
 * Database Configuration
 * Copy this file to config.php and update with your credentials
 */

// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'expense_tracker');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Expense Tracker');
define('APP_URL', 'http://localhost/seee/public');
define('APP_TIMEZONE', 'Asia/Kolkata');

// File upload settings
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('REMEMBER_ME_LIFETIME', 2592000); // 30 days in seconds

// Pagination
define('ITEMS_PER_PAGE', 10);

// Security
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 10);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

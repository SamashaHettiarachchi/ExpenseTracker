<?php
/**
 * Logout API Endpoint
 * POST: /api/logout.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';

// Logout user
Auth::logout();

// Redirect to login page
header('Location: ' . APP_URL . '/index.php');
exit;

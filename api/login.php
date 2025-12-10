<?php
/**
 * Login API Endpoint
 * POST: /api/login.php
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/User.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['email']) || !isset($input['password'])) {
    Response::validationError([
        'email' => 'Email is required',
        'password' => 'Password is required'
    ]);
}

// Attempt login
$userModel = new User();
$result = $userModel->login($input['email'], $input['password']);

if (!$result['success']) {
    Response::error($result['message'], 401);
}

// Set session
$remember = isset($input['remember']) && $input['remember'] === true;
Auth::login($result['user'], $remember);

// Return success
Response::success([
    'user' => $result['user'],
    'redirect' => APP_URL . '/dashboard.php'
], 'Login successful');

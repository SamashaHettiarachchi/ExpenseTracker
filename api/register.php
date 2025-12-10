<?php
/**
 * Register API Endpoint
 * POST: /api/register.php
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
$errors = [];

if (empty($input['name'])) {
    $errors['name'] = 'Name is required';
}

if (empty($input['email'])) {
    $errors['email'] = 'Email is required';
}

if (empty($input['password'])) {
    $errors['password'] = 'Password is required';
}

if (!empty($input['password']) && strlen($input['password']) < 6) {
    $errors['password'] = 'Password must be at least 6 characters';
}

if (isset($input['confirm_password']) && $input['password'] !== $input['confirm_password']) {
    $errors['confirm_password'] = 'Passwords do not match';
}

if (!empty($errors)) {
    Response::validationError($errors);
}

// Attempt registration
$userModel = new User();
$result = $userModel->register([
    'name' => $input['name'],
    'email' => $input['email'],
    'password' => $input['password'],
    'role' => 'user' // Default role
]);

if (!$result['success']) {
    if (isset($result['errors'])) {
        Response::validationError($result['errors']);
    }
    Response::error($result['message'], 400);
}

// Auto-login after registration
$loginResult = $userModel->login($input['email'], $input['password']);

if ($loginResult['success']) {
    Auth::login($loginResult['user']);
}

// Return success
Response::success([
    'user_id' => $result['user_id'],
    'redirect' => APP_URL . '/dashboard.php'
], 'Registration successful', 201);

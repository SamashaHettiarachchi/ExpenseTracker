<?php
/**
 * Statistics API Endpoint
 * GET: Get dashboard statistics
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/Expense.php';

// Check authentication
Auth::init();
if (!Auth::check()) {
    Response::unauthorized('Please login to continue');
}

$expenseModel = new Expense();
$userId = Auth::id();
$isAdmin = Auth::isAdmin();

// Allow admin to view other user's stats
if ($isAdmin && isset($_GET['user_id'])) {
    $userId = (int)$_GET['user_id'];
}

// Get statistics
$stats = $expenseModel->getStats($userId);

Response::success($stats);

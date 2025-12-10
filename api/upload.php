<?php
/**
 * Upload API Endpoint
 * POST: Upload receipt or profile image
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/helpers/FileUpload.php';

// Check authentication
Auth::init();
if (!Auth::check()) {
    Response::unauthorized('Please login to continue');
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed', 405);
}

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    Response::error('No file uploaded', 400);
}

// Get upload directory (receipts or profiles)
$uploadDir = $_POST['type'] ?? 'receipts';

if (!in_array($uploadDir, ['receipts', 'profiles'])) {
    Response::error('Invalid upload type', 400);
}

// Upload file
$result = FileUpload::upload($_FILES['file'], $uploadDir);

if (!$result['success']) {
    Response::error($result['error'], 400);
}

// Return success with file info
Response::success([
    'filename' => $result['filename'],
    'url' => FileUpload::getUrl($result['filename'], $uploadDir),
    'type' => $uploadDir
], 'File uploaded successfully');

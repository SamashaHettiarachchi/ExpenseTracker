<?php
/**
 * Profile API Endpoint
 * GET: Get user profile
 * PUT: Update user profile
 * POST: Update profile picture
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/helpers/FileUpload.php';
require_once __DIR__ . '/../app/models/User.php';

// Check authentication
Auth::init();
if (!Auth::check()) {
    Response::unauthorized('Please login to continue');
}

$userModel = new User();
$userId = Auth::id();
$method = $_SERVER['REQUEST_METHOD'];

// GET - Get profile
if ($method === 'GET') {
    $user = $userModel->getById($userId);
    
    if (!$user) {
        Response::notFound('User not found');
    }
    
    Response::success($user);
}

// PUT - Update profile
if ($method === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    
    $result = $userModel->updateProfile($userId, $input);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    // Update session data
    $updatedUser = $userModel->getById($userId);
    Auth::login($updatedUser);
    
    Response::success(null, $result['message']);
}

// POST - Update profile picture or password
if ($method === 'POST') {
    // Handle profile picture upload
    if (isset($_FILES['profile_pic'])) {
        $uploadResult = FileUpload::upload($_FILES['profile_pic'], 'profiles');
        
        if (!$uploadResult['success']) {
            Response::error($uploadResult['error'], 400);
        }
        
        // Delete old profile picture
        $currentUser = $userModel->getById($userId);
        if ($currentUser['profile_pic'] !== 'default-avatar.png') {
            FileUpload::delete($currentUser['profile_pic'], 'profiles');
        }
        
        // Update profile picture in database
        $result = $userModel->updateProfile($userId, [
            'profile_pic' => $uploadResult['filename']
        ]);
        
        if (!$result['success']) {
            Response::error($result['message'], 400);
        }
        
        // Update session
        $updatedUser = $userModel->getById($userId);
        Auth::login($updatedUser);
        
        Response::success([
            'profile_pic' => $uploadResult['filename'],
            'url' => FileUpload::getUrl($uploadResult['filename'], 'profiles')
        ], 'Profile picture updated successfully');
    }
    
    // Handle password update
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['current_password']) && isset($input['new_password'])) {
        $result = $userModel->updatePassword(
            $userId,
            $input['current_password'],
            $input['new_password']
        );
        
        if (!$result['success']) {
            Response::error($result['message'], 400);
        }
        
        Response::success(null, $result['message']);
    }
    
    Response::error('Invalid request', 400);
}

Response::error('Method not allowed', 405);

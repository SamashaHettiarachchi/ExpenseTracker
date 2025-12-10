<?php
/**
 * Categories API Endpoint
 * GET: List all categories
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/Category.php';

// Check authentication
Auth::init();
if (!Auth::check()) {
    Response::unauthorized('Please login to continue');
}

$categoryModel = new Category();
$method = $_SERVER['REQUEST_METHOD'];

// GET - List all categories
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $category = $categoryModel->getById($_GET['id']);
        
        if (!$category) {
            Response::notFound('Category not found');
        }
        
        Response::success($category);
    }
    
    $categories = $categoryModel->getAll();
    Response::success($categories);
}

// POST - Create category (admin only)
if ($method === 'POST') {
    Auth::requireAdmin();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['name'])) {
        Response::validationError(['name' => 'Category name is required']);
    }
    
    $result = $categoryModel->create($input);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    Response::success([
        'category_id' => $result['category_id']
    ], $result['message'], 201);
}

// PUT - Update category (admin only)
if ($method === 'PUT') {
    Auth::requireAdmin();
    
    if (!isset($_GET['id'])) {
        Response::error('Category ID is required', 400);
    }
    
    parse_str(file_get_contents('php://input'), $input);
    
    $result = $categoryModel->update($_GET['id'], $input);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    Response::success(null, $result['message']);
}

// DELETE - Delete category (admin only)
if ($method === 'DELETE') {
    Auth::requireAdmin();
    
    if (!isset($_GET['id'])) {
        Response::error('Category ID is required', 400);
    }
    
    $result = $categoryModel->delete($_GET['id']);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    Response::success(null, $result['message']);
}

Response::error('Method not allowed', 405);

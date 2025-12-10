<?php
/**
 * Expenses API Endpoint
 * GET: List expenses with filters
 * POST: Create new expense
 * PUT: Update expense
 * DELETE: Delete expense
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Response.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/helpers/FileUpload.php';
require_once __DIR__ . '/../app/models/Expense.php';

// Check authentication
Auth::init();
if (!Auth::check()) {
    Response::unauthorized('Please login to continue');
}

$expenseModel = new Expense();
$userId = Auth::id();
$isAdmin = Auth::isAdmin();

$method = $_SERVER['REQUEST_METHOD'];

// GET - List expenses
if ($method === 'GET') {
    $filters = [
        'category_id' => !empty($_GET['category_id']) ? $_GET['category_id'] : null,
        'status' => !empty($_GET['status']) ? $_GET['status'] : null,
        'payment_method' => !empty($_GET['payment_method']) ? $_GET['payment_method'] : null,
        'date_from' => !empty($_GET['date_from']) ? $_GET['date_from'] : null,
        'date_to' => !empty($_GET['date_to']) ? $_GET['date_to'] : null,
        'search' => !empty($_GET['search']) ? $_GET['search'] : null,
        'order_by' => $_GET['order_by'] ?? 'expense_date',
        'order_dir' => $_GET['order_dir'] ?? 'DESC',
        'limit' => isset($_GET['limit']) ? (int)$_GET['limit'] : ITEMS_PER_PAGE,
        'offset' => isset($_GET['offset']) ? (int)$_GET['offset'] : 0
    ];
    
    // Filter by user_id: Admin sees all, regular users see only their own
    if (!$isAdmin) {
        $filters['user_id'] = $userId;
    } elseif (isset($_GET['user_id'])) {
        // Admin can filter by specific user if provided
        $filters['user_id'] = $_GET['user_id'];
    }
    // If admin and no user_id provided, don't add user_id filter (show all)
    
    // Remove null values
    $filters = array_filter($filters, function($value) {
        return $value !== null;
    });
    
    $expenses = $expenseModel->getAll($filters);
    $total = $expenseModel->getTotalCount($filters);
    
    Response::success([
        'expenses' => $expenses,
        'total' => $total,
        'page' => isset($_GET['offset']) ? floor($_GET['offset'] / ITEMS_PER_PAGE) + 1 : 1,
        'per_page' => $filters['limit']
    ]);
}

// POST - Create expense
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Handle file upload if present
    $receiptImage = null;
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = FileUpload::upload($_FILES['receipt_image'], 'receipts');
        
        if (!$uploadResult['success']) {
            Response::error($uploadResult['error'], 400);
        }
        
        $receiptImage = $uploadResult['filename'];
    }
    
    // If JSON input, use it; otherwise use POST data
    if ($input === null) {
        $input = $_POST;
    }
    
    $data = [
        'user_id' => $userId,
        'title' => $input['title'] ?? null,
        'amount' => $input['amount'] ?? null,
        'category_id' => $input['category_id'] ?? null,
        'description' => $input['description'] ?? null,
        'expense_date' => $input['expense_date'] ?? date('Y-m-d'),
        'payment_method' => $input['payment_method'] ?? 'cash',
        'receipt_image' => $receiptImage,
        'status' => $isAdmin ? ($input['status'] ?? 'approved') : 'approved'
    ];
    
    $result = $expenseModel->create($data);
    
    if (!$result['success']) {
        if (isset($result['errors'])) {
            Response::validationError($result['errors']);
        }
        Response::error($result['message'], 400);
    }
    
    Response::success([
        'expense_id' => $result['expense_id']
    ], $result['message'], 201);
}

// PUT - Update expense
if ($method === 'PUT') {
    parse_str(file_get_contents('php://input'), $input);
    
    if (!isset($_GET['id'])) {
        Response::error('Expense ID is required', 400);
    }
    
    $expenseId = (int)$_GET['id'];
    
    // Admin can update any expense, users can only update their own
    $result = $expenseModel->update($expenseId, $input, $isAdmin ? null : $userId);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    Response::success(null, $result['message']);
}

// DELETE - Delete expense
if ($method === 'DELETE') {
    if (!isset($_GET['id'])) {
        Response::error('Expense ID is required', 400);
    }
    
    $expenseId = (int)$_GET['id'];
    
    // Get expense to delete receipt image
    $expense = $expenseModel->getById($expenseId, $isAdmin ? null : $userId);
    
    if (!$expense) {
        Response::notFound('Expense not found');
    }
    
    // Delete expense
    $result = $expenseModel->delete($expenseId, $isAdmin ? null : $userId);
    
    if (!$result['success']) {
        Response::error($result['message'], 400);
    }
    
    // Delete receipt image if exists
    if (!empty($expense['receipt_image'])) {
        FileUpload::delete($expense['receipt_image'], 'receipts');
    }
    
    Response::success(null, $result['message']);
}

Response::error('Method not allowed', 405);

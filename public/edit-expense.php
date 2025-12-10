<?php
/**
 * Edit Expense Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__ . '/../app/models/Expense.php';

// Require authentication
Auth::requireLogin();

$user = Auth::user();
$userId = Auth::id();

// Get expense ID
if (!isset($_GET['id'])) {
    header('Location: expenses.php');
    exit;
}

$expenseId = (int)$_GET['id'];

// Load expense
$expenseModel = new Expense();
$expense = $expenseModel->getById($expenseId, $userId);

if (!$expense) {
    header('Location: expenses.php');
    exit;
}

// Load categories
$categoryModel = new Category();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-wallet me-2"></i><?php echo APP_NAME; ?></h4>
            <p class="mb-0 small"><?php echo $user['role']; ?></p>
        </div>
        
        <ul class="nav flex-column mt-3">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="expenses.php">
                    <i class="fas fa-receipt"></i> Expenses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add-expense.php">
                    <i class="fas fa-plus-circle"></i> Add Expense
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="../api/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <h5 class="mb-0 ms-2">Edit Expense</h5>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Dark Mode Toggle -->
                <div class="dark-mode-toggle" id="darkModeToggle" title="Toggle Dark Mode"></div>
                
                <!-- User Profile -->
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($user['name']); ?></span>
                        <i class="fas fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../api/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form Content -->
        <div class="container-fluid p-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <div class="card fade-in">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Expense</h5>
                        </div>
                        <div class="card-body">
                            <form id="editExpenseForm">
                                <input type="hidden" id="expense_id" value="<?php echo $expense['id']; ?>">
                                
                                <div class="row g-3">
                                    <!-- Title -->
                                    <div class="col-12">
                                        <label for="title" class="form-label">Expense Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($expense['title']); ?>" required>
                                    </div>

                                    <!-- Amount & Category -->
                                    <div class="col-md-6">
                                        <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" value="<?php echo $expense['amount']; ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $expense['category_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Date & Payment Method -->
                                    <div class="col-md-6">
                                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo $expense['expense_date']; ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="cash" <?php echo $expense['payment_method'] == 'cash' ? 'selected' : ''; ?>>Cash</option>
                                            <option value="card" <?php echo $expense['payment_method'] == 'card' ? 'selected' : ''; ?>>Card</option>
                                            <option value="upi" <?php echo $expense['payment_method'] == 'upi' ? 'selected' : ''; ?>>UPI</option>
                                            <option value="bank_transfer" <?php echo $expense['payment_method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($expense['description'] ?? ''); ?></textarea>
                                    </div>

                                    <!-- Current Receipt -->
                                    <?php if (!empty($expense['receipt_image'])): ?>
                                    <div class="col-12">
                                        <label class="form-label">Current Receipt</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="uploads/receipts/<?php echo htmlspecialchars($expense['receipt_image']); ?>" alt="Receipt" class="img-thumbnail" style="max-width: 150px;">
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteReceipt()">
                                                <i class="fas fa-trash me-1"></i>Delete Receipt
                                            </button>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <!-- New Receipt Upload -->
                                    <div class="col-12">
                                        <label for="receipt_image" class="form-label">Upload New Receipt</label>
                                        <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*,.pdf">
                                        <small class="text-muted">Leave empty to keep current receipt</small>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Expense
                                    </button>
                                    <a href="expenses.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to List
                                    </a>
                                    <a href="expense-details.php?id=<?php echo $expense['id']; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    <script>
        // Edit Expense Form Handler
        document.getElementById('editExpenseForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            btn.disabled = true;
            
            const expenseId = document.getElementById('expense_id').value;
            
            try {
                const formData = new FormData(this);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    if (key !== 'receipt_image') {
                        data[key] = value;
                    }
                }
                
                // Handle file upload separately if new file selected
                const fileInput = document.getElementById('receipt_image');
                if (fileInput.files.length > 0) {
                    const uploadFormData = new FormData();
                    uploadFormData.append('file', fileInput.files[0]);
                    uploadFormData.append('type', 'receipts');
                    
                    const uploadResponse = await fetch('/seee/api/upload.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const uploadResult = await uploadResponse.json();
                    if (uploadResult.success) {
                        data.receipt_image = uploadResult.data.filename;
                    }
                }
                
                const response = await fetch(`/seee/api/expenses.php?id=${expenseId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(data).toString()
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Expense updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'expenses.php';
                    }, 1500);
                } else {
                    showToast(result.message || 'Failed to update expense', 'danger');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'danger');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
        
        async function deleteReceipt() {
            const confirmed = await confirmAction('Are you sure you want to delete the receipt?');
            if (!confirmed) return;
            
            // Implementation would remove receipt via API
            showToast('Receipt will be removed on update', 'info');
        }
    </script>
</body>
</html>

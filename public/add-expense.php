<?php
/**
 * Add Expense Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/Category.php';

// Require authentication
Auth::requireLogin();

$user = Auth::user();

// Load categories
$categoryModel = new Category();
$categories = $categoryModel->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - <?php echo APP_NAME; ?></title>
    
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
                <a class="nav-link active" href="add-expense.php">
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
                <h5 class="mb-0 ms-2">Add New Expense</h5>
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
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Expense</h5>
                        </div>
                        <div class="card-body">
                            <form id="addExpenseForm" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <!-- Title -->
                                    <div class="col-12">
                                        <label for="title" class="form-label">Expense Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Lunch at Restaurant" required>
                                    </div>

                                    <!-- Amount & Category -->
                                    <div class="col-md-6">
                                        <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" placeholder="0.00" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Date & Payment Method -->
                                    <div class="col-md-6">
                                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="upi">UPI</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any additional notes..."></textarea>
                                    </div>

                                    <!-- Receipt Upload -->
                                    <div class="col-12">
                                        <label for="receipt_image" class="form-label">Receipt Image</label>
                                        <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*,.pdf">
                                        <small class="text-muted">Supported formats: JPG, PNG, GIF, PDF (Max 5MB)</small>
                                        
                                        <!-- Preview -->
                                        <div id="receiptPreview" class="mt-3" style="display: none;">
                                            <img id="previewImage" src="" alt="Receipt Preview" class="img-thumbnail" style="max-width: 200px;">
                                            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeReceipt()">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Expense
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                    <a href="expenses.php" class="btn btn-outline-danger">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="card mt-4 fade-in">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>Quick Tips</h6>
                            <ul class="mb-0 small text-muted">
                                <li>Enter accurate amounts to track your spending effectively</li>
                                <li>Choose the right category for better expense analysis</li>
                                <li>Upload receipts for record keeping and tax purposes</li>
                                <li>Add descriptions to remember the context later</li>
                            </ul>
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
        // Add Expense Form Handler
        document.getElementById('addExpenseForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            btn.disabled = true;
            
            try {
                const formData = new FormData(this);
                
                const response = await fetch('/seee/api/expenses.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Expense added successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'expenses.php';
                    }, 1500);
                } else {
                    showToast(result.message || 'Failed to add expense', 'danger');
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
        
        // Receipt Image Preview
        document.getElementById('receipt_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImage').src = e.target.result;
                        document.getElementById('receiptPreview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('receiptPreview').style.display = 'none';
                    showToast('Receipt uploaded (PDF cannot be previewed)', 'info');
                }
            }
        });
        
        function removeReceipt() {
            document.getElementById('receipt_image').value = '';
            document.getElementById('receiptPreview').style.display = 'none';
        }
        
        function resetForm() {
            document.getElementById('addExpenseForm').reset();
            document.getElementById('receiptPreview').style.display = 'none';
            document.getElementById('expense_date').value = '<?php echo date('Y-m-d'); ?>';
        }
    </script>
</body>
</html>

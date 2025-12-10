<?php
/**
 * Expense Details Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Details - <?php echo APP_NAME; ?></title>
    
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
                <h5 class="mb-0 ms-2">Expense Details</h5>
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

        <!-- Details Content -->
        <div class="container-fluid p-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    <!-- Back Button -->
                    <div class="mb-3">
                        <a href="expenses.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Expenses
                        </a>
                    </div>

                    <!-- Main Details Card -->
                    <div class="card fade-in">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i><?php echo htmlspecialchars($expense['title']); ?></h5>
                            <span class="badge bg-<?php 
                                echo $expense['status'] === 'approved' ? 'success' : 
                                    ($expense['status'] === 'pending' ? 'warning' : 'danger'); 
                            ?> fs-6">
                                <?php echo ucfirst($expense['status']); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Amount -->
                                <div class="col-12">
                                    <div class="text-center p-4 bg-light rounded">
                                        <h2 class="text-primary mb-0">₹<?php echo number_format($expense['amount'], 2); ?></h2>
                                        <p class="text-muted mb-0">Expense Amount</p>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="col-md-6">
                                    <div class="border-start border-4 border-primary ps-3">
                                        <label class="text-muted small">Category</label>
                                        <p class="mb-0">
                                            <span class="badge category-badge fs-6" style="background-color: <?php echo $expense['category_color']; ?>">
                                                <i class="fas <?php echo $expense['category_icon']; ?> me-1"></i>
                                                <?php echo htmlspecialchars($expense['category_name']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border-start border-4 border-success ps-3">
                                        <label class="text-muted small">Payment Method</label>
                                        <p class="mb-0 fw-bold">
                                            <i class="fas fa-<?php 
                                                echo $expense['payment_method'] === 'cash' ? 'money-bill-wave' : 
                                                    ($expense['payment_method'] === 'card' ? 'credit-card' : 
                                                    ($expense['payment_method'] === 'upi' ? 'mobile-alt' : 'university')); 
                                            ?> me-2"></i>
                                            <?php echo ucwords(str_replace('_', ' ', $expense['payment_method'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border-start border-4 border-warning ps-3">
                                        <label class="text-muted small">Expense Date</label>
                                        <p class="mb-0 fw-bold">
                                            <i class="fas fa-calendar me-2"></i>
                                            <?php echo date('F d, Y', strtotime($expense['expense_date'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border-start border-4 border-info ps-3">
                                        <label class="text-muted small">Created At</label>
                                        <p class="mb-0 fw-bold">
                                            <i class="fas fa-clock me-2"></i>
                                            <?php echo date('M d, Y H:i', strtotime($expense['created_at'])); ?>
                                        </p>
                                    </div>
                                </div>

                                <?php if (!empty($expense['description'])): ?>
                                <div class="col-12">
                                    <div class="border-start border-4 border-secondary ps-3">
                                        <label class="text-muted small">Description</label>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($expense['description'])); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Receipt Image -->
                                <?php if (!empty($expense['receipt_image'])): ?>
                                <div class="col-12">
                                    <label class="text-muted small mb-2">Receipt</label>
                                    <div class="text-center">
                                        <img src="uploads/receipts/<?php echo htmlspecialchars($expense['receipt_image']); ?>" 
                                             alt="Receipt" 
                                             class="receipt-image img-fluid rounded shadow-sm" 
                                             style="max-height: 400px; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#receiptModal">
                                        <p class="text-muted small mt-2">Click to view full size</p>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="col-12">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="edit-expense.php?id=<?php echo $expense['id']; ?>" class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Edit Expense
                                        </a>
                                        <button class="btn btn-danger" onclick="deleteExpense(<?php echo $expense['id']; ?>)">
                                            <i class="fas fa-trash me-2"></i>Delete Expense
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="window.print()">
                                            <i class="fas fa-print me-2"></i>Print
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info Card -->
                    <div class="card mt-4 fade-in">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="fas fa-info-circle text-info me-2"></i>Additional Information</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted">Added By</small>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($expense['user_name']); ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-0 fw-bold"><?php echo date('M d, Y H:i', strtotime($expense['updated_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <?php if (!empty($expense['receipt_image'])): ?>
    <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receipt Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="uploads/receipts/<?php echo htmlspecialchars($expense['receipt_image']); ?>" 
                         alt="Receipt" 
                         class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    <script>
        async function deleteExpense(id) {
            const confirmed = await confirmAction('Are you sure you want to delete this expense? This action cannot be undone.');
            if (!confirmed) return;
            
            showLoading();
            
            try {
                const response = await fetch(`/seee/api/expenses.php?id=${id}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Expense deleted successfully', 'success');
                    setTimeout(() => {
                        window.location.href = 'expenses.php';
                    }, 1500);
                } else {
                    showToast(result.message, 'danger');
                    hideLoading();
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred while deleting expense', 'danger');
                hideLoading();
            }
        }
    </script>
</body>
</html>

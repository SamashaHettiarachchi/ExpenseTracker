<?php
/**
 * Expenses List Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';

// Require authentication
Auth::requireLogin();

$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses - <?php echo APP_NAME; ?></title>
    
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
                <a class="nav-link active" href="expenses.php">
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
                <h5 class="mb-0 ms-2">My Expenses</h5>
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

        <!-- Expenses Content -->
        <div class="container-fluid p-4">
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="row g-3 align-items-end">
                    <!-- Search -->
                    <div class="col-12 col-md-4">
                        <label for="searchExpense" class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchExpense" placeholder="Search expenses...">
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="col-6 col-md-2">
                        <label for="filterCategory" class="form-label">Category</label>
                        <select class="form-select" id="filterCategory">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    
                    <!-- Payment Method Filter -->
                    <div class="col-6 col-md-2">
                        <label for="filterPayment" class="form-label">Payment</label>
                        <select class="form-select" id="filterPayment">
                            <option value="">All Methods</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    
                    <!-- Date From -->
                    <div class="col-6 col-md-2">
                        <label for="filterDateFrom" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="filterDateFrom">
                    </div>
                    
                    <!-- Date To -->
                    <div class="col-6 col-md-2">
                        <label for="filterDateTo" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="filterDateTo">
                    </div>
                </div>
                
                <!-- Filter Actions -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                        <i class="fas fa-redo me-1"></i>Clear Filters
                    </button>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-success btn-sm" onclick="exportExpenses()">
                            <i class="fas fa-download me-1"></i>Export CSV
                        </button>
                        <a href="add-expense.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Expense
                        </a>
                    </div>
                </div>
            </div>

            <!-- View Toggle & Sort -->
            <div class="d-flex justify-content-between align-items-center my-3">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm active" id="viewTable" onclick="toggleView('table')">
                        <i class="fas fa-table me-1"></i>Table
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="viewCards" onclick="toggleView('cards')">
                        <i class="fas fa-th-large me-1"></i>Cards
                    </button>
                </div>
                
                <p class="text-muted mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>Click on any expense to view details
                </p>
            </div>

            <!-- Expenses Container -->
            <div class="card fade-in">
                <div class="card-body p-0" id="expensesContainer">
                    <!-- Loading -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading expenses...</p>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="mt-4"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/main.js"></script>
    <script src="js/expenses.js"></script>
</body>
</html>

<?php
/**
 * Profile Page
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/models/User.php';

// Require authentication
Auth::requireLogin();

$user = Auth::user();
$userId = Auth::id();

// Load full user details
$userModel = new User();
$userDetails = $userModel->getById($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo APP_NAME; ?></title>
    
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
                <a class="nav-link active" href="profile.php">
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
                <h5 class="mb-0 ms-2">My Profile</h5>
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

        <!-- Profile Content -->
        <div class="container-fluid p-4">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-12 col-lg-4 mb-4">
                    <div class="card fade-in">
                        <div class="card-body text-center">
                            <!-- Profile Picture -->
                            <div class="position-relative d-inline-block mb-3">
                                <img src="<?php echo !empty($userDetails['profile_pic']) && $userDetails['profile_pic'] !== 'default-avatar.png' 
                                    ? 'uploads/profiles/' . htmlspecialchars($userDetails['profile_pic']) 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&size=150&background=4e73df&color=fff'; ?>" 
                                     alt="Profile Picture" 
                                     class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover;"
                                     id="profileImage">
                                <button class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0" 
                                        style="width: 40px; height: 40px;"
                                        onclick="document.getElementById('profilePicInput').click()">
                                    <i class="fas fa-camera"></i>
                                </button>
                                <input type="file" id="profilePicInput" accept="image/*" style="display: none;" onchange="uploadProfilePic(event)">
                            </div>
                            
                            <h5 class="mb-1"><?php echo htmlspecialchars($userDetails['name']); ?></h5>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($userDetails['email']); ?></p>
                            <span class="badge bg-<?php echo $userDetails['role'] === 'admin' ? 'danger' : 'primary'; ?> mb-3">
                                <?php echo ucfirst($userDetails['role']); ?>
                            </span>
                            
                            <hr>
                            
                            <div class="text-start">
                                <p class="mb-2"><i class="fas fa-calendar-alt me-2 text-muted"></i>
                                    <small>Member since <?php echo date('M Y', strtotime($userDetails['created_at'])); ?></small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forms Section -->
                <div class="col-12 col-lg-8">
                    <!-- Update Profile -->
                    <div class="card mb-4 fade-in">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Profile Information</h6>
                        </div>
                        <div class="card-body">
                            <form id="updateProfileForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($userDetails['name']); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card fade-in">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h6>
                        </div>
                        <div class="card-body">
                            <form id="changePasswordForm">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </button>
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
        // Update Profile Form
        document.getElementById('updateProfileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            btn.disabled = true;
            
            try {
                const formData = new FormData(this);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                const response = await fetch('/seee/api/profile.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(data).toString()
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Profile updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Failed to update profile', 'danger');
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

        // Change Password Form
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'danger');
                return;
            }
            
            if (newPassword.length < 6) {
                showToast('Password must be at least 6 characters', 'danger');
                return;
            }
            
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Changing...';
            btn.disabled = true;
            
            try {
                const response = await fetch('/seee/api/profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: document.getElementById('current_password').value,
                        new_password: newPassword
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Password changed successfully!', 'success');
                    this.reset();
                } else {
                    showToast(result.message || 'Failed to change password', 'danger');
                }
                
                btn.innerHTML = originalText;
                btn.disabled = false;
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'danger');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });

        // Upload Profile Picture
        async function uploadProfilePic(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            showLoading();
            
            try {
                const formData = new FormData();
                formData.append('profile_pic', file);
                
                const response = await fetch('/seee/api/profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Profile picture updated!', 'success');
                    document.getElementById('profileImage').src = result.data.url;
                } else {
                    showToast(result.message || 'Failed to upload image', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'danger');
            } finally {
                hideLoading();
            }
        }
    </script>
</body>
</html>

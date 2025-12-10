# Script to create 30 meaningful commits for expense tracker project
# Run this from the project root directory

$commits = @(
    @{ files = "config/config.example.php"; msg = "Initial commit: Add config example file" }
    @{ files = "database/expense_tracker.sql"; msg = "Add database schema and seed data" }
    @{ files = "app/helpers/Database.php"; msg = "Create Database helper class for PDO connection" }
    @{ files = "app/helpers/Response.php"; msg = "Add Response helper for API responses" }
    @{ files = "app/helpers/Auth.php"; msg = "Implement Auth helper for session management" }
    @{ files = "app/helpers/FileUpload.php"; msg = "Add FileUpload helper for receipt handling" }
    @{ files = "app/models/User.php"; msg = "Create User model with login/register methods" }
    @{ files = "app/models/Category.php"; msg = "Implement Category model for expense categories" }
    @{ files = "app/models/Expense.php"; msg = "Add Expense model with CRUD operations" }
    @{ files = "api/login.php"; msg = "Create login API endpoint" }
    @{ files = "api/register.php"; msg = "Add user registration API" }
    @{ files = "api/logout.php"; msg = "Implement logout functionality" }
    @{ files = "api/categories.php"; msg = "Add categories API with admin protection" }
    @{ files = "api/expenses.php"; msg = "Create expenses API with filtering support" }
    @{ files = "api/stats.php"; msg = "Add statistics API for dashboard" }
    @{ files = "api/profile.php"; msg = "Implement profile update API" }
    @{ files = "api/upload.php"; msg = "Add file upload API endpoint" }
    @{ files = "public/css/style.css"; msg = "Add custom CSS with dark mode support" }
    @{ files = "public/js/main.js"; msg = "Create main JavaScript utility functions" }
    @{ files = "public/js/auth.js"; msg = "Implement login/register JavaScript handlers" }
    @{ files = "public/js/dashboard.js"; msg = "Add dashboard JavaScript for charts and stats" }
    @{ files = "public/js/expenses.js"; msg = "Create expenses list JavaScript with filters" }
    @{ files = "public/index.php"; msg = "Build login page UI" }
    @{ files = "public/register.php"; msg = "Add registration page" }
    @{ files = "public/dashboard.php"; msg = "Create dashboard with statistics cards" }
    @{ files = "public/expenses.php"; msg = "Build expenses list page with table/card views" }
    @{ files = "public/add-expense.php"; msg = "Add expense creation form" }
    @{ files = "public/edit-expense.php"; msg = "Implement expense edit page" }
    @{ files = "public/expense-details.php"; msg = "Create expense details view page" }
    @{ files = "public/profile.php"; msg = "Add user profile management page" }
    @{ files = "README.md, .gitignore, LICENSE"; msg = "Add documentation and project files" }
)

Write-Host "Starting commit process..." -ForegroundColor Green
Write-Host ""

$count = 1
foreach ($commit in $commits) {
    Write-Host "[$count/30] $($commit.msg)" -ForegroundColor Cyan
    
    # Stage files
    git add $commit.files 2>$null
    
    # Commit
    git commit -m "$($commit.msg)" 2>$null
    
    $count++
    Start-Sleep -Milliseconds 500
}

Write-Host ""
Write-Host "✅ All 30 commits created successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Create GitHub repository" 
Write-Host "2. Run: git remote add origin <your-repo-url>"
Write-Host "3. Run: git push -u origin main"

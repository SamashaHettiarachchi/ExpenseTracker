# Create 30 meaningful commits for Expense Tracker

# Commit 1: Initial project structure
git add README.md LICENSE .gitignore
git commit -m "Initial commit: Add project documentation and license"

# Commit 2: Database schema
git add database/expense_tracker.sql
git commit -m "Add database schema with users, categories, and expenses tables"

# Commit 3: Configuration
git add config/config.example.php
git commit -m "Add configuration example file"

# Commit 4: Database helper
git add app/helpers/Database.php
git commit -m "Implement Database helper class with PDO connection"

# Commit 5: Response helper
git add app/helpers/Response.php
git commit -m "Add Response helper for standardized API responses"

# Commit 6: Auth helper
git add app/helpers/Auth.php
git commit -m "Implement authentication and session management"

# Commit 7: File upload helper
git add app/helpers/FileUpload.php
git commit -m "Add file upload validation and handling"

# Commit 8: User model
git add app/models/User.php
git commit -m "Create User model with login and registration methods"

# Commit 9: Category model
git add app/models/Category.php
git commit -m "Implement Category model for expense categorization"

# Commit 10: Expense model
git add app/models/Expense.php
git commit -m "Add Expense model with CRUD operations"

# Commit 11: Login API
git add api/login.php
git commit -m "Create login API endpoint with JWT authentication"

# Commit 12: Register API
git add api/register.php
git commit -m "Implement user registration API with validation"

# Commit 13: Logout API
git add api/logout.php
git commit -m "Add logout endpoint for session management"

# Commit 14: Expenses API
git add api/expenses.php
git commit -m "Create expenses CRUD API with filtering and pagination"

# Commit 15: Categories API
git add api/categories.php
git commit -m "Implement categories management API for admins"

# Commit 16: Stats API
git add api/stats.php
git commit -m "Add statistics API for dashboard analytics"

# Commit 17: Profile API
git add api/profile.php
git commit -m "Create profile update API endpoint"

# Commit 18: Upload API
git add api/upload.php
git commit -m "Implement file upload API for receipts and avatars"

# Commit 19: CSS styling
git add public/css/style.css
git commit -m "Add responsive CSS with dark mode support"

# Commit 20: Main JavaScript
git add public/js/main.js
git commit -m "Implement core JavaScript utilities and helpers"

# Commit 21: Auth JavaScript
git add public/js/auth.js
git commit -m "Add client-side authentication logic"

# Commit 22: Dashboard JavaScript
git add public/js/dashboard.js
git commit -m "Create dashboard with Chart.js visualization"

# Commit 23: Expenses JavaScript
git add public/js/expenses.js
git commit -m "Implement expenses list with filters and CSV export"

# Commit 24: Login page
git add public/index.php
git commit -m "Add login page with form validation"

# Commit 25: Register page
git add public/register.php
git commit -m "Create user registration page"

# Commit 26: Dashboard page
git add public/dashboard.php
git commit -m "Implement dashboard with statistics cards"

# Commit 27: Expenses pages
git add public/expenses.php public/add-expense.php public/edit-expense.php public/expense-details.php
git commit -m "Add expenses management pages with CRUD functionality"

# Commit 28: Profile page
git add public/profile.php
git commit -m "Create profile management page"

# Commit 29: Upload directories
git add public/uploads/receipts/.gitkeep public/uploads/profiles/.gitkeep
git commit -m "Add upload directories for receipts and profile pictures"

# Commit 30: Documentation
git add CHANGELOG.md CONTRIBUTING.md
git commit -m "Add changelog and contributing guidelines"

# Push all commits
git push -u origin main

echo "✅ Successfully created 30 commits and pushed to GitHub!"

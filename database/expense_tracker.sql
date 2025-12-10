-- ================================================
-- EXPENSE TRACKER DATABASE SCHEMA
-- ================================================

-- Create database
CREATE DATABASE IF NOT EXISTS expense_tracker;
USE expense_tracker;

-- ================================================
-- TABLE 1: USERS
-- ================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT 'default-avatar.png',
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE 2: CATEGORIES
-- ================================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'fa-circle',
    color VARCHAR(20) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE 3: EXPENSES
-- ================================================
CREATE TABLE expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    payment_method ENUM('cash', 'card', 'upi', 'bank_transfer') DEFAULT 'cash',
    receipt_image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_expense_date (expense_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- TABLE 4: BUDGETS (Optional - for bonus features)
-- ================================================
CREATE TABLE budgets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    monthly_limit DECIMAL(10,2) NOT NULL,
    month DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_category_month (user_id, category_id, month),
    INDEX idx_month (month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- INSERT DEFAULT CATEGORIES
-- ================================================
INSERT INTO categories (name, icon, color) VALUES
('Food & Dining', 'fa-utensils', '#e74c3c'),
('Transportation', 'fa-car', '#3498db'),
('Shopping', 'fa-shopping-cart', '#9b59b6'),
('Entertainment', 'fa-film', '#f39c12'),
('Bills & Utilities', 'fa-file-invoice-dollar', '#e67e22'),
('Healthcare', 'fa-medkit', '#1abc9c'),
('Education', 'fa-graduation-cap', '#34495e'),
('Travel', 'fa-plane', '#16a085'),
('Personal Care', 'fa-heart', '#e91e63'),
('Other', 'fa-ellipsis-h', '#95a5a6');

-- ================================================
-- INSERT DEFAULT USERS
-- ================================================
-- Password: admin123 (hashed with bcrypt)
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@expense.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Doe', 'user@expense.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- ================================================
-- INSERT SAMPLE EXPENSES (for demo)
-- ================================================
INSERT INTO expenses (user_id, category_id, title, amount, description, expense_date, payment_method, status) VALUES
-- Admin User (user_id = 1) expenses
(1, 1, 'Business Lunch', 1500.00, 'Client meeting at restaurant', CURDATE(), 'card', 'approved'),
(1, 2, 'Taxi to Airport', 450.00, 'Business trip transportation', CURDATE(), 'cash', 'approved'),
(1, 3, 'Office Supplies', 3200.00, 'Printer and stationery', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'card', 'approved'),
(1, 5, 'Internet Bill', 999.00, 'Monthly broadband', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'bank_transfer', 'approved'),
(1, 8, 'Health Insurance', 5000.00, 'Quarterly premium', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'bank_transfer', 'approved'),
(1, 6, 'Gym Membership', 2500.00, 'Annual membership', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 'card', 'approved'),
(1, 4, 'Concert Tickets', 1800.00, 'Weekend entertainment', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 'upi', 'approved'),
-- Regular User (user_id = 2) expenses
(2, 1, 'Lunch at Restaurant', 850.00, 'Team lunch meeting', CURDATE(), 'card', 'approved'),
(2, 2, 'Uber Ride', 250.00, 'Office to home', CURDATE(), 'upi', 'approved'),
(2, 3, 'Online Shopping', 2500.00, 'Clothes and accessories', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'card', 'approved'),
(2, 5, 'Electricity Bill', 1200.00, 'Monthly electricity bill', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'bank_transfer', 'approved'),
(2, 4, 'Movie Tickets', 600.00, 'Weekend movie', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'cash', 'approved');

-- ================================================
-- CREATE VIEWS FOR DASHBOARD STATISTICS
-- ================================================

-- View: Monthly expenses by user
CREATE VIEW v_monthly_expenses AS
SELECT 
    user_id,
    YEAR(expense_date) as year,
    MONTH(expense_date) as month,
    SUM(amount) as total_amount,
    COUNT(*) as total_count
FROM expenses
WHERE status = 'approved'
GROUP BY user_id, YEAR(expense_date), MONTH(expense_date);

-- View: Category-wise expenses
CREATE VIEW v_category_expenses AS
SELECT 
    e.user_id,
    c.name as category_name,
    c.color as category_color,
    SUM(e.amount) as total_amount,
    COUNT(*) as expense_count
FROM expenses e
JOIN categories c ON e.category_id = c.id
WHERE e.status = 'approved'
GROUP BY e.user_id, c.id, c.name, c.color;

-- ================================================
-- END OF SCHEMA
-- ================================================

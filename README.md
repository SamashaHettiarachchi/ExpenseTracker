# 💰 Expense Tracker - CRUD Application

A comprehensive expense tracking web application built with PHP, MySQL, Bootstrap 5, and Chart.js. Track your daily expenses, visualize spending patterns, manage budgets, and export data seamlessly.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-purple)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4.0-green)

## 📋 Table of Contents

- [Setup Instructions](#setup-instructions)
- [API Endpoints List](#api-endpoints-list)
- [Login Credentials](#login-credentials)
- [ER Diagram](#er-diagram)

## ✨ Features

## 📥 Setup Instructions (Step by Step)

### Prerequisites

- **XAMPP/WAMP/LAMP** - Local server environment
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- **Apache Web Server**

### Step 1: Clone or Download the Project

```bash
git clone https://github.com/SamashaHettiarachchi/ExpenseTracker.git
cd ExpenseTracker
```

### Step 2: Move to Web Server Directory

```bash
# For XAMPP on Windows
Copy the folder to C:\xampp\htdocs\

# For XAMPP on macOS/Linux
Copy the folder to /opt/lampp/htdocs/
```

### Step 3: Start Apache and MySQL

- Open XAMPP Control Panel
- Start Apache and MySQL modules

### Step 4: Configure Database Connection

Edit `config/config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'expense_tracker');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
```

### Step 5: Database Setup

**Method 1: phpMyAdmin (Recommended)**

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: Click "New" → Enter `expense_tracker` → Click "Create"
3. Import SQL file:
   - Select `expense_tracker` database
   - Click "Import" tab
   - Choose file: `database/expense_tracker.sql`
   - Click "Go"

**Method 2: MySQL Command Line**

```bash
mysql -u root -p expense_tracker < database/expense_tracker.sql
```

### Step 6: Access the Application

Open browser: `http://localhost/ExpenseTracker/public/`

## 🔌 API Endpoints List

All API endpoints return JSON responses in this format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

### Authentication Endpoints

#### POST `/api/login.php`

Login user

```json
{
  "email": "admin@expense.com",
  "password": "admin123"
}
```

#### POST `/api/register.php`

Register new user

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

#### GET `/api/logout.php`

Logout current user

### Expense Endpoints

#### GET `/api/expenses.php`

Get expenses with filters

```
?search=grocery&category_id=1&payment_method=cash&date_from=2024-01-01&date_to=2024-12-31&status=approved&order_by=expense_date&order_dir=DESC&page=1&limit=10
```

#### POST `/api/expenses.php`

Create new expense

```json
{
  "title": "Grocery Shopping",
  "amount": 1500.0,
  "category_id": 1,
  "expense_date": "2024-12-09",
  "payment_method": "cash",
  "description": "Weekly groceries",
  "receipt_image": "filename.jpg"
}
```

#### PUT `/api/expenses.php?id=1`

Update expense

```json
{
  "title": "Updated Title",
  "amount": 2000.00,
  ...
}
```

#### DELETE `/api/expenses.php?id=1`

Delete expense

### Other Endpoints

#### GET `/api/categories.php`

Get all categories

#### GET `/api/stats.php`

Get dashboard statistics

#### PUT `/api/profile.php`

Update profile information

#### POST `/api/profile.php`

Change password or upload profile picture

#### POST `/api/upload.php`

Upload file (receipt/profile pic)

## 🔑 Login Credentials (Admin + Normal User)

| Role        | Email             | Password |
| ----------- | ----------------- | -------- |
| Admin User  | admin@expense.com | admin123 |
| Normal User | user@expense.com  | admin123 |

**⚠️ Important**: Change these credentials after first login for security!

## 📊 ER Diagram

For detailed database structure and relationships, see: [`database/ER_DIAGRAM.md`](database/ER_DIAGRAM.md)

The database includes:

- **4 Tables**: users, categories, expenses, budgets
- **2 Views**: expense_statistics, user_expense_summary
- **Relationships**: One-to-Many between users-expenses, categories-expenses

---

Made with ❤️ by Sashi

---

**⭐ If you find this project helpful, please give it a star!**

## 📞 Support

For support, email your.email@example.com or open an issue in the repository.

---

Made with ❤️ by Sashi

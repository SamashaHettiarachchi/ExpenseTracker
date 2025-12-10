# 💰 Expense Tracker - CRUD Application

A comprehensive expense tracking web application built with PHP, MySQL, Bootstrap 5, and Chart.js. Track your daily expenses, visualize spending patterns, manage budgets, and export data seamlessly.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-purple)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4.0-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Usage Guide](#usage-guide)
- [Screenshots](#screenshots)
- [Security Features](#security-features)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### 🔐 Authentication & Authorization

- User registration with email validation
- Secure login with password hashing (bcrypt)
- Session management with Auth helper
- Role-based access control (Admin/User)

### 💳 Expense Management

- **Create** expenses with receipt uploads
- **Read** expenses with advanced filtering
- **Update** expenses with ownership validation
- **Delete** expenses with file cleanup
- Multiple payment methods (Cash, Card, UPI, Bank Transfer)
- Expense status tracking (Pending, Approved, Rejected)
- Receipt image uploads (JPG, PNG, GIF, PDF - max 5MB)

### 📊 Dashboard & Analytics

- Real-time statistics (Total, Monthly, Daily expenses)
- Interactive Chart.js visualizations (Category breakdown)
- Top spending category display
- Recent expenses overview

### 🔍 Advanced Filtering

- Search by title/description
- Filter by category, payment method, date range, status
- Sort by date, amount, or title
- Pagination with customizable items per page

### 📤 Data Export

- Export expenses to CSV format
- Filtered exports based on current search criteria
- Properly formatted data with headers

### 🎨 UI/UX Features

- **Dark Mode** with localStorage persistence
- Responsive design (Mobile, Tablet, Desktop)
- Toast notifications for user feedback
- Loading spinners for async operations
- Image preview before upload
- Modal popups for receipt viewing
- Print-friendly layouts

### 👤 Profile Management

- Update profile information (Name, Email)
- Change password with current password verification
- Profile picture upload with avatar fallback
- Member since date display

## 🛠 Tech Stack

### Backend

- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Database management
- **PDO** - Database connection with prepared statements

### Frontend

- **HTML5** - Semantic markup
- **CSS3** - Custom styling with CSS variables
- **JavaScript ES6+** - Interactive functionality
- **Bootstrap 5.3.0** - Responsive UI framework
- **Font Awesome 6.4.0** - Icon library
- **Chart.js 4.4.0** - Data visualization

### Architecture

- **MVC Pattern** - Separation of concerns
- **RESTful API** - Clean API endpoints
- **OOP Principles** - Model classes for data handling
- **Helper Classes** - Reusable utilities (Auth, Database, FileUpload, Response)

## 📥 Installation

### Prerequisites

- **XAMPP/WAMP/LAMP** - Local server environment
- **PHP 7.4 or higher**
- **MySQL 5.7 or higher**
- **Apache Web Server**

### Step-by-Step Setup

1. **Clone or Download the Project**

   ```bash
   git clone <repository-url>
   cd seee
   ```

2. **Move to Web Server Directory**

   ```bash
   # For XAMPP on Windows
   Copy the 'seee' folder to C:\xampp\htdocs\

   # For XAMPP on macOS/Linux
   Copy the 'seee' folder to /opt/lampp/htdocs/
   ```

3. **Start Apache and MySQL**

   - Open XAMPP Control Panel
   - Start Apache and MySQL modules

4. **Configure Database Connection**

   Edit `config/config.php` with your database credentials:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'expense_tracker');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Your MySQL password
   ```

## 🗄 Database Setup

### Method 1: phpMyAdmin (Recommended)

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create database: Click "New" → Enter `expense_tracker` → Click "Create"
3. Import SQL file:
   - Select `expense_tracker` database
   - Click "Import" tab
   - Choose file: `database/expense_tracker.sql`
   - Click "Go"

### Method 2: MySQL Command Line

```bash
# Open terminal/command prompt
mysql -u root -p

# Create database
CREATE DATABASE expense_tracker;
USE expense_tracker;

# Import SQL file
SOURCE /path/to/seee/database/expense_tracker.sql;
```

### Method 3: Command Line Import

```bash
# Windows (PowerShell/CMD)
mysql -u root -p expense_tracker < database\expense_tracker.sql

# macOS/Linux
mysql -u root -p expense_tracker < database/expense_tracker.sql
```

### Database Structure

The database includes:

- **4 Tables**: users, categories, expenses, budgets
- **2 Views**: expense_statistics, user_expense_summary
- **Sample Data**: 10 categories, 2 demo users, 5 sample expenses

## 📁 Project Structure

```
seee/
├── api/                      # RESTful API Endpoints
│   ├── categories.php        # Category CRUD operations
│   ├── expenses.php          # Expense CRUD operations
│   ├── login.php             # User authentication
│   ├── logout.php            # Session termination
│   ├── profile.php           # Profile management
│   ├── register.php          # User registration
│   ├── stats.php             # Dashboard statistics
│   └── upload.php            # File upload handler
├── app/
│   ├── helpers/              # Helper Classes
│   │   ├── Auth.php          # Authentication & session management
│   │   ├── Database.php      # PDO database connection
│   │   ├── FileUpload.php    # File upload validation & handling
│   │   └── Response.php      # JSON response formatter
│   └── models/               # Data Models (OOP)
│       ├── Category.php      # Category model
│       ├── Expense.php       # Expense model
│       └── User.php          # User model
├── config/
│   └── config.php            # Application configuration
├── database/
│   ├── expense_tracker.sql   # Complete database dump
│   └── ER_DIAGRAM.md         # Entity Relationship diagram
├── public/                   # Web-accessible files
│   ├── css/
│   │   └── style.css         # Custom CSS with dark mode
│   ├── js/
│   │   ├── auth.js           # Authentication logic
│   │   ├── dashboard.js      # Dashboard & Chart.js
│   │   ├── expenses.js       # Expense list & filters
│   │   └── main.js           # Global functions & utilities
│   ├── uploads/
│   │   ├── profiles/         # Profile pictures
│   │   └── receipts/         # Expense receipts
│   ├── add-expense.php       # Add expense form
│   ├── dashboard.php         # Main dashboard
│   ├── edit-expense.php      # Edit expense form
│   ├── expense-details.php   # Single expense view
│   ├── expenses.php          # Expense list
│   ├── index.php             # Login page
│   ├── profile.php           # User profile
│   └── register.php          # Registration page
├── DATABASE_SETUP.md         # Database setup guide
└── README.md                 # This file
```

## 🔌 API Documentation

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

## 📖 Usage Guide

### First Time Setup

1. **Access the Application**

   - Open browser: `http://localhost/seee/public/`

2. **Login with Demo Account**

   - **Admin User**:

     - Email: `admin@expense.com`
     - Password: `admin123`

   - **Regular User**:
     - Email: `user@expense.com`
     - Password: `admin123`

3. **Or Register New Account**
   - Click "Don't have an account? Register"
   - Fill in Name, Email, Password
   - Click "Register"

### Adding Expenses

1. Click "Add Expense" in sidebar or dashboard
2. Fill in expense details:
   - Title (required)
   - Amount (required)
   - Category (required)
   - Date (required)
   - Payment Method (required)
   - Description (optional)
   - Receipt Image (optional)
3. Click "Add Expense"

### Viewing & Managing Expenses

1. Go to "Expenses" page
2. Use filters to narrow down:
   - Search by title/description
   - Filter by category, payment method, date range
   - Sort by date, amount, or title
3. Click on expense to view details
4. Edit or delete from details page

### Using Dark Mode

1. Click the toggle switch in top navbar
2. Theme preference is saved automatically
3. Applies to all pages

### Exporting Data

1. Go to "Expenses" page
2. Apply desired filters
3. Click "Export CSV" button
4. CSV file downloads with filtered data

### Profile Management

1. Click on your name → "Profile"
2. Update name or email
3. Change password if needed
4. Upload profile picture

## 🔒 Security Features

- **Password Hashing**: bcrypt with salt
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: htmlspecialchars() on all outputs
- **CSRF Protection**: Session-based validation
- **File Upload Validation**: Type, size, extension checks
- **Authentication**: Session-based with timeout
- **Authorization**: Ownership validation for CRUD operations
- **Secure File Storage**: Unique filenames with timestamp

## 🎨 Screenshots

### Login Page

Clean login interface with gradient background

### Dashboard

Interactive dashboard with statistics cards and Chart.js visualization

### Expenses List

Advanced filtering with table/card view toggle

### Add/Edit Expense

Form with receipt upload and image preview

### Expense Details

Full expense view with receipt modal

### Profile Page

Profile management with picture upload

### Dark Mode

All pages support dark theme with smooth transitions

## 🔑 Default Login Credentials

| Role  | Email             | Password |
| ----- | ----------------- | -------- |
| Admin | admin@expense.com | password |
| User  | user@expense.com  | password |

**⚠️ Important**: Change these credentials after first login for security!

## 🐛 Troubleshooting

### Database Connection Error

- Check MySQL is running in XAMPP
- Verify credentials in `config/config.php`
- Ensure database `expense_tracker` exists

### File Upload Not Working

- Check folder permissions:
  ```bash
  chmod 755 public/uploads/receipts
  chmod 755 public/uploads/profiles
  ```
- Verify `upload_max_filesize` in php.ini (minimum 5M)

### CSS/JS Not Loading

- Clear browser cache (Ctrl+Shift+R)
- Check file paths in HTML
- Verify Apache is serving files from correct directory

### Session Issues

- Check `session.save_path` in php.ini
- Ensure cookies are enabled in browser
- Clear browser cookies for localhost

## 🚀 Future Enhancements

- [ ] Budget alerts and notifications
- [ ] Recurring expenses
- [ ] Multiple currency support
- [ ] Advanced analytics with more charts
- [ ] Email notifications
- [ ] Mobile app (PWA)
- [ ] API rate limiting
- [ ] Two-factor authentication
- [ ] Expense categories customization
- [ ] Team/family expense sharing

## 👥 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Developer

**Sashi**

- GitHub: [@yourusername]
- Email: your.email@example.com

## 🙏 Acknowledgments

- Bootstrap team for the amazing CSS framework
- Chart.js for beautiful visualizations
- Font Awesome for comprehensive icon library
- PHP community for excellent documentation

---

**⭐ If you find this project helpful, please give it a star!**

## 📞 Support

For support, email your.email@example.com or open an issue in the repository.

---

Made with ❤️ by Sashi

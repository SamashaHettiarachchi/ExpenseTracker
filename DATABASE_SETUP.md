# 🚀 DATABASE SETUP GUIDE

## ✅ **EASIEST METHOD: phpMyAdmin** (Recommended)

### Step-by-Step:

1. **Start XAMPP/WAMP**

   - Open XAMPP Control Panel
   - Start Apache + MySQL

2. **Open phpMyAdmin**

   - Go to: http://localhost/phpmyadmin
   - Or click "Admin" button next to MySQL in XAMPP

3. **Import Database**

   - Click **"Import"** tab at the top
   - Click **"Choose File"** button
   - Navigate to: `C:\Users\LENOVO\Downloads\Sashi\seee\database\expense_tracker.sql`
   - Click **"Go"** button at bottom
   - Wait for success message ✅

4. **Verify**
   - You should see database `expense_tracker` in left sidebar
   - Click it to see 4 tables: users, categories, expenses, budgets

---

## 🔧 **ALTERNATIVE: MySQL Command (if phpMyAdmin fails)**

### Method 1: Direct Command

```powershell
cd C:\Users\LENOVO\Downloads\Sashi\seee
mysql -u root -p -e "source database/expense_tracker.sql"
```

(Enter your MySQL password when prompted)

### Method 2: Two-Step Process

```powershell
# Step 1: Login to MySQL
mysql -u root -p

# Step 2: Inside MySQL, run:
source C:/Users/LENOVO/Downloads/Sashi/seee/database/expense_tracker.sql
exit;
```

---

## ✅ **What Gets Created:**

- Database: `expense_tracker`
- Tables: `users`, `categories`, `expenses`, `budgets`
- 10 Categories: Food, Transport, Shopping, Entertainment, Bills, Healthcare, Education, Travel, Personal Care, Other
- 2 Demo Users:
  - admin@expense.com / admin123 (Admin)
  - user@expense.com / admin123 (User)
- 5 Sample Expenses

---

## 🎯 **After Import:**

Copy config file:

```powershell
Copy-Item config\config.example.php config\config.php
```

Then edit `config\config.php` with your MySQL password (if not empty).

---

## ❓ **Problems?**

- **"MySQL not found"** → Add MySQL to PATH or use full path: `C:\xampp\mysql\bin\mysql.exe`
- **"Access denied"** → Check your MySQL username/password
- **"Database exists"** → Drop it first: `DROP DATABASE expense_tracker;`

---

**👉 Use phpMyAdmin - it's visual, easier, and always works!**

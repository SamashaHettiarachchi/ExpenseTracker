# Expense Tracker - Quick Start Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Setup Environment

1. Download and install XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Deploy Application

1. Copy the `seee` folder to `C:\xampp\htdocs\`
2. Your project path should be: `C:\xampp\htdocs\seee\`

### Step 3: Create Database

1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click "New" on the left sidebar
3. Create database named: `expense_tracker`
4. Click on `expense_tracker` database
5. Click "Import" tab
6. Choose file: `database/expense_tracker.sql`
7. Click "Go" button at the bottom
8. Wait for success message

### Step 4: Configure (Optional)

If your MySQL has a password:

1. Edit `config/config.php`
2. Change `DB_PASS` to your MySQL password

### Step 5: Access Application

1. Open browser
2. Go to: `http://localhost/seee/public/`
3. Login with:
   - Email: `admin@expense.com`
   - Password: `admin123`

## ✅ That's It!

You're ready to track expenses!

## 🎯 What to Do Next

1. **Add Your First Expense**

   - Click "Add Expense" in sidebar
   - Fill in the form
   - Upload a receipt (optional)

2. **View Dashboard**

   - See your expense statistics
   - Check the category breakdown chart

3. **Try Dark Mode**

   - Click the toggle switch in top navbar

4. **Export Data**
   - Go to "Expenses" page
   - Click "Export CSV"

## 🔑 Demo Accounts

| Email             | Password | Role  |
| ----------------- | -------- | ----- |
| admin@expense.com | admin123 | Admin |
| user@expense.com  | admin123 | User  |

## 🆘 Having Issues?

### MySQL Not Starting

- Open XAMPP, click "Config" → "my.ini"
- Change port from 3306 to 3307
- Save and restart MySQL

### Page Not Loading

- Check URL: `http://localhost/seee/public/` (not `file://`)
- Verify Apache is running in XAMPP
- Check if `seee` folder is in `htdocs`

### Database Error

- Verify database name is `expense_tracker`
- Check MySQL credentials in `config/config.php`
- Ensure SQL file was imported successfully

## 📱 Features to Explore

✨ **Dashboard**: Real-time statistics and charts  
💰 **Expenses**: Add, edit, delete with filters  
🎨 **Dark Mode**: Toggle for eye comfort  
📊 **Analytics**: Category-wise breakdown  
📤 **Export**: Download data as CSV  
👤 **Profile**: Manage your account  
📱 **Responsive**: Works on all devices

## 📖 Full Documentation

For detailed documentation, see [README.md](README.md)

---

**Happy Expense Tracking! 💸**

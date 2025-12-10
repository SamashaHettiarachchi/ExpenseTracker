# Contributing to Expense Tracker

First off, thank you for considering contributing to Expense Tracker! 🎉

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

### Our Standards

- Be respectful and inclusive
- Accept constructive criticism gracefully
- Focus on what's best for the community
- Show empathy towards other community members

## 🤝 How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**When reporting bugs, include:**

- Clear and descriptive title
- Steps to reproduce the issue
- Expected behavior vs actual behavior
- Screenshots if applicable
- PHP/MySQL version
- Browser and OS information

**Example:**

```
Title: CSV export fails on filtered data

Steps to Reproduce:
1. Go to Expenses page
2. Apply category filter
3. Click Export CSV
4. Error appears in console

Expected: CSV downloads with filtered data
Actual: JavaScript error in console

Environment: PHP 8.0, Chrome 120, Windows 11
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues.

**Include in your suggestion:**

- Clear description of the feature
- Use case and benefits
- Possible implementation approach
- Screenshots/mockups if applicable

### Code Contributions

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/AmazingFeature
   ```
3. **Make your changes**
4. **Test thoroughly**
5. **Commit with clear messages**
6. **Push to your fork**
7. **Open a Pull Request**

## 🛠 Development Setup

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- XAMPP/WAMP/LAMP
- Git
- Text editor (VS Code recommended)

### Setup Steps

1. **Fork and clone**

   ```bash
   git clone https://github.com/yourusername/expense-tracker.git
   cd expense-tracker
   ```

2. **Install dependencies**

   ```bash
   # No npm/composer dependencies currently
   # Just ensure PHP/MySQL are running
   ```

3. **Setup database**

   ```bash
   mysql -u root -p
   CREATE DATABASE expense_tracker_dev;
   USE expense_tracker_dev;
   SOURCE database/expense_tracker.sql;
   ```

4. **Configure environment**

   ```php
   // Edit config/config.php
   define('DB_NAME', 'expense_tracker_dev');
   ```

5. **Access dev site**
   ```
   http://localhost/expense-tracker/public/
   ```

## 📝 Coding Standards

### PHP Standards

Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style:

```php
<?php

namespace App\Models;

class Expense
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = [])
    {
        // Method implementation
    }
}
```

**Key Points:**

- Use 4 spaces for indentation
- Opening braces on same line for methods
- camelCase for method names
- PascalCase for class names
- Descriptive variable names

### JavaScript Standards

Follow ES6+ conventions:

```javascript
// Use const/let, not var
const expenses = [];

// Arrow functions for callbacks
expenses.map((expense) => expense.amount);

// Async/await for promises
async function loadExpenses() {
  const response = await fetch("/api/expenses.php");
  const data = await response.json();
  return data;
}
```

### CSS Standards

```css
/* Use meaningful class names */
.expense-card {
  border-radius: 10px;
  transition: all 0.3s ease;
}

/* Group related properties */
.btn-primary {
  /* Display & Box Model */
  display: inline-block;
  padding: 10px 20px;

  /* Typography */
  font-size: 14px;
  font-weight: 600;

  /* Visual */
  background: #4e73df;
  border-radius: 5px;
}
```

### SQL Standards

```sql
-- Use uppercase for SQL keywords
SELECT
    e.id,
    e.title,
    e.amount,
    c.name AS category_name
FROM expenses e
LEFT JOIN categories c ON e.category_id = c.id
WHERE e.user_id = ?
ORDER BY e.expense_date DESC;
```

## 📦 Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting)
- **refactor**: Code refactoring
- **test**: Adding tests
- **chore**: Maintenance tasks

### Examples

```bash
# Good commits
git commit -m "feat(expenses): add CSV export functionality"
git commit -m "fix(auth): resolve session timeout issue"
git commit -m "docs(readme): update installation instructions"
git commit -m "style(dashboard): improve card responsiveness"

# Bad commits
git commit -m "fixed stuff"
git commit -m "updates"
git commit -m "changes"
```

## 🔄 Pull Request Process

### Before Submitting

1. **Update documentation** if needed
2. **Test all features** affected by your changes
3. **Follow coding standards**
4. **Ensure no console errors**
5. **Update CHANGELOG.md** if applicable

### PR Template

```markdown
## Description

Brief description of changes

## Type of Change

- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing

How was this tested?

## Screenshots

If applicable

## Checklist

- [ ] Code follows style guidelines
- [ ] Self-reviewed code
- [ ] Commented complex code
- [ ] Updated documentation
- [ ] No new warnings/errors
- [ ] Added tests (if applicable)
```

### Review Process

1. PR will be reviewed by maintainers
2. Address any requested changes
3. Once approved, PR will be merged
4. Your contribution will be credited in CHANGELOG

## 🎯 Areas for Contribution

### High Priority

- [ ] Unit tests for models
- [ ] API rate limiting
- [ ] Budget management features
- [ ] Email notifications
- [ ] Multi-language support

### Medium Priority

- [ ] Advanced analytics
- [ ] Recurring expenses
- [ ] Custom categories
- [ ] Mobile PWA features
- [ ] Performance optimizations

### Low Priority

- [ ] Additional chart types
- [ ] Export formats (PDF, Excel)
- [ ] Dark mode improvements
- [ ] UI animations
- [ ] Code refactoring

## 🐛 Debugging Tips

### Enable PHP Errors

```php
// Add to config/config.php for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Browser DevTools

- Use Console for JavaScript errors
- Check Network tab for API failures
- Use Lighthouse for performance audit

### Database Queries

```php
// Add to Database.php for query debugging
echo $stmt->debugDumpParams();
```

## 📚 Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

## ❓ Questions?

Feel free to:

- Open a discussion on GitHub
- Contact maintainers
- Check existing issues

---

**Thank you for contributing! 🙌**

Your efforts help make Expense Tracker better for everyone.

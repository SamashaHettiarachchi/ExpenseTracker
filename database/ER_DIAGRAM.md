# 📊 Expense Tracker - ER Diagram

## Database Structure

```
┌─────────────────────────┐
│        USERS            │
├─────────────────────────┤
│ 🔑 id (PK)              │
│ 📝 name                 │
│ 📧 email (UNIQUE)       │
│ 🔒 password             │
│ 🖼️  profile_pic         │
│ 👤 role (admin/user)    │
│ 📅 created_at           │
└─────────────────────────┘
         │
         │ 1:N
         ├──────────────────┐
         │                  │
         ▼                  ▼
┌─────────────────────────┐ ┌─────────────────────────┐
│      EXPENSES           │ │       BUDGETS           │
├─────────────────────────┤ ├─────────────────────────┤
│ 🔑 id (PK)              │ │ 🔑 id (PK)              │
│ 🔗 user_id (FK)         │ │ 🔗 user_id (FK)         │
│ 🔗 category_id (FK)     │ │ 🔗 category_id (FK)     │
│ 📝 title                │ │ 💰 monthly_limit        │
│ 💰 amount               │ │ 📅 month                │
│ 📄 description          │ │ 📅 created_at           │
│ 📅 expense_date         │ └─────────────────────────┘
│ 💳 payment_method       │          │
│ 🖼️  receipt_image       │          │ N:1
│ ✅ status               │          │
│ 📅 created_at           │          ▼
│ 📅 updated_at           │ ┌─────────────────────────┐
└─────────────────────────┘ │      CATEGORIES         │
         │                  ├─────────────────────────┤
         │ N:1              │ 🔑 id (PK)              │
         └─────────────────▶│ 📝 name (UNIQUE)        │
                            │ 🎨 icon                 │
                            │ 🌈 color                │
                            │ 📅 created_at           │
                            └─────────────────────────┘
```

---

## Relationships

### 1. **USERS → EXPENSES** (One-to-Many)

- One user can have multiple expenses
- Each expense belongs to one user
- **FK**: `expenses.user_id` → `users.id`
- **CASCADE DELETE**: Delete user → delete all their expenses

### 2. **USERS → BUDGETS** (One-to-Many)

- One user can set multiple budgets (one per category per month)
- Each budget belongs to one user
- **FK**: `budgets.user_id` → `users.id`
- **CASCADE DELETE**: Delete user → delete all their budgets

### 3. **CATEGORIES → EXPENSES** (One-to-Many)

- One category can have multiple expenses
- Each expense belongs to one category
- **FK**: `expenses.category_id` → `categories.id`
- **RESTRICT DELETE**: Cannot delete category if expenses exist

### 4. **CATEGORIES → BUDGETS** (One-to-Many)

- One category can have multiple budgets
- Each budget is for one category
- **FK**: `budgets.category_id` → `categories.id`
- **CASCADE DELETE**: Delete category → delete related budgets

---

## Key Features

### Indexes

- `users.email` - Fast login lookups
- `expenses.user_id` - User expense queries
- `expenses.category_id` - Category filtering
- `expenses.expense_date` - Date range filtering
- `expenses.status` - Status filtering

### Constraints

- `users.email` - UNIQUE (no duplicate emails)
- `categories.name` - UNIQUE (no duplicate category names)
- `budgets` - UNIQUE (user_id, category_id, month) - One budget per category per month

### Enums

- `users.role`: 'admin', 'user'
- `expenses.payment_method`: 'cash', 'card', 'upi', 'bank_transfer'
- `expenses.status`: 'pending', 'approved', 'rejected'

### Default Values

- `users.profile_pic` → 'default-avatar.png'
- `users.role` → 'user'
- `categories.icon` → 'fa-circle'
- `categories.color` → '#6c757d'
- `expenses.payment_method` → 'cash'
- `expenses.status` → 'approved'

---

## Sample Queries

### Get user's monthly expenses

```sql
SELECT SUM(amount) as total
FROM expenses
WHERE user_id = 1
  AND MONTH(expense_date) = MONTH(CURDATE())
  AND YEAR(expense_date) = YEAR(CURDATE())
  AND status = 'approved';
```

### Get expenses by category

```sql
SELECT c.name, c.color, SUM(e.amount) as total
FROM expenses e
JOIN categories c ON e.category_id = c.id
WHERE e.user_id = 1 AND e.status = 'approved'
GROUP BY c.id;
```

### Check budget exceeded

```sql
SELECT b.monthly_limit, SUM(e.amount) as spent
FROM budgets b
LEFT JOIN expenses e ON b.category_id = e.category_id
  AND MONTH(e.expense_date) = MONTH(b.month)
  AND e.user_id = b.user_id
WHERE b.user_id = 1
GROUP BY b.id;
```

<?php
/**
 * Expense Model
 * Handles all expense-related database operations
 */

require_once __DIR__ . '/../helpers/Database.php';

class Expense {
    private $db;
    private $table = 'expenses';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new expense
     * @param array $data
     * @return array
     */
    public function create($data) {
        // Validate required fields
        $errors = [];
        
        if (empty($data['title'])) {
            $errors['title'] = 'Title is required';
        }
        
        if (empty($data['amount']) || $data['amount'] <= 0) {
            $errors['amount'] = 'Valid amount is required';
        }
        
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Category is required';
        }
        
        if (empty($data['expense_date'])) {
            $errors['expense_date'] = 'Date is required';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $sql = "INSERT INTO {$this->table} 
                (user_id, category_id, title, amount, description, expense_date, payment_method, receipt_image, status) 
                VALUES (:user_id, :category_id, :title, :amount, :description, :expense_date, :payment_method, :receipt_image, :status)";
        
        $stmt = $this->db->prepare($sql);
        
        $status = $data['status'] ?? 'approved';
        $paymentMethod = $data['payment_method'] ?? 'cash';
        $receiptImage = $data['receipt_image'] ?? null;
        $description = $data['description'] ?? null;
        
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':expense_date', $data['expense_date']);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindParam(':receipt_image', $receiptImage);
        $stmt->bindParam(':status', $status);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Expense created successfully',
                'expense_id' => $this->db->lastInsertId()
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create expense'];
    }
    
    /**
     * Get expense by ID
     * @param int $id
     * @param int $userId - Optional, to restrict by user
     * @return array|null
     */
    public function getById($id, $userId = null) {
        $sql = "SELECT e.*, c.name as category_name, c.icon as category_icon, c.color as category_color,
                       u.name as user_name
                FROM {$this->table} e
                JOIN categories c ON e.category_id = c.id
                JOIN users u ON e.user_id = u.id
                WHERE e.id = :id";
        
        if ($userId !== null) {
            $sql .= " AND e.user_id = :user_id";
        }
        
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($userId !== null) {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get all expenses with filters
     * @param array $filters
     * @return array
     */
    public function getAll($filters = []) {
        $sql = "SELECT e.*, c.name as category_name, c.icon as category_icon, c.color as category_color
                FROM {$this->table} e
                JOIN categories c ON e.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        // Filter by user
        if (isset($filters['user_id'])) {
            $sql .= " AND e.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        
        // Filter by category
        if (isset($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        // Filter by status
        if (isset($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filter by payment method
        if (isset($filters['payment_method'])) {
            $sql .= " AND e.payment_method = :payment_method";
            $params[':payment_method'] = $filters['payment_method'];
        }
        
        // Filter by date range
        if (isset($filters['date_from'])) {
            $sql .= " AND e.expense_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (isset($filters['date_to'])) {
            $sql .= " AND e.expense_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        // Search by title
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND e.title LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Sorting
        $orderBy = $filters['order_by'] ?? 'expense_date';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $sql .= " ORDER BY e.{$orderBy} {$orderDir}";
        
        // Pagination
        if (isset($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $limit = (int) $filters['limit'];
        }
        
        if (isset($filters['offset'])) {
            $sql .= " OFFSET :offset";
            $offset = (int) $filters['offset'];
        }
        
        $stmt = $this->db->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if (isset($limit)) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        if (isset($offset)) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update expense
     * @param int $id
     * @param array $data
     * @param int $userId - To verify ownership
     * @return array
     */
    public function update($id, $data, $userId = null) {
        // Check if expense exists and belongs to user
        $expense = $this->getById($id, $userId);
        
        if (!$expense) {
            return ['success' => false, 'message' => 'Expense not found'];
        }
        
        $updates = [];
        $params = [':id' => $id];
        
        if (isset($data['title'])) {
            $updates[] = "title = :title";
            $params[':title'] = $data['title'];
        }
        
        if (isset($data['amount'])) {
            $updates[] = "amount = :amount";
            $params[':amount'] = $data['amount'];
        }
        
        if (isset($data['category_id'])) {
            $updates[] = "category_id = :category_id";
            $params[':category_id'] = $data['category_id'];
        }
        
        if (isset($data['description'])) {
            $updates[] = "description = :description";
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['expense_date'])) {
            $updates[] = "expense_date = :expense_date";
            $params[':expense_date'] = $data['expense_date'];
        }
        
        if (isset($data['payment_method'])) {
            $updates[] = "payment_method = :payment_method";
            $params[':payment_method'] = $data['payment_method'];
        }
        
        if (isset($data['receipt_image'])) {
            $updates[] = "receipt_image = :receipt_image";
            $params[':receipt_image'] = $data['receipt_image'];
        }
        
        if (isset($data['status'])) {
            $updates[] = "status = :status";
            $params[':status'] = $data['status'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($params)) {
            return ['success' => true, 'message' => 'Expense updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update expense'];
    }
    
    /**
     * Delete expense
     * @param int $id
     * @param int $userId - To verify ownership
     * @return array
     */
    public function delete($id, $userId = null) {
        // Check if expense exists and belongs to user
        $expense = $this->getById($id, $userId);
        
        if (!$expense) {
            return ['success' => false, 'message' => 'Expense not found'];
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Expense deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete expense'];
    }
    
    /**
     * Get total count with filters
     * @param array $filters
     * @return int
     */
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} e WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['user_id'])) {
            $sql .= " AND e.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        
        if (isset($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $sql .= " AND e.title LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
    
    /**
     * Get statistics for dashboard
     * @param int $userId
     * @return array
     */
    public function getStats($userId) {
        $stats = [];
        
        // Total expenses (all time)
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE user_id = :user_id AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        $stats['total_expenses'] = (int) $result['count'];
        $stats['total_amount'] = (float) $result['total'];
        
        // This month
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE user_id = :user_id 
                AND status = 'approved'
                AND MONTH(expense_date) = MONTH(CURDATE())
                AND YEAR(expense_date) = YEAR(CURDATE())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        $stats['month_expenses'] = (int) $result['count'];
        $stats['month_amount'] = (float) $result['total'];
        
        // Today
        $sql = "SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total 
                FROM {$this->table} 
                WHERE user_id = :user_id 
                AND status = 'approved'
                AND DATE(expense_date) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        $stats['today_expenses'] = (int) $result['count'];
        $stats['today_amount'] = (float) $result['total'];
        
        // Top category
        $sql = "SELECT c.name, c.color, COUNT(*) as count, SUM(e.amount) as total
                FROM {$this->table} e
                JOIN categories c ON e.category_id = c.id
                WHERE e.user_id = :user_id AND e.status = 'approved'
                GROUP BY c.id
                ORDER BY total DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $stats['top_category'] = $stmt->fetch() ?: null;
        
        // Category breakdown
        $sql = "SELECT c.name, c.color, c.icon, COALESCE(SUM(e.amount), 0) as total
                FROM categories c
                LEFT JOIN {$this->table} e ON c.id = e.category_id 
                    AND e.user_id = :user_id 
                    AND e.status = 'approved'
                GROUP BY c.id
                ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $stats['category_breakdown'] = $stmt->fetchAll();
        
        // Recent expenses
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color
                FROM {$this->table} e
                JOIN categories c ON e.category_id = c.id
                WHERE e.user_id = :user_id
                ORDER BY e.expense_date DESC, e.created_at DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $stats['recent_expenses'] = $stmt->fetchAll();
        
        return $stats;
    }
}

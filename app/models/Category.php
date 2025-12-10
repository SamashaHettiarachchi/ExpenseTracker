<?php
/**
 * Category Model
 * Handles category-related database operations
 */

require_once __DIR__ . '/../helpers/Database.php';

class Category {
    private $db;
    private $table = 'categories';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all categories
     * @return array
     */
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create new category
     * @param array $data
     * @return array
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, icon, color) VALUES (:name, :icon, :color)";
        $stmt = $this->db->prepare($sql);
        
        $icon = $data['icon'] ?? 'fa-circle';
        $color = $data['color'] ?? '#6c757d';
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':icon', $icon);
        $stmt->bindParam(':color', $color);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Category created successfully',
                'category_id' => $this->db->lastInsertId()
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create category'];
    }
    
    /**
     * Update category
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET name = :name, icon = :icon, color = :color WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':icon', $data['icon']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Category updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update category'];
    }
    
    /**
     * Delete category
     * @param int $id
     * @return array
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Category deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete category'];
    }
}

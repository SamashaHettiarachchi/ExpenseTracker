<?php
/**
 * User Model
 * Handles all user-related database operations
 */

require_once __DIR__ . '/../helpers/Database.php';

class User {
    private $db;
    private $table = 'users';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register new user
     * @param array $data - User registration data
     * @return array - ['success' => bool, 'message' => string, 'user_id' => int]
     */
    public function register($data) {
        // Validate required fields
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if email already exists
        if ($this->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashedPassword = password_hash($data['password'], HASH_ALGO, ['cost' => HASH_COST]);
        
        // Insert user
        $sql = "INSERT INTO {$this->table} (name, email, password, role) VALUES (:name, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        
        $role = $data['role'] ?? 'user';
        
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $this->db->lastInsertId()
            ];
        }
        
        return ['success' => false, 'message' => 'Registration failed'];
    }
    
    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return array - ['success' => bool, 'message' => string, 'user' => array]
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Remove password from returned data
        unset($user['password']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    }
    
    /**
     * Get user by ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT id, name, email, profile_pic, role, created_at FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get user by email
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        $sql = "SELECT id, name, email, profile_pic, role, created_at FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Update user profile
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateProfile($id, $data) {
        $updates = [];
        $params = [':id' => $id];
        
        if (isset($data['name']) && !empty($data['name'])) {
            $updates[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            
            // Check if email exists for other users
            $sql = "SELECT id FROM {$this->table} WHERE email = :email AND id != :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $data['email'], ':id' => $id]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already in use'];
            }
            
            $updates[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['profile_pic']) && !empty($data['profile_pic'])) {
            $updates[] = "profile_pic = :profile_pic";
            $params[':profile_pic'] = $data['profile_pic'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($params)) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
    
    /**
     * Update password
     * @param int $id
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function updatePassword($id, $currentPassword, $newPassword) {
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        // Get current password hash
        $sql = "SELECT password FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, HASH_ALGO, ['cost' => HASH_COST]);
        
        // Update password
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update password'];
    }
    
    /**
     * Get all users (admin only)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 10, $offset = 0) {
        $sql = "SELECT id, name, email, profile_pic, role, created_at 
                FROM {$this->table} 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get total user count
     * @return int
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        
        return (int) $result['count'];
    }
    
    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    private function emailExists($email) {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Delete user
     * @param int $id
     * @return array
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete user'];
    }
}

<?php
/**
 * Update user passwords to 'password'
 * Run this once: http://localhost/seee/update_password.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/helpers/Database.php';

// Generate hash for 'password'
$newPassword = 'password';
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// Update database
try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "UPDATE users SET password = :password WHERE email IN ('admin@expense.com', 'user@expense.com')";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':password', $hashedPassword);
    
    if ($stmt->execute()) {
        echo "<h2>✅ Success!</h2>";
        echo "<p>Passwords updated successfully for both users.</p>";
        echo "<p><strong>New credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Admin: admin@expense.com / <strong>password</strong></li>";
        echo "<li>User: user@expense.com / <strong>password</strong></li>";
        echo "</ul>";
        echo "<p>Password hash: <code>$hashedPassword</code></p>";
        echo "<hr>";
        echo "<p><a href='public/'>Go to Login Page</a></p>";
        echo "<p style='color:red;'><strong>Important:</strong> Delete this file (update_password.php) after running it!</p>";
    } else {
        echo "❌ Error updating passwords";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

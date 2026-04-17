<?php
/**
 * CSI EduAid Database Configuration
 * File: config/db.php
 */

 // Prevent direct access
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    die('Direct access not allowed.');
}

// ==================== DATABASE CREDENTIALS ====================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Default for XAMPP
define('DB_PASS', '');               // Empty for default XAMPP
define('DB_NAME', 'csi_eduaid');

// ==================== CREATE CONNECTION ====================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die("❌ Database Connection Failed: " . $e->getMessage());
}
?>
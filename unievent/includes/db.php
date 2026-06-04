<?php
// ============================================================
// DATABASE CONFIGURATION — UniEvent IIUM Event Management
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'unievent_db');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: Check if logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper: Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit();
    }
}

// Helper: Get current user
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $id = intval($_SESSION['user_id']);
    $result = $db->query("SELECT * FROM users WHERE id = $id LIMIT 1");
    $user = $result ? $result->fetch_assoc() : null;
    $db->close();
    return $user;
}

// Helper: sanitize input
function clean($str) {
    return htmlspecialchars(strip_tags(trim($str)));
}
?>

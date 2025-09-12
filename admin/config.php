<?php
// Prevent direct access to this file
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    header("Location: index.php");
    exit();
}

// Define constants
define('ADMIN_PATH', __DIR__);
define('ADMIN_URL', '/homestay/admin');
define('ADMIN_ACCESS', true);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . ADMIN_URL . "/login.php");
        exit();
    }
}

<?php
/**
 * Admin Index - Redirect to Login
 * This file ensures that accessing /admin/ redirects to the login page
 */

// Start session
session_start();

// Include authentication middleware to check if user is already logged in
require_once 'backend/api/utils/auth_middleware.php';

// Check if user is already authenticated
if (isAuthenticated()) {
    // Redirect to dashboard if already logged in
    header("Location: pages/dashboard.php");
    exit();
} else {
    // Redirect to login page if not authenticated
    header("Location: pages/login.php");
    exit();
}
?>

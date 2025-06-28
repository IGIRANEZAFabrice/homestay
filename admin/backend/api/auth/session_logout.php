<?php
/**
 * Session Logout API Endpoint
 * Handles user logout via PHP session
 * Used by dashboard.html to logout users
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Include authentication middleware (which defines ADMIN_ACCESS and starts session)
require_once '../utils/auth_middleware.php';

try {
    // Check if user is authenticated before logout
    if (isAuthenticated()) {
        $username = $_SESSION['admin_username'] ?? 'unknown';
        
        // Clear all session data
        session_unset();
        session_destroy();
        
        // Start a new session to send response
        session_start();
        
        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Logout successful'
        ]);
        
        // Log the logout (optional)
        error_log("User {$username} logged out successfully");
        
    } else {
        // User was not authenticated
        echo json_encode([
            'success' => true,
            'message' => 'Already logged out'
        ]);
    }
    
} catch (Exception $e) {
    // Handle errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

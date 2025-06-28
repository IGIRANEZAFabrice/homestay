<?php
/**
 * Session Check API Endpoint
 * Checks if user is authenticated via PHP session
 * Used by dashboard.html to verify authentication
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include authentication middleware (which defines ADMIN_ACCESS and starts session)
require_once '../utils/auth_middleware.php';

try {
    // Check if user is authenticated
    if (isAuthenticated()) {
        // Get user data from session
        $user_data = [
            'id' => $_SESSION['admin_user_id'],
            'username' => $_SESSION['admin_username'],
            'role' => $_SESSION['admin_role'] ?? 'admin',
            'full_name' => $_SESSION['admin_full_name'] ?? $_SESSION['admin_username']
        ];
        
        // Send success response
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'message' => 'User is authenticated',
            'user' => $user_data
        ]);
    } else {
        // User is not authenticated
        echo json_encode([
            'success' => false,
            'authenticated' => false,
            'message' => 'User is not authenticated'
        ]);
    }
    
} catch (Exception $e) {
    // Handle errors
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'authenticated' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

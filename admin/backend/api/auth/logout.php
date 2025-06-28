<?php
/**
 * Admin Logout API Endpoint
 * Handles admin user logout for Virunga Homestay Admin Dashboard
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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

// Include database connection
require_once __DIR__ . '/../../../../include/connection.php';

// Include utility functions
require_once '../utils/auth_utils.php';
require_once '../utils/response_utils.php';

try {
    // Get authorization header
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (empty($auth_header) || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        throw new Exception('Authorization token required');
    }
    
    $token = $matches[1];
    
    // Verify and decode token
    $payload = verifyJWT($token);
    if (!$payload) {
        throw new Exception('Invalid or expired token');
    }
    
    // Get token hash
    $token_hash = hash('sha256', $token);
    
    // Remove session from database
    $stmt = $conn->prepare("DELETE FROM admin_sessions WHERE token_hash = ?");
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    
    // Log logout activity
    logActivity($payload['user_id'], 'logout', 'User logged out successfully');
    
    // Send success response
    sendResponse(true, 'Logged out successfully');
    
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

/**
 * Log user activity
 * @param int $user_id
 * @param string $action
 * @param string $description
 */
function logActivity($user_id, $action, $description) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $conn->prepare("
        INSERT INTO admin_activity_log (user_id, action, description, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("issss", $user_id, $action, $description, $ip_address, $user_agent);
    $stmt->execute();
}
?>

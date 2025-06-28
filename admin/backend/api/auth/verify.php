<?php
/**
 * Admin Token Verification API Endpoint
 * Verifies JWT tokens for Virunga Homestay Admin Dashboard
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
    // Debug logging
    error_log("Verify token request started");

    // Get authorization header
    $headers = getallheaders();
    error_log("Headers: " . json_encode($headers));

    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    error_log("Auth header: " . $auth_header);

    if (empty($auth_header) || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        error_log("No valid authorization header found");
        throw new Exception('Authorization token required');
    }

    $token = $matches[1];
    error_log("Extracted token: " . substr($token, 0, 20) . "...");
    
    // Verify and decode token
    error_log("Verifying JWT token...");
    $payload = verifyJWT($token);
    if (!$payload) {
        error_log("JWT verification failed");
        throw new Exception('Invalid or expired token');
    }
    error_log("JWT payload: " . json_encode($payload));

    // Check if session exists in database
    $token_hash = hash('sha256', $token);
    error_log("Token hash: " . $token_hash);

    $stmt = $conn->prepare("
        SELECT s.id, s.user_id, s.expires_at, u.username, u.email, u.full_name, u.role, u.status
        FROM admin_sessions s
        JOIN admin_users u ON s.user_id = u.id
        WHERE s.token_hash = ? AND s.expires_at > NOW() AND u.status = 'active'
    ");

    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    error_log("Session query result rows: " . $result->num_rows);

    if ($result->num_rows === 0) {
        error_log("No valid session found for token hash");
        throw new Exception('Session not found or expired');
    }
    
    $session_data = $result->fetch_assoc();
    
    // Update session last activity
    $update_stmt = $conn->prepare("UPDATE admin_sessions SET last_activity = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $session_data['id']);
    $update_stmt->execute();
    
    // Prepare user data for response
    $user_data = [
        'id' => $session_data['user_id'],
        'username' => $session_data['username'],
        'email' => $session_data['email'],
        'full_name' => $session_data['full_name'],
        'role' => $session_data['role']
    ];
    
    // Send success response
    sendResponse(true, 'Token is valid', [
        'user' => $user_data,
        'expires_at' => $session_data['expires_at'],
        'token_payload' => $payload
    ]);
    
} catch (Exception $e) {
    error_log("Token verification error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

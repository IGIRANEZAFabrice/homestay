<?php
/**
 * Admin Login API Endpoint
 * Handles admin user authentication for Virunga Homestay Admin Dashboard
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

// Include database connection
require_once __DIR__ . '/../../../../include/connection.php';

// Debug: Check database connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    throw new Exception('Database connection failed');
} else {
    error_log("Database connection successful");
}

// Include utility functions
require_once '../utils/auth_utils.php';
require_once '../utils/response_utils.php';

try {
    // Debug: Log the request
    error_log("Login attempt started");
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Content type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));

    // Get JSON input
    $raw_input = file_get_contents('php://input');
    error_log("Raw input: " . $raw_input);

    $input = json_decode($raw_input, true);
    error_log("Decoded input: " . json_encode($input));

    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    // Validate required fields
    $required_fields = ['username', 'password'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    $username = trim($input['username']);
    $password = $input['password'];
    $remember = isset($input['remember']) ? (bool)$input['remember'] : false;
    
    // Rate limiting check
    if (!checkRateLimit($username)) {
        throw new Exception('Too many login attempts. Please try again later.');
    }
    
    // Query admin user
    $stmt = $conn->prepare("
        SELECT id, username, password, email, full_name, role, status, 
               last_login, created_at, updated_at 
        FROM admin_users 
        WHERE username = ? AND status = 'active'
    ");
    
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Log failed attempt
        logLoginAttempt($username, false, 'User not found');
        throw new Exception('Invalid username or password');
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        // Log failed attempt
        logLoginAttempt($username, false, 'Invalid password');
        throw new Exception('Invalid username or password');
    }
    
    // Generate JWT token
    $token_payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role'],
        'iat' => time(),
        'exp' => time() + ($remember ? 30 * 24 * 60 * 60 : 24 * 60 * 60) // 30 days if remember, 1 day otherwise
    ];
    
    $token = generateJWT($token_payload);
    
    // Update last login
    $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
    $update_stmt->bind_param("i", $user['id']);
    $update_stmt->execute();
    
    // Store session in database
    $session_stmt = $conn->prepare("
        INSERT INTO admin_sessions (user_id, token_hash, expires_at, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $token_hash = hash('sha256', $token);
    $expires_at = date('Y-m-d H:i:s', $token_payload['exp']);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $session_stmt->bind_param("issss", $user['id'], $token_hash, $expires_at, $ip_address, $user_agent);
    $session_stmt->execute();
    
    // Log successful attempt
    logLoginAttempt($username, true, 'Login successful');
    
    // Prepare user data for response (exclude sensitive information)
    $user_data = [
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'full_name' => $user['full_name'],
        'role' => $user['role'],
        'last_login' => $user['last_login']
    ];
    
    // Send success response
    sendResponse(true, 'Login successful', [
        'token' => $token,
        'user' => $user_data,
        'expires_in' => $token_payload['exp'] - time()
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

/**
 * Check rate limiting for login attempts
 * @param string $username
 * @return bool
 */
function checkRateLimit($username) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $time_window = date('Y-m-d H:i:s', time() - 900); // 15 minutes ago
    
    // Check failed attempts in the last 15 minutes
    $stmt = $conn->prepare("
        SELECT COUNT(*) as attempt_count 
        FROM admin_login_attempts 
        WHERE (username = ? OR ip_address = ?) 
        AND success = 0 
        AND attempted_at > ?
    ");
    
    $stmt->bind_param("sss", $username, $ip_address, $time_window);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Allow maximum 5 failed attempts per 15 minutes
    return $row['attempt_count'] < 5;
}

/**
 * Log login attempt
 * @param string $username
 * @param bool $success
 * @param string $message
 */
function logLoginAttempt($username, $success, $message) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $conn->prepare("
        INSERT INTO admin_login_attempts (username, ip_address, user_agent, success, message, attempted_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $success_int = $success ? 1 : 0;
    $stmt->bind_param("sssis", $username, $ip_address, $user_agent, $success_int, $message);
    $stmt->execute();
}
?>

<?php
/**
 * Authentication Utility Functions
 * JWT token handling and authentication helpers for Virunga Homestay Admin Dashboard
 */

// JWT Secret Key - In production, this should be stored in environment variables
define('JWT_SECRET', 'virunga_homestay_admin_secret_key_2024_change_in_production');
define('JWT_ALGORITHM', 'HS256');

/**
 * Generate JWT token
 * @param array $payload Token payload
 * @return string JWT token
 */
function generateJWT($payload) {
    // Header
    $header = json_encode(['typ' => 'JWT', 'alg' => JWT_ALGORITHM]);
    $header = base64UrlEncode($header);
    
    // Payload
    $payload = json_encode($payload);
    $payload = base64UrlEncode($payload);
    
    // Signature
    $signature = hash_hmac('sha256', $header . "." . $payload, JWT_SECRET, true);
    $signature = base64UrlEncode($signature);
    
    return $header . "." . $payload . "." . $signature;
}

/**
 * Verify and decode JWT token
 * @param string $token JWT token
 * @return array|false Token payload or false if invalid
 */
function verifyJWT($token) {
    $parts = explode('.', $token);
    
    if (count($parts) !== 3) {
        return false;
    }
    
    list($header, $payload, $signature) = $parts;
    
    // Verify signature
    $expected_signature = hash_hmac('sha256', $header . "." . $payload, JWT_SECRET, true);
    $expected_signature = base64UrlEncode($expected_signature);
    
    if (!hash_equals($signature, $expected_signature)) {
        return false;
    }
    
    // Decode payload
    $payload_data = json_decode(base64UrlDecode($payload), true);
    
    if (!$payload_data) {
        return false;
    }
    
    // Check expiration
    if (isset($payload_data['exp']) && $payload_data['exp'] < time()) {
        return false;
    }
    
    return $payload_data;
}

/**
 * Base64 URL encode
 * @param string $data Data to encode
 * @return string Encoded data
 */
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Base64 URL decode
 * @param string $data Data to decode
 * @return string Decoded data
 */
function base64UrlDecode($data) {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

/**
 * Get current authenticated user from token
 * @return array|false User data or false if not authenticated
 */
function getCurrentUser() {
    global $conn;
    
    // Get authorization header
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (empty($auth_header) || !preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        return false;
    }
    
    $token = $matches[1];
    
    // Verify token
    $payload = verifyJWT($token);
    if (!$payload) {
        return false;
    }
    
    // Get user from database
    $stmt = $conn->prepare("
        SELECT id, username, email, full_name, role, status 
        FROM admin_users 
        WHERE id = ? AND status = 'active'
    ");
    
    $stmt->bind_param("i", $payload['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Check if user has required role
 * @param string $required_role Required role
 * @param array $user User data (optional, will get current user if not provided)
 * @return bool Has required role
 */
function hasRole($required_role, $user = null) {
    if (!$user) {
        $user = getCurrentUser();
    }
    
    if (!$user) {
        return false;
    }
    
    // Role hierarchy: super_admin > admin > moderator
    $role_hierarchy = [
        'super_admin' => 3,
        'admin' => 2,
        'moderator' => 1
    ];
    
    $user_role_level = $role_hierarchy[$user['role']] ?? 0;
    $required_role_level = $role_hierarchy[$required_role] ?? 0;
    
    return $user_role_level >= $required_role_level;
}

/**
 * Require authentication middleware
 * @param string $required_role Required role (optional)
 * @return array User data
 * @throws Exception If not authenticated or insufficient permissions
 */
function requireAuth($required_role = null) {
    $user = getCurrentUser();
    
    if (!$user) {
        http_response_code(401);
        throw new Exception('Authentication required');
    }
    
    if ($required_role && !hasRole($required_role, $user)) {
        http_response_code(403);
        throw new Exception('Insufficient permissions');
    }
    
    return $user;
}

/**
 * Generate secure random password
 * @param int $length Password length
 * @return string Generated password
 */
function generateSecurePassword($length = 12) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $password;
}

/**
 * Hash password securely
 * @param string $password Plain text password
 * @return string Hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536, // 64 MB
        'time_cost' => 4,       // 4 iterations
        'threads' => 3          // 3 threads
    ]);
}

/**
 * Verify password against hash
 * @param string $password Plain text password
 * @param string $hash Hashed password
 * @return bool Password matches
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    
    return $token;
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool Token is valid
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Clean expired sessions
 * @param PDO $conn Database connection
 */
function cleanExpiredSessions($conn) {
    $stmt = $conn->prepare("DELETE FROM admin_sessions WHERE expires_at < NOW()");
    $stmt->execute();
}

/**
 * Get user permissions
 * @param int $user_id User ID
 * @return array User permissions
 */
function getUserPermissions($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT p.permission_name, p.description
        FROM admin_user_permissions up
        JOIN admin_permissions p ON up.permission_id = p.id
        WHERE up.user_id = ? AND up.granted = 1
    ");
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['permission_name'];
    }
    
    return $permissions;
}

/**
 * Check if user has specific permission
 * @param string $permission Permission name
 * @param int $user_id User ID (optional, will use current user if not provided)
 * @return bool Has permission
 */
function hasPermission($permission, $user_id = null) {
    if (!$user_id) {
        $user = getCurrentUser();
        if (!$user) {
            return false;
        }
        $user_id = $user['id'];
    }
    
    $permissions = getUserPermissions($user_id);
    return in_array($permission, $permissions);
}

/**
 * Log security event
 * @param string $event_type Event type
 * @param string $description Event description
 * @param int $user_id User ID (optional)
 */
function logSecurityEvent($event_type, $description, $user_id = null) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $stmt = $conn->prepare("
        INSERT INTO admin_security_log (user_id, event_type, description, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("issss", $user_id, $event_type, $description, $ip_address, $user_agent);
    $stmt->execute();
}
?>

<?php
/**
 * Authentication Middleware
 * Handles session-based authentication for admin pages
 * Provides simple, secure authentication without JWT complexity
 */

// Define admin access constant if not already defined
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 * @return bool True if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['admin_user_id']) && 
           isset($_SESSION['admin_username']) && 
           !empty($_SESSION['admin_user_id']);
}

/**
 * Get current authenticated user data
 * @return array|null User data or null if not authenticated
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    // Include database connection
    require_once __DIR__ . '/../../database/connection.php';
    
    $user_id = $_SESSION['admin_user_id'];
    
    // Get user data from database
    $query = "SELECT id, username, email, full_name, role, status, last_login 
              FROM admin_users 
              WHERE id = ? AND status = 'active'";
    
    $user = getSingleRow($query, 'i', [$user_id]);
    
    return $user;
}

/**
 * Require authentication - redirect to login if not authenticated
 * @param string $redirect_url URL to redirect to after login
 */
function requireAuth($redirect_url = null) {
    if (!isAuthenticated()) {
        // Store the intended URL for redirect after login
        if ($redirect_url) {
            $_SESSION['redirect_after_login'] = $redirect_url;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        // Redirect to login page
        header('Location: /homestay/admin/pages/login.php');
        exit();
    }
}

/**
 * Check if user has required role
 * @param string $required_role Required role (super_admin, admin, moderator)
 * @return bool True if user has required role or higher
 */
function hasRole($required_role) {
    $user = getCurrentUser();
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
 * Require specific role - redirect with error if insufficient permissions
 * @param string $required_role Required role
 */
function requireRole($required_role) {
    requireAuth(); // First ensure user is authenticated
    
    if (!hasRole($required_role)) {
        $_SESSION['error_message'] = 'Insufficient permissions to access this page.';
        header('Location: /admin/pages/dashboard.php');
        exit();
    }
}

/**
 * Login user with username and password
 * @param string $username Username
 * @param string $password Password
 * @return array Result array with success status and message
 */
function loginUser($username, $password) {
    // Include database connection
    require_once __DIR__ . '/../../database/connection.php';
    
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password are required.'];
    }
    
    // Get user from database
    $query = "SELECT id, username, password, email, full_name, role, status 
              FROM admin_users 
              WHERE username = ? AND status = 'active'";
    
    $user = getSingleRow($query, 's', [$username]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }
    
    // Verify password using password_verify for hashed passwords
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid username or password.'];
    }
    
    // Set session variables
    $_SESSION['admin_user_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['admin_role'] = $user['role'];
    $_SESSION['admin_full_name'] = $user['full_name'];
    
    // Update last login time
    $update_query = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
    updateData($update_query, 'i', [$user['id']]);
    
    return ['success' => true, 'message' => 'Login successful.', 'user' => $user];
}

/**
 * Logout current user
 */
function logoutUser() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 * @param mixed $data Data to sanitize
 * @return mixed Sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}



/**
 * Generate secure random string
 * @param int $length Length of string
 * @return string Random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Log admin activity
 * @param string $action Action performed
 * @param string $details Additional details
 */
function logActivity($action, $details = '') {
    $user = getCurrentUser();
    if (!$user) return;

    try {
        // Include database connection
        require_once __DIR__ . '/../../database/connection.php';

        $query = "INSERT INTO admin_activity_log (user_id, action, description, ip_address, user_agent, created_at)
                  VALUES (?, ?, ?, ?, ?, NOW())";

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        insertData($query, 'issss', [
            $user['id'],
            $action,
            $details,
            $ip_address,
            $user_agent
        ]);
    } catch (Exception $e) {
        // Log the error but don't break the main functionality
        error_log('Failed to log activity: ' . $e->getMessage());
    }
}
?>

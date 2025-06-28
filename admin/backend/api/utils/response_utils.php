<?php
/**
 * Response Utility Functions
 * Standardized API response helpers for Virunga Homestay Admin Dashboard
 */

/**
 * Send standardized JSON response
 * @param bool $success Success status
 * @param string $message Response message
 * @param mixed $data Response data (optional)
 * @param int $http_code HTTP status code (optional)
 */
function sendResponse($success, $message, $data = null, $http_code = null) {
    // Set appropriate HTTP status code
    if ($http_code) {
        http_response_code($http_code);
    } elseif (!$success) {
        http_response_code(400);
    } else {
        http_response_code(200);
    }
    
    // Prepare response array
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s'),
        'request_id' => generateRequestId()
    ];
    
    // Add data if provided
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    // Send JSON response
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Send success response
 * @param string $message Success message
 * @param mixed $data Response data (optional)
 */
function sendSuccess($message, $data = null) {
    sendResponse(true, $message, $data, 200);
}

/**
 * Send error response
 * @param string $message Error message
 * @param mixed $data Error data (optional)
 * @param int $http_code HTTP status code (default: 400)
 */
function sendError($message, $data = null, $http_code = 400) {
    sendResponse(false, $message, $data, $http_code);
}

/**
 * Send validation error response
 * @param array $errors Validation errors
 * @param string $message Main error message (optional)
 */
function sendValidationError($errors, $message = 'Validation failed') {
    sendResponse(false, $message, ['validation_errors' => $errors], 422);
}

/**
 * Send unauthorized response
 * @param string $message Error message (optional)
 */
function sendUnauthorized($message = 'Unauthorized access') {
    sendResponse(false, $message, null, 401);
}

/**
 * Send forbidden response
 * @param string $message Error message (optional)
 */
function sendForbidden($message = 'Access forbidden') {
    sendResponse(false, $message, null, 403);
}

/**
 * Send not found response
 * @param string $message Error message (optional)
 */
function sendNotFound($message = 'Resource not found') {
    sendResponse(false, $message, null, 404);
}

/**
 * Send method not allowed response
 * @param array $allowed_methods Allowed HTTP methods
 */
function sendMethodNotAllowed($allowed_methods = []) {
    if (!empty($allowed_methods)) {
        header('Allow: ' . implode(', ', $allowed_methods));
    }
    sendResponse(false, 'Method not allowed', null, 405);
}

/**
 * Send internal server error response
 * @param string $message Error message (optional)
 */
function sendInternalError($message = 'Internal server error') {
    sendResponse(false, $message, null, 500);
}

/**
 * Send paginated response
 * @param array $items Items array
 * @param int $total Total items count
 * @param int $page Current page
 * @param int $per_page Items per page
 * @param string $message Success message (optional)
 */
function sendPaginatedResponse($items, $total, $page, $per_page, $message = 'Data retrieved successfully') {
    $total_pages = ceil($total / $per_page);
    
    $pagination = [
        'current_page' => $page,
        'per_page' => $per_page,
        'total_items' => $total,
        'total_pages' => $total_pages,
        'has_next_page' => $page < $total_pages,
        'has_prev_page' => $page > 1
    ];
    
    $data = [
        'items' => $items,
        'pagination' => $pagination
    ];
    
    sendSuccess($message, $data);
}

/**
 * Generate unique request ID
 * @return string Request ID
 */
function generateRequestId() {
    return uniqid('req_', true);
}

/**
 * Validate required fields in input data
 * @param array $data Input data
 * @param array $required_fields Required field names
 * @return array Validation errors (empty if valid)
 */
function validateRequiredFields($data, $required_fields) {
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            $errors[$field] = "Field '$field' is required";
        }
    }
    
    return $errors;
}



/**
 * Validate URL format
 * @param string $url URL to validate
 * @return bool Is valid URL
 */
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate integer value
 * @param mixed $value Value to validate
 * @param int $min Minimum value (optional)
 * @param int $max Maximum value (optional)
 * @return bool Is valid integer
 */
function validateInteger($value, $min = null, $max = null) {
    if (!is_numeric($value) || (int)$value != $value) {
        return false;
    }
    
    $int_value = (int)$value;
    
    if ($min !== null && $int_value < $min) {
        return false;
    }
    
    if ($max !== null && $int_value > $max) {
        return false;
    }
    
    return true;
}

/**
 * Validate string length
 * @param string $value String to validate
 * @param int $min_length Minimum length (optional)
 * @param int $max_length Maximum length (optional)
 * @return bool Is valid length
 */
function validateStringLength($value, $min_length = null, $max_length = null) {
    if (!is_string($value)) {
        return false;
    }
    
    $length = strlen($value);
    
    if ($min_length !== null && $length < $min_length) {
        return false;
    }
    
    if ($max_length !== null && $length > $max_length) {
        return false;
    }
    
    return true;
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
    
    if (is_string($data)) {
        // Remove null bytes
        $data = str_replace("\0", '', $data);
        
        // Trim whitespace
        $data = trim($data);
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    return $data;
}

/**
 * Get client IP address
 * @return string Client IP address
 */
function getClientIpAddress() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            
            // Handle comma-separated IPs (from proxies)
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            
            // Validate IP address
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Get user agent string
 * @return string User agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
}

/**
 * Log API request
 * @param string $endpoint API endpoint
 * @param string $method HTTP method
 * @param array $data Request data (optional)
 * @param int $user_id User ID (optional)
 */
function logApiRequest($endpoint, $method, $data = null, $user_id = null) {
    global $conn;
    
    if (!$conn) {
        return;
    }
    
    $ip_address = getClientIpAddress();
    $user_agent = getUserAgent();
    $request_data = $data ? json_encode($data) : null;
    
    $stmt = $conn->prepare("
        INSERT INTO admin_api_log (user_id, endpoint, method, request_data, ip_address, user_agent, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->bind_param("isssss", $user_id, $endpoint, $method, $request_data, $ip_address, $user_agent);
    $stmt->execute();
}

/**
 * Format database error for response
 * @param string $error Database error message
 * @return string Formatted error message
 */
function formatDatabaseError($error) {
    // Don't expose sensitive database information in production
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        return 'A database error occurred. Please try again later.';
    }
    
    return $error;
}

/**
 * Check if request is AJAX
 * @return bool Is AJAX request
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get request method
 * @return string HTTP method
 */
function getRequestMethod() {
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
}

/**
 * Get JSON input from request body
 * @return array|null Decoded JSON data or null if invalid
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}
?>

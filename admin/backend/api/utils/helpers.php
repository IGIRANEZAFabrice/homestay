<?php
/**
 * Helper Utilities
 * Common helper functions for Virunga Homestay Admin Dashboard
 */

// Define admin access constant if not already defined
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

/**
 * Send JSON response
 * @param bool $success Success status
 * @param string $message Response message
 * @param mixed $data Additional data
 * @param int $http_code HTTP status code
 */
function sendJSONResponse($success, $message, $data = null, $http_code = 200) {
    http_response_code($http_code);
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit();
}

/**
 * Redirect with message
 * @param string $url URL to redirect to
 * @param string $message Message to display
 * @param string $type Message type (success, error, warning, info)
 */
function redirectWithMessage($url, $message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Get and clear flash message
 * @return array|null Flash message data or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $message;
    }
    
    return null;
}

/**
 * Format file size in human readable format
 * @param int $bytes File size in bytes
 * @param int $precision Decimal precision
 * @return string Formatted file size
 */
function formatFileSize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'M j, Y') {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 * @param string $datetime Datetime string
 * @param string $format Output format
 * @return string Formatted datetime
 */
function formatDateTime($datetime, $format = 'M j, Y g:i A') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    
    return date($format, strtotime($datetime));
}

/**
 * Truncate text to specified length
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to append
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate pagination HTML
 * @param int $current_page Current page number
 * @param int $total_pages Total number of pages
 * @param string $base_url Base URL for pagination links
 * @param array $params Additional URL parameters
 * @return string Pagination HTML
 */
function generatePagination($current_page, $total_pages, $base_url, $params = []) {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Build query string
    $query_params = $params;
    $query_string = !empty($query_params) ? '&' . http_build_query($query_params) : '';
    
    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $prev_page . $query_string . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=1' . $query_string . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $i . $query_string . '">' . $i . '</a></li>';
        }
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $total_pages . $query_string . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $html .= '<li class="page-item"><a class="page-link" href="' . $base_url . '?page=' . $next_page . $query_string . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Get status badge HTML
 * @param bool $is_active Status value
 * @param string $active_text Text for active status
 * @param string $inactive_text Text for inactive status
 * @return string Status badge HTML
 */
function getStatusBadge($is_active, $active_text = 'Active', $inactive_text = 'Inactive') {
    if ($is_active) {
        return '<span class="badge badge-success">' . $active_text . '</span>';
    } else {
        return '<span class="badge badge-secondary">' . $inactive_text . '</span>';
    }
}

/**
 * Get rating stars HTML
 * @param int $rating Rating value (1-5)
 * @return string Rating stars HTML
 */
function getRatingStars($rating) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $html .= '<i class="fas fa-star text-warning"></i>';
        } else {
            $html .= '<i class="far fa-star text-muted"></i>';
        }
    }
    return $html;
}

/**
 * Generate breadcrumb HTML
 * @param array $breadcrumbs Array of breadcrumb items
 * @return string Breadcrumb HTML
 */
function generateBreadcrumb($breadcrumbs) {
    if (empty($breadcrumbs)) {
        return '';
    }
    
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($breadcrumbs);
    foreach ($breadcrumbs as $index => $crumb) {
        if ($index === $total - 1) {
            // Last item (current page)
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($crumb['title']) . '</li>';
        } else {
            // Linked items
            $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['title']) . '</a></li>';
        }
    }
    
    $html .= '</ol></nav>';
    
    return $html;
}

/**
 * Clean and validate input data
 * @param mixed $data Input data
 * @return mixed Cleaned data
 */
function cleanInput($data) {
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if string contains only allowed characters
 * @param string $string String to check
 * @param string $pattern Regex pattern for allowed characters
 * @return bool True if valid, false otherwise
 */
function isValidString($string, $pattern = '/^[a-zA-Z0-9\s\-_.,!?]+$/') {
    return preg_match($pattern, $string);
}

/**
 * Generate random token
 * @param int $length Token length
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Log error message
 * @param string $message Error message
 * @param array $context Additional context
 */
function logError($message, $context = []) {
    $log_message = date('Y-m-d H:i:s') . ' - ' . $message;
    
    if (!empty($context)) {
        $log_message .= ' - Context: ' . json_encode($context);
    }
    
    error_log($log_message);
}

/**
 * Get client IP address
 * @return string Client IP address
 */
function getClientIP() {
    $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Convert array to CSV string
 * @param array $data Array data
 * @param array $headers Column headers
 * @return string CSV string
 */
function arrayToCSV($data, $headers = []) {
    $output = fopen('php://temp', 'r+');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

/**
 * Check if request is AJAX
 * @return bool True if AJAX request
 */
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get table statistics
 * @param string $table_name Table name
 * @return array Table statistics
 */
function getTableStats($table_name) {
    // Include database connection
    require_once __DIR__ . '/../../database/connection.php';
    
    $stats = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0
    ];
    
    // Get total count
    $total_result = getSingleRow("SELECT COUNT(*) as count FROM `$table_name`");
    $stats['total'] = $total_result['count'] ?? 0;
    
    // Get active/inactive counts if table has is_active column
    $columns_result = getMultipleRows("SHOW COLUMNS FROM `$table_name` LIKE 'is_active'");
    if (!empty($columns_result)) {
        $active_result = getSingleRow("SELECT COUNT(*) as count FROM `$table_name` WHERE is_active = 1");
        $stats['active'] = $active_result['count'] ?? 0;
        $stats['inactive'] = $stats['total'] - $stats['active'];
    }
    
    return $stats;
}
?>

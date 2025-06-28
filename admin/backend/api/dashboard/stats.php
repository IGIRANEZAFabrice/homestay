<?php
/**
 * Dashboard Statistics API Endpoint
 * Provides dashboard statistics for Virunga Homestay Admin Dashboard
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Include database connection
require_once '../../../include/connection.php';

// Include utility functions
require_once '../utils/auth_utils.php';
require_once '../utils/response_utils.php';

try {
    // Require authentication
    $user = requireAuth();
    
    // Log API request
    logApiRequest('/admin/backend/api/dashboard/stats.php', 'GET', null, $user['id']);
    
    // Get statistics from database
    $stats = [];
    
    // Get total activities
    $result = $conn->query("SELECT COUNT(*) as count FROM activities WHERE status = 'active'");
    $stats['totalActivities'] = $result->fetch_assoc()['count'];
    
    // Get total blog posts
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE status = 'published'");
    $stats['totalBlogs'] = $result->fetch_assoc()['count'];
    
    // Get total cars
    $result = $conn->query("SELECT COUNT(*) as count FROM cars WHERE status = 'active'");
    $stats['totalCars'] = $result->fetch_assoc()['count'];
    
    // Get total reviews
    $result = $conn->query("SELECT COUNT(*) as count FROM reviews WHERE status = 'approved'");
    $stats['totalReviews'] = $result->fetch_assoc()['count'];
    
    // Get total rooms
    $result = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE status = 'active'");
    $stats['totalRooms'] = $result->fetch_assoc()['count'];
    
    // Get total services
    $result = $conn->query("SELECT COUNT(*) as count FROM services WHERE status = 'active'");
    $stats['totalServices'] = $result->fetch_assoc()['count'];
    
    // Get total events
    $result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'active'");
    $stats['totalEvents'] = $result->fetch_assoc()['count'];
    
    // Get total messages (unread)
    $result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'");
    $stats['totalMessages'] = $result->fetch_assoc()['count'];
    
    // Get total hero images
    $result = $conn->query("SELECT COUNT(*) as count FROM hero_images WHERE status = 'active'");
    $stats['totalHeroImages'] = $result->fetch_assoc()['count'];
    
    // Get additional statistics
    $stats['totalUsers'] = getTotalUsers($conn);
    $stats['monthlyStats'] = getMonthlyStats($conn);
    $stats['recentActivity'] = getRecentActivityCount($conn);
    
    // Send success response
    sendSuccess('Statistics retrieved successfully', $stats);
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    sendError($e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

/**
 * Get total users count
 * @param mysqli $conn Database connection
 * @return int Total users count
 */
function getTotalUsers($conn) {
    $result = $conn->query("SELECT COUNT(*) as count FROM admin_users WHERE status = 'active'");
    return $result->fetch_assoc()['count'];
}

/**
 * Get monthly statistics
 * @param mysqli $conn Database connection
 * @return array Monthly statistics
 */
function getMonthlyStats($conn) {
    $current_month = date('Y-m');
    $stats = [];
    
    // New activities this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM activities 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'active'
    ");
    $stmt->bind_param("s", $current_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['newActivities'] = $result->fetch_assoc()['count'];
    
    // New blog posts this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM blogs 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'published'
    ");
    $stmt->bind_param("s", $current_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['newBlogs'] = $result->fetch_assoc()['count'];
    
    // New reviews this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM reviews 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ? AND status = 'approved'
    ");
    $stmt->bind_param("s", $current_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['newReviews'] = $result->fetch_assoc()['count'];
    
    // New messages this month
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM contact_messages 
        WHERE DATE_FORMAT(created_at, '%Y-%m') = ?
    ");
    $stmt->bind_param("s", $current_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['newMessages'] = $result->fetch_assoc()['count'];
    
    return $stats;
}

/**
 * Get recent activity count (last 7 days)
 * @param mysqli $conn Database connection
 * @return int Recent activity count
 */
function getRecentActivityCount($conn) {
    $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
    
    $total_activity = 0;
    
    // Count recent activities
    $tables = [
        'activities' => 'created_at',
        'blogs' => 'created_at',
        'cars' => 'created_at',
        'reviews' => 'created_at',
        'events' => 'created_at',
        'contact_messages' => 'created_at'
    ];
    
    foreach ($tables as $table => $date_column) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table WHERE $date_column >= ?");
        $stmt->bind_param("s", $seven_days_ago);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_activity += $result->fetch_assoc()['count'];
    }
    
    return $total_activity;
}
?>

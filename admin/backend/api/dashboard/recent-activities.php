<?php
/**
 * Dashboard Recent Activities API Endpoint
 * Provides recent activities for Virunga Homestay Admin Dashboard
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
    logApiRequest('/admin/backend/api/dashboard/recent-activities.php', 'GET', null, $user['id']);
    
    // Get limit from query parameter (default: 10, max: 50)
    $limit = isset($_GET['limit']) ? min(max(1, (int)$_GET['limit']), 50) : 10;
    
    // Get recent activities from multiple tables
    $activities = [];
    
    // Get recent blog posts
    $blog_activities = getBlogActivities($conn, $limit);
    $activities = array_merge($activities, $blog_activities);
    
    // Get recent reviews
    $review_activities = getReviewActivities($conn, $limit);
    $activities = array_merge($activities, $review_activities);
    
    // Get recent activities
    $activity_activities = getActivityActivities($conn, $limit);
    $activities = array_merge($activities, $activity_activities);
    
    // Get recent car additions
    $car_activities = getCarActivities($conn, $limit);
    $activities = array_merge($activities, $car_activities);
    
    // Get recent events
    $event_activities = getEventActivities($conn, $limit);
    $activities = array_merge($activities, $event_activities);
    
    // Get recent messages
    $message_activities = getMessageActivities($conn, $limit);
    $activities = array_merge($activities, $message_activities);
    
    // Sort by timestamp (most recent first)
    usort($activities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    
    // Limit to requested number
    $activities = array_slice($activities, 0, $limit);
    
    // Send success response
    sendSuccess('Recent activities retrieved successfully', $activities);
    
} catch (Exception $e) {
    error_log("Dashboard recent activities error: " . $e->getMessage());
    sendError($e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

/**
 * Get recent blog activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Blog activities
 */
function getBlogActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT title, created_at, status
        FROM blogs 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'type' => 'blog',
            'title' => 'New blog post: "' . htmlspecialchars($row['title']) . '"',
            'time' => $row['created_at'],
            'icon' => 'fas fa-blog',
            'status' => $row['status']
        ];
    }
    
    return $activities;
}

/**
 * Get recent review activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Review activities
 */
function getReviewActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT customer_name, rating, created_at, status
        FROM reviews 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $stars = str_repeat('⭐', $row['rating']);
        $activities[] = [
            'type' => 'review',
            'title' => 'New ' . $row['rating'] . '-star review from ' . htmlspecialchars($row['customer_name']),
            'time' => $row['created_at'],
            'icon' => 'fas fa-star',
            'status' => $row['status']
        ];
    }
    
    return $activities;
}

/**
 * Get recent activity activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Activity activities
 */
function getActivityActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT title, created_at, status
        FROM activities 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'type' => 'activity',
            'title' => 'New activity added: "' . htmlspecialchars($row['title']) . '"',
            'time' => $row['created_at'],
            'icon' => 'fas fa-hiking',
            'status' => $row['status']
        ];
    }
    
    return $activities;
}

/**
 * Get recent car activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Car activities
 */
function getCarActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT make, model, created_at, status
        FROM cars 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'type' => 'car',
            'title' => 'New car added: ' . htmlspecialchars($row['make'] . ' ' . $row['model']),
            'time' => $row['created_at'],
            'icon' => 'fas fa-car',
            'status' => $row['status']
        ];
    }
    
    return $activities;
}

/**
 * Get recent event activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Event activities
 */
function getEventActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT title, created_at, status, event_date
        FROM events 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'type' => 'event',
            'title' => 'New event created: "' . htmlspecialchars($row['title']) . '"',
            'time' => $row['created_at'],
            'icon' => 'fas fa-calendar-alt',
            'status' => $row['status'],
            'event_date' => $row['event_date']
        ];
    }
    
    return $activities;
}

/**
 * Get recent message activities
 * @param mysqli $conn Database connection
 * @param int $limit Limit number of results
 * @return array Message activities
 */
function getMessageActivities($conn, $limit) {
    $stmt = $conn->prepare("
        SELECT name, email, subject, created_at, status
        FROM contact_messages 
        ORDER BY created_at DESC 
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'type' => 'message',
            'title' => 'New message from ' . htmlspecialchars($row['name']) . ': "' . htmlspecialchars($row['subject']) . '"',
            'time' => $row['created_at'],
            'icon' => 'fas fa-envelope',
            'status' => $row['status']
        ];
    }
    
    return $activities;
}
?>

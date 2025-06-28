<?php
/**
 * Reviews Management - Quick Actions
 * AJAX endpoint for review moderation actions
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Include database connection and helpers
require_once '../../backend/database/connection.php';
require_once '../../backend/api/utils/helpers.php';

// Set JSON response header
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(false, 'Method not allowed.');
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['review_id']) || !isset($input['action'])) {
    sendJSONResponse(false, 'Invalid input data.');
}

$review_id = intval($input['review_id']);
$action = $input['action'];

// Validate review ID
if ($review_id <= 0) {
    sendJSONResponse(false, 'Invalid review ID.');
}

// Validate action
$allowed_actions = ['approve', 'reject', 'feature', 'unfeature'];
if (!in_array($action, $allowed_actions)) {
    sendJSONResponse(false, 'Invalid action.');
}

// Get review data
$review = getSingleRow("SELECT * FROM reviews WHERE id = ?", 'i', [$review_id]);

if (!$review) {
    sendJSONResponse(false, 'Review not found.');
}

try {
    $query = '';
    $params = [];
    $param_types = '';
    $activity_message = '';
    
    switch ($action) {
        case 'approve':
            $query = "UPDATE reviews SET is_active = 1, updated_at = NOW() WHERE id = ?";
            $params = [$review_id];
            $param_types = 'i';
            $activity_message = "Approved review from {$review['name']}";
            break;

        case 'reject':
            $query = "UPDATE reviews SET is_active = 0, is_featured = 0, updated_at = NOW() WHERE id = ?";
            $params = [$review_id];
            $param_types = 'i';
            $activity_message = "Rejected review from {$review['name']}";
            break;

        case 'feature':
            // Only active reviews can be featured
            if (!$review['is_active']) {
                sendJSONResponse(false, 'Only active reviews can be featured.');
            }
            $query = "UPDATE reviews SET is_featured = 1, updated_at = NOW() WHERE id = ?";
            $params = [$review_id];
            $param_types = 'i';
            $activity_message = "Featured review from {$review['name']}";
            break;

        case 'unfeature':
            $query = "UPDATE reviews SET is_featured = 0, updated_at = NOW() WHERE id = ?";
            $params = [$review_id];
            $param_types = 'i';
            $activity_message = "Removed featured status from review by {$review['name']}";
            break;
    }
    
    // Execute the update
    $result = updateData($query, $param_types, $params);
    
    if ($result !== false) {
        // Log activity (non-critical operation)
        try {
            if (function_exists('logActivity')) {
                logActivity('review_' . $action, $activity_message);
            }
        } catch (Exception $log_error) {
            error_log('Activity logging failed: ' . $log_error->getMessage());
            // Don't let logging failure break the action
        }

        sendJSONResponse(true, ucfirst($action) . ' action completed successfully.');
    } else {
        sendJSONResponse(false, 'Failed to perform action.');
    }
    
} catch (Exception $e) {
    error_log('Error performing review action: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred while performing the action.');
}
?>

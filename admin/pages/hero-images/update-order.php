<?php
/**
 * Hero Images Management - Update Display Order
 * AJAX endpoint for updating hero image display order
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

if (!$input || !isset($input['order']) || !is_array($input['order'])) {
    sendJSONResponse(false, 'Invalid input data.');
}

$order_data = $input['order'];

// Validate order data
foreach ($order_data as $item) {
    if (!isset($item['id']) || !isset($item['order']) || !is_numeric($item['id']) || !is_numeric($item['order'])) {
        sendJSONResponse(false, 'Invalid order data format.');
    }
}

try {
    // Start transaction
    $conn = getConnection();
    $conn->begin_transaction();
    
    // Update each hero image's display order
    $update_query = "UPDATE hero_images SET display_order = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    foreach ($order_data as $item) {
        $stmt->bind_param('ii', $item['order'], $item['id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update order for ID ' . $item['id'] . ': ' . $stmt->error);
        }
    }
    
    $stmt->close();
    
    // Commit transaction
    $conn->commit();

    // Log activity (non-critical operation)
    try {
        if (function_exists('logActivity')) {
            logActivity('update_hero_images_order', 'Updated hero images display order');
        }
    } catch (Exception $log_error) {
        error_log('Activity logging failed: ' . $log_error->getMessage());
        // Don't let logging failure break the update operation
    }

    sendJSONResponse(true, 'Display order updated successfully.');
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    error_log('Error updating hero images order: ' . $e->getMessage());
    sendJSONResponse(false, 'Failed to update display order.');
}
?>

<?php
/**
 * Contact Messages Quick Actions
 * AJAX endpoint for quick message status updates
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

if (!$input) {
    sendJSONResponse(false, 'Invalid JSON input.');
}

$action = $input['action'] ?? '';
$message_id = intval($input['message_id'] ?? 0);

if (!$message_id) {
    sendJSONResponse(false, 'Invalid message ID.');
}

// Verify message exists
$message = getSingleRow("SELECT * FROM contact_messages WHERE id = ?", 'i', [$message_id]);

if (!$message) {
    sendJSONResponse(false, 'Message not found.');
}

try {
    switch ($action) {
        case 'update_status':
            $status = $input['status'] ?? '';
            
            // Validate status
            $valid_statuses = ['new', 'read', 'replied'];
            if (!in_array($status, $valid_statuses)) {
                sendJSONResponse(false, 'Invalid status.');
            }
            
            // Update message status
            $query = "UPDATE contact_messages SET status = ? WHERE id = ?";
            $result = updateData($query, 'si', [$status, $message_id]);
            
            if ($result !== false) {
                // Log activity
                logActivity('update_message_status', "Updated message status to {$status} for message from {$message['name']}");
                
                sendJSONResponse(true, 'Message status updated successfully.');
            } else {
                sendJSONResponse(false, 'Failed to update message status.');
            }
            break;
            
        default:
            sendJSONResponse(false, 'Invalid action.');
    }
    
} catch (Exception $e) {
    error_log('Error in contact messages quick actions: ' . $e->getMessage());
    sendJSONResponse(false, 'An error occurred while processing the request.');
}
?>

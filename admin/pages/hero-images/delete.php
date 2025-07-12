<?php
/**
 * Hero Images Management - Delete Hero Image
 * Direct deletion without confirmation page
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

// Get current user
$current_user = getCurrentUser();

// Get hero image ID
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$image_id) {
    redirectWithMessage('index.php', 'Invalid hero image ID.', 'danger');
}

// Get hero image data
$hero_image = getSingleRow("SELECT * FROM hero_images WHERE id = ?", 'i', [$image_id]);

if (!$hero_image) {
    redirectWithMessage('index.php', 'Hero image not found.', 'danger');
}

// Perform deletion directly (no confirmation page)
try {
    // Start transaction
    $conn = getConnection();
    $conn->begin_transaction();

    // Delete the hero image from database
    $result = deleteData("DELETE FROM hero_images WHERE id = ?", 'i', [$image_id]);

    if ($result > 0) {
        // Delete associated image file if exists
        if (!empty($hero_image['image'])) {
            // Construct full path for deletion - handle both old and new formats
            $image_path = (strpos($hero_image['image'], 'uploads/') === 0)
                ? $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $hero_image['image']
                : $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/hero/' . $hero_image['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Commit the deletion first
        $conn->commit();

        // Reorder remaining hero images to fill the gap (non-critical operation)
        try {
            $remaining_images = getMultipleRows("SELECT id FROM hero_images ORDER BY display_order ASC");
            if ($remaining_images && is_array($remaining_images)) {
                $update_query = "UPDATE hero_images SET display_order = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($update_query);

                if ($stmt) {
                    foreach ($remaining_images as $index => $image) {
                        $new_order = $index + 1;
                        $stmt->bind_param('ii', $new_order, $image['id']);
                        $stmt->execute();
                    }
                    $stmt->close();
                }
            }
        } catch (Exception $reorder_error) {
            error_log('Hero image reordering failed: ' . $reorder_error->getMessage());
            // Don't let reordering failure break the delete operation
        }

        // Log activity (non-critical operation)
        try {
            if (function_exists('logActivity')) {
                logActivity('delete_hero_image', "Deleted hero image: {$hero_image['title']}");
            }
        } catch (Exception $log_error) {
            error_log('Activity logging failed: ' . $log_error->getMessage());
            // Don't let logging failure break the delete operation
        }

        redirectWithMessage('index.php', 'Hero image deleted successfully!', 'success');
    } else {
        $conn->rollback();
        redirectWithMessage('index.php', 'Failed to delete hero image.', 'danger');
    }

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    error_log('Error deleting hero image: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    redirectWithMessage('index.php', 'An error occurred while deleting the hero image.', 'danger');
}

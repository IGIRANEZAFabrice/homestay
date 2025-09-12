<?php
/**
 * Custom Image Upload Handler
 * Handles image uploads from custom rich text editor
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once 'auth_middleware.php';

// Require authentication
requireAuth();

// Include image handler utilities
require_once 'image-handler.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file parameter received']);
    exit;
}

if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $error_messages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];

    $error_message = $error_messages[$_FILES['file']['error']] ?? 'Unknown upload error';
    http_response_code(400);
    echo json_encode(['error' => $error_message]);
    exit;
}

try {
    // Upload the image (path relative to project root)
    $upload_result = uploadImage($_FILES['file'], 'uploads/blog/', [
        'max_width' => 1200,
        'max_height' => 1200,
        'quality' => 90
    ]);
    
    if ($upload_result['success']) {
        // Return the image URL in the format expected by the editor
        // Only use the filename, not the full path, to match how images are stored in the database
        // This works with buildImageUrl() which expects just the filename
        echo json_encode([
            'location' => '/homestay/uploads/blog/' . $upload_result['filename']
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $upload_result['message']]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
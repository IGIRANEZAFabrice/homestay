<?php
/**
 * TinyMCE Image Upload Handler
 * Handles image uploads from TinyMCE editor
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
    $upload_result = uploadImage($_FILES['file'], 'uploads/blog-content/', [
        'max_width' => 1200,
        'max_height' => 800,
        'quality' => 85,
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp']
    ]);
    
    if ($upload_result['success']) {
        // Return the image URL for TinyMCE
        // The uploadImage function returns path like 'uploads/blog-content/filename.jpg'
        // Use absolute path from web root for better compatibility
        $image_url = '/homestay/' . $upload_result['path'];

        // Debug log
        error_log('TinyMCE upload success - Original path: ' . $upload_result['path'] . ', Final URL: ' . $image_url);

        echo json_encode([
            'location' => $image_url
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => $upload_result['message']]);
    }
    
} catch (Exception $e) {
    error_log('TinyMCE image upload error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload image: ' . $e->getMessage()]);
}

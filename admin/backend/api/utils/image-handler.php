<?php
/**
 * Image Handler Utility
 * Handles secure image uploads, resizing, and management
 * For Virunga Homestay Admin Dashboard
 */

// Define admin access constant if not already defined
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

/**
 * Upload and process image file
 * @param array $file $_FILES array element
 * @param string $upload_dir Upload directory (relative to project root)
 * @param array $options Upload options
 * @return array Result array with success status and file info
 */
function uploadImage($file, $upload_dir = 'uploads/', $options = []) {
    // Check if GD extension is available
    if (!extension_loaded('gd')) {
        return uploadImageSimple($file, $upload_dir, $options);
    }

    // Check what image formats are supported
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

    $default_options = [
        'max_size' => 20 * 1024 * 1024, // 20MB
        'allowed_types' => $allowed_types,
        'max_width' => 2000,
        'max_height' => 2000,
        'create_thumbnail' => false,
        'thumbnail_width' => 300,
        'thumbnail_height' => 300,
        'quality' => 85
    ];
    
    $options = array_merge($default_options, $options);
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded.'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error: ' . getUploadErrorMessage($file['error'])];
    }
    
    // Validate file size
    if ($file['size'] > $options['max_size']) {
        $max_mb = round($options['max_size'] / (1024 * 1024), 2);
        return ['success' => false, 'message' => "File size exceeds maximum allowed size of {$max_mb}MB."];
    }
    
    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    if (!in_array($file_extension, $options['allowed_types'])) {
        $allowed = implode(', ', $options['allowed_types']);
        return ['success' => false, 'message' => "Invalid file type. Allowed types: {$allowed}"];
    }
    
    // Validate image using getimagesize
    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return ['success' => false, 'message' => 'Invalid image file.'];
    }
    
    // Create upload directory if it doesn't exist
    $full_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . trim($upload_dir, '/') . '/';
    if (!is_dir($full_upload_dir)) {
        if (!mkdir($full_upload_dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }
    
    // Generate unique filename - always use .jpg for consistency
    $timestamp = time();
    $random = mt_rand(1000000, 9999999);
    $output_extension = 'jpg'; // Convert all images to JPEG for maximum compatibility
    $new_filename = $timestamp . '_' . $random . '.' . $output_extension;
    $full_path = $full_upload_dir . $new_filename;
    $relative_path = trim($upload_dir, '/') . '/' . $new_filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
    
    // Resize image if needed
    $resized = resizeImage($full_path, $options['max_width'], $options['max_height'], $options['quality']);
    if (!$resized['success']) {
        // If resize fails, delete the uploaded file and return error
        unlink($full_path);
        return $resized;
    }
    
    $result = [
        'success' => true,
        'message' => 'Image uploaded successfully.',
        'filename' => $new_filename,
        'path' => $relative_path,
        'full_path' => $full_path,
        'size' => filesize($full_path),
        'width' => $image_info[0],
        'height' => $image_info[1],
        'type' => $image_info['mime']
    ];
    
    // Create thumbnail if requested
    if ($options['create_thumbnail']) {
        $thumbnail_result = createThumbnail(
            $full_path, 
            $options['thumbnail_width'], 
            $options['thumbnail_height'],
            $options['quality']
        );
        
        if ($thumbnail_result['success']) {
            $result['thumbnail'] = $thumbnail_result;
        }
    }
    
    return $result;
}

/**
 * Resize image to fit within specified dimensions
 * @param string $image_path Path to image file
 * @param int $max_width Maximum width
 * @param int $max_height Maximum height
 * @param int $quality JPEG quality (1-100)
 * @return array Result array
 */
function resizeImage($image_path, $max_width, $max_height, $quality = 85) {
    // Get image info
    $image_info = getimagesize($image_path);
    if ($image_info === false) {
        return ['success' => false, 'message' => 'Invalid image file.'];
    }
    
    $original_width = $image_info[0];
    $original_height = $image_info[1];
    $image_type = $image_info[2];
    
    // Check if resize is needed
    if ($original_width <= $max_width && $original_height <= $max_height) {
        return ['success' => true, 'message' => 'No resize needed.'];
    }
    
    // Calculate new dimensions
    $ratio = min($max_width / $original_width, $max_height / $original_height);
    $new_width = round($original_width * $ratio);
    $new_height = round($original_height * $ratio);
    
    // Create image resource from file with error handling
    $source = false;
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            if (function_exists('imagecreatefromjpeg')) {
                $source = imagecreatefromjpeg($image_path);
            }
            break;
        case IMAGETYPE_PNG:
            if (function_exists('imagecreatefrompng')) {
                $source = imagecreatefrompng($image_path);
            }
            break;
        case IMAGETYPE_GIF:
            if (function_exists('imagecreatefromgif')) {
                $source = imagecreatefromgif($image_path);
            }
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $source = imagecreatefromwebp($image_path);
            }
            break;
        case IMAGETYPE_BMP:
            if (function_exists('imagecreatefrombmp')) {
                $source = imagecreatefrombmp($image_path);
            }
            break;
        default:
            return ['success' => false, 'message' => 'Unsupported image type.'];
    }

    // Check if image creation was successful
    if (!$source) {
        return ['success' => false, 'message' => 'Failed to create image resource. This image format may not be supported by your PHP installation.'];
    }
    
    // Create new image
    $destination = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($image_type == IMAGETYPE_PNG || $image_type == IMAGETYPE_GIF) {
        imagealphablending($destination, false);
        imagesavealpha($destination, true);
        $transparent = imagecolorallocatealpha($destination, 255, 255, 255, 127);
        imagefilledrectangle($destination, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Resize image
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // Save resized image - convert all formats to JPEG for maximum compatibility
    $result = imagejpeg($destination, $image_path, $quality);
    
    // Clean up memory
    imagedestroy($source);
    imagedestroy($destination);
    
    if (!$result) {
        return ['success' => false, 'message' => 'Failed to save resized image.'];
    }
    
    return [
        'success' => true,
        'message' => 'Image resized successfully.',
        'new_width' => $new_width,
        'new_height' => $new_height
    ];
}

/**
 * Create thumbnail image
 * @param string $source_path Path to source image
 * @param int $thumb_width Thumbnail width
 * @param int $thumb_height Thumbnail height
 * @param int $quality JPEG quality
 * @return array Result array
 */
function createThumbnail($source_path, $thumb_width, $thumb_height, $quality = 85) {
    $path_info = pathinfo($source_path);
    $thumb_filename = $path_info['filename'] . '_thumb.' . $path_info['extension'];
    $thumb_path = $path_info['dirname'] . '/' . $thumb_filename;
    
    // Copy source to thumbnail path
    if (!copy($source_path, $thumb_path)) {
        return ['success' => false, 'message' => 'Failed to create thumbnail file.'];
    }
    
    // Resize thumbnail
    $resize_result = resizeImage($thumb_path, $thumb_width, $thumb_height, $quality);
    
    if (!$resize_result['success']) {
        unlink($thumb_path); // Delete failed thumbnail
        return $resize_result;
    }
    
    return [
        'success' => true,
        'message' => 'Thumbnail created successfully.',
        'filename' => $thumb_filename,
        'path' => $thumb_path,
        'width' => $resize_result['new_width'],
        'height' => $resize_result['new_height']
    ];
}

/**
 * Delete image file and its thumbnail
 * @param string $image_path Path to image file
 * @return bool True if deleted successfully
 */
function deleteImage($image_path) {
    $deleted = true;
    
    // Delete main image
    if (file_exists($image_path)) {
        $deleted = unlink($image_path);
    }
    
    // Delete thumbnail if exists
    $path_info = pathinfo($image_path);
    $thumb_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_thumb.' . $path_info['extension'];
    
    if (file_exists($thumb_path)) {
        unlink($thumb_path);
    }
    
    return $deleted;
}

/**
 * Simple image upload without resizing (fallback when GD is not available)
 * @param array $file $_FILES array element
 * @param string $upload_dir Upload directory
 * @param array $options Upload options
 * @return array Result array
 */
function uploadImageSimple($file, $upload_dir = 'uploads/', $options = []) {
    $default_options = [
        'max_size' => 20 * 1024 * 1024, // 20MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']
    ];

    $options = array_merge($default_options, $options);

    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded.'];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error: ' . getUploadErrorMessage($file['error'])];
    }

    // Validate file size
    if ($file['size'] > $options['max_size']) {
        $max_mb = round($options['max_size'] / (1024 * 1024), 2);
        return ['success' => false, 'message' => "File size exceeds maximum allowed size of {$max_mb}MB."];
    }

    // Get file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validate file type
    if (!in_array($file_extension, $options['allowed_types'])) {
        $allowed = implode(', ', $options['allowed_types']);
        return ['success' => false, 'message' => "Invalid file type. Allowed types: {$allowed}"];
    }

    // Create upload directory if it doesn't exist
    $full_upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . trim($upload_dir, '/') . '/';
    if (!is_dir($full_upload_dir)) {
        if (!mkdir($full_upload_dir, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directory.'];
        }
    }

    // Generate unique filename
    $timestamp = time();
    $random = mt_rand(1000000, 9999999);
    $new_filename = $timestamp . '_' . $random . '.' . $file_extension;
    $full_path = $full_upload_dir . $new_filename;
    $relative_path = trim($upload_dir, '/') . '/' . $new_filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }

    return [
        'success' => true,
        'message' => 'Image uploaded successfully.',
        'filename' => $new_filename,
        'path' => $relative_path,
        'full_path' => $full_path,
        'size' => $file['size']
    ];
}

/**
 * Get upload error message
 * @param int $error_code PHP upload error code
 * @return string Error message
 */
function getUploadErrorMessage($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'File exceeds upload_max_filesize directive.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'File exceeds MAX_FILE_SIZE directive.';
        case UPLOAD_ERR_PARTIAL:
            return 'File was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing temporary folder.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk.';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension.';
        default:
            return 'Unknown upload error.';
    }
}

/**
 * Validate image dimensions
 * @param string $image_path Path to image
 * @param int $min_width Minimum width
 * @param int $min_height Minimum height
 * @param int $max_width Maximum width
 * @param int $max_height Maximum height
 * @return array Validation result
 */
function validateImageDimensions($image_path, $min_width = 0, $min_height = 0, $max_width = 5000, $max_height = 5000) {
    $image_info = getimagesize($image_path);
    if ($image_info === false) {
        return ['valid' => false, 'message' => 'Invalid image file.'];
    }
    
    $width = $image_info[0];
    $height = $image_info[1];
    
    if ($width < $min_width || $height < $min_height) {
        return ['valid' => false, 'message' => "Image dimensions too small. Minimum: {$min_width}x{$min_height}px"];
    }
    
    if ($width > $max_width || $height > $max_height) {
        return ['valid' => false, 'message' => "Image dimensions too large. Maximum: {$max_width}x{$max_height}px"];
    }
    
    return ['valid' => true, 'width' => $width, 'height' => $height];
}
?>

<?php
/**
 * Image Helper Functions
 * Provides consistent image path building and display utilities
 * For Virunga Homestay Application
 */

/**
 * Build image URL for display
 * Converts database filename to full web-accessible URL
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category (activities, rooms, etc.)
 * @param bool $absolute Whether to return absolute URL (default: false for relative)
 * @return string The complete image URL or empty string if no filename
 */
function buildImageUrl($filename, $category, $absolute = false) {
    // Return empty string for empty filenames
    if (empty($filename)) {
        return '';
    }
    
    // Define base paths for each category
    $base_paths = [
        'activities' => 'uploads/activities/',
        'blogs' => 'uploads/blogs/',
        'blog-content' => 'uploads/blog-content/',
        'cars' => 'uploads/cars/',
        'events' => 'uploads/events/',
        'hero' => 'uploads/hero/',
        'hero-images' => 'uploads/hero-images/',
        'homeabout' => 'uploads/homeabout/',
        'rooms' => 'uploads/rooms/',
        'services' => 'uploads/services/',
        'about' => 'uploads/about/'
    ];
    
    // Get base path for category
    $base_path = isset($base_paths[$category]) ? $base_paths[$category] : 'uploads/';
    
    // Handle legacy full paths (backwards compatibility)
    if (strpos($filename, 'uploads/') === 0) {
        // Already a full path, use as is
        $relative_path = $filename;
    } else {
        // Build path from filename
        $relative_path = $base_path . $filename;
    }
    
    // Return absolute or relative URL
    if ($absolute) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host . '/homestay/' . $relative_path;
    } else {
        return $relative_path;
    }
}

/**
 * Build image URL with homestay prefix for admin pages
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return string The complete image URL with /homestay/ prefix
 */
function buildAdminImageUrl($filename, $category) {
    if (empty($filename)) {
        return '';
    }
    
    $relative_path = buildImageUrl($filename, $category, false);
    return '/homestay/' . $relative_path;
}

/**
 * Get image file path for server operations (delete, check existence)
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return string The complete server file path
 */
function buildImageFilePath($filename, $category) {
    if (empty($filename)) {
        return '';
    }
    
    $relative_path = buildImageUrl($filename, $category, false);
    return $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $relative_path;
}

/**
 * Check if image file exists on server
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return bool True if file exists, false otherwise
 */
function imageExists($filename, $category) {
    if (empty($filename)) {
        return false;
    }
    
    $file_path = buildImageFilePath($filename, $category);
    return file_exists($file_path);
}

/**
 * Get image tag HTML with proper src and fallback
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @param string $alt Alt text for the image
 * @param array $attributes Additional HTML attributes
 * @return string Complete HTML img tag or empty string
 */
function getImageTag($filename, $category, $alt = '', $attributes = []) {
    if (empty($filename)) {
        return '';
    }
    
    $src = buildImageUrl($filename, $category, false);
    $alt = htmlspecialchars($alt);
    
    // Build attributes string
    $attr_string = '';
    foreach ($attributes as $key => $value) {
        $attr_string .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }
    
    return '<img src="' . htmlspecialchars($src) . '" alt="' . $alt . '"' . $attr_string . '>';
}

/**
 * Get background image CSS style
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return string CSS background-image style or empty string
 */
function getBackgroundImageStyle($filename, $category) {
    if (empty($filename)) {
        return '';
    }
    
    $src = buildImageUrl($filename, $category, false);
    return 'background-image: url(\'' . htmlspecialchars($src) . '\');';
}

/**
 * Delete image file from server
 * Handles both legacy full paths and new filename-only format
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return bool True if deleted successfully or file doesn't exist, false on error
 */
function deleteImageFile($filename, $category) {
    if (empty($filename)) {
        return true; // Nothing to delete
    }
    
    $file_path = buildImageFilePath($filename, $category);
    
    if (file_exists($file_path)) {
        return unlink($file_path);
    }
    
    return true; // File doesn't exist, consider it "deleted"
}

/**
 * Get image dimensions
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @return array|false Array with width/height or false if not found
 */
function getImageDimensions($filename, $category) {
    if (empty($filename)) {
        return false;
    }
    
    $file_path = buildImageFilePath($filename, $category);
    
    if (file_exists($file_path)) {
        return getimagesize($file_path);
    }
    
    return false;
}

/**
 * Get optimized image URL for different screen sizes
 * (Placeholder for future responsive image implementation)
 * 
 * @param string $filename The filename stored in database
 * @param string $category The image category
 * @param string $size Size variant (small, medium, large)
 * @return string Image URL
 */
function getResponsiveImageUrl($filename, $category, $size = 'medium') {
    // For now, return standard URL
    // In future, this could return different sized versions
    return buildImageUrl($filename, $category, false);
}

/**
 * Validate image category
 * 
 * @param string $category The category to validate
 * @return bool True if valid category, false otherwise
 */
function isValidImageCategory($category) {
    $valid_categories = [
        'activities', 'blogs', 'blog-content', 'cars', 'events', 
        'hero', 'hero-images', 'homeabout', 'rooms', 'services', 'about'
    ];
    
    return in_array($category, $valid_categories);
}
?>

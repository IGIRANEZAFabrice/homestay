<?php
/**
 * Validation Utility
 * Provides comprehensive validation functions for admin forms
 * For Virunga Homestay Admin Dashboard
 */

// Define admin access constant if not already defined
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}

/**
 * Validate required fields
 * @param array $data Data to validate
 * @param array $required_fields Array of required field names
 * @return array Validation result
 */
function validateRequired($data, $required_fields) {
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $field_name = ucwords(str_replace('_', ' ', $field));
            $errors[$field] = "{$field_name} is required.";
        }
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Validate string length
 * @param string $value Value to validate
 * @param int $min_length Minimum length
 * @param int $max_length Maximum length
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateLength($value, $min_length = 0, $max_length = 255, $field_name = 'Field') {
    $length = strlen(trim($value));
    
    if ($length < $min_length) {
        return [
            'valid' => false,
            'message' => "{$field_name} must be at least {$min_length} characters long."
        ];
    }
    
    if ($length > $max_length) {
        return [
            'valid' => false,
            'message' => "{$field_name} must not exceed {$max_length} characters."
        ];
    }
    
    return ['valid' => true];
}

/**
 * Validate email address
 * @param string $email Email to validate
 * @return array Validation result
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'Email is required.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format.'];
    }
    
    return ['valid' => true];
}

/**
 * Validate numeric value
 * @param mixed $value Value to validate
 * @param float $min Minimum value
 * @param float $max Maximum value
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateNumeric($value, $min = null, $max = null, $field_name = 'Field') {
    if (!is_numeric($value)) {
        return ['valid' => false, 'message' => "{$field_name} must be a number."];
    }
    
    $num_value = floatval($value);
    
    if ($min !== null && $num_value < $min) {
        return ['valid' => false, 'message' => "{$field_name} must be at least {$min}."];
    }
    
    if ($max !== null && $num_value > $max) {
        return ['valid' => false, 'message' => "{$field_name} must not exceed {$max}."];
    }
    
    return ['valid' => true, 'value' => $num_value];
}

/**
 * Validate integer value
 * @param mixed $value Value to validate
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateInteger($value, $min = null, $max = null, $field_name = 'Field') {
    if (!filter_var($value, FILTER_VALIDATE_INT)) {
        return ['valid' => false, 'message' => "{$field_name} must be an integer."];
    }
    
    $int_value = intval($value);
    
    if ($min !== null && $int_value < $min) {
        return ['valid' => false, 'message' => "{$field_name} must be at least {$min}."];
    }
    
    if ($max !== null && $int_value > $max) {
        return ['valid' => false, 'message' => "{$field_name} must not exceed {$max}."];
    }
    
    return ['valid' => true, 'value' => $int_value];
}

/**
 * Validate date format
 * @param string $date Date string to validate
 * @param string $format Expected date format (default: Y-m-d)
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateDate($date, $format = 'Y-m-d', $field_name = 'Date') {
    if (empty($date)) {
        return ['valid' => false, 'message' => "{$field_name} is required."];
    }
    
    $date_obj = DateTime::createFromFormat($format, $date);
    
    if (!$date_obj || $date_obj->format($format) !== $date) {
        return ['valid' => false, 'message' => "{$field_name} must be in format {$format}."];
    }
    
    return ['valid' => true, 'date' => $date_obj];
}

/**
 * Validate datetime format
 * @param string $datetime Datetime string to validate
 * @param string $format Expected datetime format (default: Y-m-d H:i:s)
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateDateTime($datetime, $format = 'Y-m-d H:i:s', $field_name = 'Date/Time') {
    if (empty($datetime)) {
        return ['valid' => false, 'message' => "{$field_name} is required."];
    }
    
    $datetime_obj = DateTime::createFromFormat($format, $datetime);
    
    if (!$datetime_obj) {
        return ['valid' => false, 'message' => "{$field_name} must be in format {$format}."];
    }
    
    return ['valid' => true, 'datetime' => $datetime_obj];
}

/**
 * Validate URL format
 * @param string $url URL to validate
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateURL($url, $field_name = 'URL') {
    if (empty($url)) {
        return ['valid' => false, 'message' => "{$field_name} is required."];
    }
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['valid' => false, 'message' => "{$field_name} must be a valid URL."];
    }
    
    return ['valid' => true];
}

/**
 * Validate slug format (URL-friendly string)
 * @param string $slug Slug to validate
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateSlug($slug, $field_name = 'Slug') {
    if (empty($slug)) {
        return ['valid' => false, 'message' => "{$field_name} is required."];
    }
    
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        return ['valid' => false, 'message' => "{$field_name} must contain only lowercase letters, numbers, and hyphens."];
    }
    
    return ['valid' => true];
}

/**
 * Validate rating value (1-5)
 * @param mixed $rating Rating to validate
 * @return array Validation result
 */
function validateRating($rating) {
    $validation = validateInteger($rating, 1, 5, 'Rating');
    
    if (!$validation['valid']) {
        return $validation;
    }
    
    return ['valid' => true, 'value' => $validation['value']];
}

/**
 * Validate display order
 * @param mixed $order Order value to validate
 * @return array Validation result
 */
function validateDisplayOrder($order) {
    if (empty($order)) {
        return ['valid' => true, 'value' => 1]; // Default to 1 if empty
    }
    
    return validateInteger($order, 1, 999, 'Display Order');
}

/**
 * Validate boolean value
 * @param mixed $value Value to validate
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateBoolean($value, $field_name = 'Field') {
    // Convert common boolean representations
    if (is_string($value)) {
        $value = strtolower($value);
        if (in_array($value, ['true', '1', 'yes', 'on'])) {
            $value = true;
        } elseif (in_array($value, ['false', '0', 'no', 'off', ''])) {
            $value = false;
        }
    }
    
    if (!is_bool($value) && $value !== 0 && $value !== 1) {
        return ['valid' => false, 'message' => "{$field_name} must be a boolean value."];
    }
    
    return ['valid' => true, 'value' => (bool)$value];
}

/**
 * Validate JSON string
 * @param string $json JSON string to validate
 * @param string $field_name Field name for error message
 * @return array Validation result
 */
function validateJSON($json, $field_name = 'JSON') {
    if (empty($json)) {
        return ['valid' => true, 'value' => null];
    }
    
    $decoded = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['valid' => false, 'message' => "{$field_name} must be valid JSON."];
    }
    
    return ['valid' => true, 'value' => $decoded];
}

/**
 * Generate slug from title
 * @param string $title Title to convert
 * @return string Generated slug
 */
function generateSlug($title) {
    // Convert to lowercase
    $slug = strtolower($title);
    
    // Replace spaces and special characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    
    // Remove multiple consecutive hyphens
    $slug = preg_replace('/-+/', '-', $slug);
    
    return $slug;
}

/**
 * Sanitize HTML content
 * @param string $content HTML content to sanitize
 * @param array $allowed_tags Allowed HTML tags
 * @return string Sanitized content
 */
function sanitizeHTML($content, $allowed_tags = ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img']) {
    $allowed_tags_string = '<' . implode('><', $allowed_tags) . '>';
    return strip_tags($content, $allowed_tags_string);
}

/**
 * Validate form data against rules
 * @param array $data Form data
 * @param array $rules Validation rules
 * @return array Validation result with all errors
 */
function validateFormData($data, $rules) {
    $errors = [];
    $validated_data = [];
    
    foreach ($rules as $field => $field_rules) {
        $value = $data[$field] ?? '';
        $field_name = ucwords(str_replace('_', ' ', $field));
        
        foreach ($field_rules as $rule => $params) {
            switch ($rule) {
                case 'required':
                    if ($params && empty(trim($value))) {
                        $errors[$field] = "{$field_name} is required.";
                        continue 2; // Skip other validations for this field
                    }
                    break;
                    
                case 'length':
                    $result = validateLength($value, $params[0] ?? 0, $params[1] ?? 255, $field_name);
                    if (!$result['valid']) {
                        $errors[$field] = $result['message'];
                        continue 2;
                    }
                    break;
                    
                case 'email':
                    if ($params) {
                        $result = validateEmail($value);
                        if (!$result['valid']) {
                            $errors[$field] = $result['message'];
                            continue 2;
                        }
                    }
                    break;
                    
                case 'numeric':
                    if ($params) {
                        $result = validateNumeric($value, $params[0] ?? null, $params[1] ?? null, $field_name);
                        if (!$result['valid']) {
                            $errors[$field] = $result['message'];
                            continue 2;
                        }
                        $value = $result['value'];
                    }
                    break;
                    
                case 'integer':
                    if ($params) {
                        $result = validateInteger($value, $params[0] ?? null, $params[1] ?? null, $field_name);
                        if (!$result['valid']) {
                            $errors[$field] = $result['message'];
                            continue 2;
                        }
                        $value = $result['value'];
                    }
                    break;
            }
        }
        
        $validated_data[$field] = $value;
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'data' => $validated_data
    ];
}
?>

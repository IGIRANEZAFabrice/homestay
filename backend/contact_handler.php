<?php
/**
 * Contact Form Handler
 * Handles contact form submissions from the website
 */

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type for JSON responses
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include database connection
require_once '../include/connection.php';

// Check database connection
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Get and sanitize input data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    } elseif (strlen($name) > 255) {
        $errors[] = 'Name is too long (max 255 characters)';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    } elseif (strlen($email) > 255) {
        $errors[] = 'Email is too long (max 255 characters)';
    }
    
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    } elseif (strlen($subject) > 255) {
        $errors[] = 'Subject is too long (max 255 characters)';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) > 5000) {
        $errors[] = 'Message is too long (max 5000 characters)';
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Prepare and execute the database insert
    $sql = "INSERT INTO contact_messages (name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, 'new', NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('ssss', $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        $message_id = $conn->insert_id;
        
        // Log the successful submission (optional)
        error_log("Contact form submission: ID {$message_id}, Name: {$name}, Email: {$email}, Subject: {$subject}");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Thank you! Your message has been sent successfully. We will get back to you soon.',
            'message_id' => $message_id
        ]);
    } else {
        throw new Exception('Failed to save message: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    // Log the error
    error_log("Contact form error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, there was an error sending your message. Please try again later.'
    ]);
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>

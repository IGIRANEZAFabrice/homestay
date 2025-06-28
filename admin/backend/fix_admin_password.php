<?php
/**
 * Fix Admin Password
 * Update the admin user with the correct password hash
 */

header('Content-Type: application/json');

try {
    // Include database connection
    require_once __DIR__ . '/../../include/connection.php';
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // The correct hash from our generator
    $correct_hash = '$argon2id$v=19$m=65536,t=4,p=3$S0g5Sk9sd3VDM09GOHcxVg$LiCeyjhmAA9Svok0KJ7F56rx16g1TOLvTqjI/EFfqcE';
    
    // Update the admin user password
    $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = 'admin'");
    $stmt->bind_param("s", $correct_hash);
    
    if ($stmt->execute()) {
        // Verify the update
        $check_stmt = $conn->prepare("SELECT id, username, email, role FROM admin_users WHERE username = 'admin'");
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $admin_user = $result->fetch_assoc();
        
        // Test password verification
        $verify_stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = 'admin'");
        $verify_stmt->execute();
        $password_result = $verify_stmt->get_result();
        $password_row = $password_result->fetch_assoc();
        
        $password_verified = password_verify('admin123', $password_row['password']);
        
        $response = [
            'success' => true,
            'message' => 'Admin password updated successfully',
            'admin_user' => $admin_user,
            'password_verified' => $password_verified,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } else {
        throw new Exception("Failed to update password: " . $conn->error);
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Password update failed: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>

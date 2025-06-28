<?php
/**
 * Database Setup Script
 * Run this once to set up the required admin tables
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Include database connection
    require_once __DIR__ . '/../../include/connection.php';
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    $results = [];
    $errors = [];
    
    // Read and execute the SQL file
    $sql_file = __DIR__ . '/database/admin_tables_fixed.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    $results['total_statements'] = count($statements);
    $results['executed'] = 0;
    $results['failed'] = 0;
    
    // Execute each statement
    foreach ($statements as $index => $statement) {
        try {
            if ($conn->query($statement)) {
                $results['executed']++;
            } else {
                $errors[] = "Statement " . ($index + 1) . ": " . $conn->error;
                $results['failed']++;
            }
        } catch (Exception $e) {
            $errors[] = "Statement " . ($index + 1) . ": " . $e->getMessage();
            $results['failed']++;
        }
    }
    
    // Check if tables were created successfully
    $tables_to_check = ['admin_users', 'admin_sessions', 'admin_login_attempts'];
    $results['tables_status'] = [];
    
    foreach ($tables_to_check as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        $results['tables_status'][$table] = $result && $result->num_rows > 0;
    }
    
    // Check admin user
    $admin_check = $conn->query("SELECT id, username, email, role FROM admin_users WHERE username = 'admin'");
    if ($admin_check && $admin_check->num_rows > 0) {
        $admin_user = $admin_check->fetch_assoc();
        $results['admin_user'] = $admin_user;
    } else {
        $results['admin_user'] = null;
    }
    
    $response = [
        'success' => $results['failed'] === 0,
        'message' => $results['failed'] === 0 ? 'Database setup completed successfully' : 'Database setup completed with some errors',
        'results' => $results,
        'errors' => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database setup failed: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}
?>

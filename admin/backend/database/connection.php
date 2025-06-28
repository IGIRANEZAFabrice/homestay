<?php
/**
 * Admin Database Connection
 * Professional database connection for Virunga Homestay Admin Dashboard
 * Uses MySQLi with proper error handling and security measures
 */

// Prevent direct access
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not allowed');
}

// Global connection variable
$conn = null;

/**
 * Get database connection (singleton pattern)
 * @return mysqli Database connection
 */
function getConnection() {
    global $conn;

    // Return existing connection if available
    if ($conn && !$conn->connect_error) {
        return $conn;
    }

    // Database configuration
    $db_config = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'homestay',
        'charset' => 'utf8mb4'
    ];

    // Create MySQLi connection
    $conn = new mysqli(
        $db_config['host'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );

    // Check connection
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection failed: " . $conn->connect_error . ". Please check your configuration.");
    }

    // Verify connection is working by testing a simple query
    $test_result = $conn->query("SELECT 1");
    if (!$test_result) {
        error_log("Database connection test failed: " . $conn->error);
        die("Database connection test failed: " . $conn->error . ". Please check your database.");
    }

    // Set charset for proper UTF-8 handling
    if (!$conn->set_charset($db_config['charset'])) {
        error_log("Error setting charset: " . $conn->error);
        die("Error setting database charset.");
    }

    // Set SQL mode for better data integrity
    $conn->query("SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");

    return $conn;
}

// Initialize connection
$conn = getConnection();

/**
 * Execute a prepared statement with error handling
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (e.g., 'ssi' for string, string, integer)
 * @param array $params Parameters to bind
 * @return mysqli_result|bool Query result or false on failure
 */
function executeQuery($query, $types = '', $params = []) {
    $conn = getConnection();

    if (!$conn) {
        error_log("Database connection is null");
        return false;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Query execution failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

/**
 * Get a single row from database
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array|null Single row or null if not found
 */
function getSingleRow($query, $types = '', $params = []) {
    $result = executeQuery($query, $types, $params);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

/**
 * Get multiple rows from database
 * @param string $query SQL query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return array Array of rows
 */
function getMultipleRows($query, $types = '', $params = []) {
    $result = executeQuery($query, $types, $params);
    $rows = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    
    return $rows;
}

/**
 * Insert data and return the inserted ID
 * @param string $query SQL insert query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return int|false Inserted ID or false on failure
 */
function insertData($query, $types = '', $params = []) {
    $conn = getConnection();

    if (!$conn) {
        error_log("Database connection is null");
        return false;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Insert preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Insert execution failed: " . $stmt->error);
        return false;
    }
    
    $insert_id = $conn->insert_id;
    $stmt->close();
    
    return $insert_id;
}

/**
 * Update data and return affected rows
 * @param string $query SQL update query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return int|false Number of affected rows or false on failure
 */
function updateData($query, $types = '', $params = []) {
    $conn = getConnection();

    if (!$conn) {
        error_log("Database connection is null");
        return false;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Update preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Update execution failed: " . $stmt->error);
        return false;
    }
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    return $affected_rows;
}

/**
 * Delete data and return affected rows
 * @param string $query SQL delete query
 * @param string $types Parameter types
 * @param array $params Parameters
 * @return int|false Number of affected rows or false on failure
 */
function deleteData($query, $types = '', $params = []) {
    $conn = getConnection();

    if (!$conn) {
        error_log("Database connection is null");
        return false;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Delete preparation failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Delete execution failed: " . $stmt->error);
        return false;
    }
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    return $affected_rows;
}

/**
 * Escape string for safe output
 * @param string $string String to escape
 * @return string Escaped string
 */
function escapeString($string) {
    $conn = getConnection();
    if (!$conn) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    return $conn->real_escape_string($string);
}

/**
 * Close database connection
 */
function closeConnection() {
    global $conn;
    if ($conn && $conn instanceof mysqli) {
        $conn->close();
        $conn = null;
    }
}

// Register shutdown function to close connection
register_shutdown_function('closeConnection');
?>

<?php
/**
 * Fix Contact Messages Table
 * Adds missing columns to contact_messages table
 */

// Include database connection
require_once '../../include/connection.php';

try {
    echo "<h2>Fixing Contact Messages Table</h2>\n";
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Read and execute the SQL file
    $sql_file = __DIR__ . '/database/fix_contact_messages.sql';
    
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
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            if ($conn->query($statement)) {
                echo "<p style='color: green;'>✓ Executed: " . substr(trim($statement), 0, 50) . "...</p>\n";
                $success_count++;
            } else {
                echo "<p style='color: orange;'>⚠ Warning: " . $conn->error . "</p>\n";
                echo "<p style='color: gray;'>Statement: " . substr(trim($statement), 0, 100) . "...</p>\n";
                $error_count++;
            }
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠ Warning: " . $e->getMessage() . "</p>\n";
            echo "<p style='color: gray;'>Statement: " . substr(trim($statement), 0, 100) . "...</p>\n";
            $error_count++;
        }
    }
    
    echo "<h3>Summary:</h3>\n";
    echo "<p>✓ Successful operations: $success_count</p>\n";
    echo "<p>⚠ Warnings/Errors: $error_count</p>\n";
    
    if ($error_count === 0) {
        echo "<p style='color: green; font-weight: bold;'>✓ Contact messages table has been successfully updated!</p>\n";
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠ Some operations had warnings. This is normal if columns already exist.</p>\n";
    }
    
    // Test the table structure
    echo "<h3>Testing Table Structure:</h3>\n";
    $result = $conn->query("DESCRIBE contact_messages");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p>Please check your database connection and try again.</p>\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fix Contact Messages Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2, h3 { color: #333; }
        p { margin: 5px 0; }
        table { border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <p><a href="../pages/contact-messages/index.php">← Back to Contact Messages</a></p>
</body>
</html>

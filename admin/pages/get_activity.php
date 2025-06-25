<?php
require_once '../../include/connection.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Return error response
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Activity ID is required']);
    exit;
}

// Sanitize input
$id = (int)$_GET['id'];

// Prepare and execute query
$stmt = $conn->prepare("SELECT * FROM activities WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // No activity found with this ID
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Activity not found']);
    exit;
}

// Fetch activity data
$activity = $result->fetch_assoc();

// Close statement
$stmt->close();

// Return activity data as JSON
header('Content-Type: application/json');
echo json_encode($activity);
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$page_url = $data['page_url'] ?? '';

if (!empty($page_url)) {
    // Sanitize path (just keep pathname without query params)
    $parsed = parse_url($page_url, PHP_URL_PATH);
    $path = basename($parsed);
    if (empty($path)) $path = 'index.php';

    $visit_date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO page_visits (page_url, visit_date, visit_count) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
    $stmt->bind_param("ss", $path, $visit_date);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No page url provided']);
}

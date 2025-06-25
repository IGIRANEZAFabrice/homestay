<?php
require_once '../../include/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['reviewerName'] ?? '');
    $rating = intval($_POST['reviewerRating'] ?? 0);
    $content = trim($_POST['reviewContent'] ?? '');
    if ($name && $rating >= 1 && $rating <= 5 && $content) {
        $stmt = $conn->prepare("INSERT INTO reviews (name, rating, review_content, is_active) VALUES (?, ?, ?, 1)");
        $stmt->bind_param('sis', $name, $rating, $content);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'DB error']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
    exit;
}

// GET: fetch reviews
$sql = "SELECT * FROM reviews WHERE is_active = 1 ORDER BY is_featured DESC, created_at DESC LIMIT 12";
$result = $conn->query($sql);
$reviews = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
echo json_encode(['success' => true, 'reviews' => $reviews]);

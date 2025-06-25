<?php
require_once '../include/connection.php';
header('Content-Type: application/json');
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare('SELECT * FROM blogs WHERE id=? AND is_published=1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    echo json_encode(['success' => !!$blog, 'blog' => $blog]);
    exit;
}
$sql = "SELECT id, title, image, slug, published_at, created_at FROM blogs WHERE is_published=1 ORDER BY published_at DESC, id DESC";
$result = $conn->query($sql);
$blogs = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}
echo json_encode(['success' => true, 'blogs' => $blogs]);

<?php
require_once '../../include/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        $published_at = $is_published ? date('Y-m-d H:i:s') : null;
        
        if ($title && $image && $content) {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO blogs (title, image, content, slug, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssis', $title, $image, $content, $slug, $is_published, $published_at);
                $ok = $stmt->execute();
            } else {
                $stmt = $conn->prepare("UPDATE blogs SET title=?, image=?, content=?, slug=?, is_published=?, published_at=? WHERE id=?");
                $stmt->bind_param('ssssisi', $title, $image, $content, $slug, $is_published, $published_at, $id);
                $ok = $stmt->execute();
            }
            $stmt->close();
            echo json_encode(['success' => $ok]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
        }
        exit;
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            // First delete content blocks
            $stmt = $conn->prepare("DELETE FROM blog_content_blocks WHERE blog_id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            
            // Then delete the blog
            $stmt = $conn->prepare("DELETE FROM blogs WHERE id=?");
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => $ok]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
        }
        exit;
    }
}

// GET: fetch all blogs
$sql = "SELECT * FROM blogs ORDER BY published_at DESC, id DESC";
$result = $conn->query($sql);
$blogs = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}
echo json_encode(['success' => true, 'blogs' => $blogs]);

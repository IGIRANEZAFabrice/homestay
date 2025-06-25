<?php
require_once '../../include/connection.php';
header('Content-Type: application/json');

function save_uploaded_image($file) {
    $targetDir = '../../uploads/events/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return false;
    $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return 'uploads/events/' . $filename;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' || $action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $event_date = trim($_POST['event_date'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $imagePath = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = save_uploaded_image($_FILES['image']);
        } elseif ($action === 'edit' && isset($_POST['keep_image']) && $id) {
            // Keep old image
            $stmt = $conn->prepare("SELECT image FROM events WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($oldImage);
            $stmt->fetch();
            $stmt->close();
            $imagePath = $oldImage;
        }
        if ($title && $description && $event_date && $imagePath) {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO events (title, image, description, event_date, location, is_active) VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->bind_param('sssss', $title, $imagePath, $description, $event_date, $location);
                $ok = $stmt->execute();
            } else {
                $stmt = $conn->prepare("UPDATE events SET title=?, image=?, description=?, event_date=?, location=? WHERE id=?");
                $stmt->bind_param('sssssi', $title, $imagePath, $description, $event_date, $location, $id);
                $ok = $stmt->execute();
            }
            $stmt->close();
            echo json_encode(['success' => $ok]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Missing fields or image']);
        }
        exit;
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
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
// GET: fetch all events
$sql = "SELECT * FROM events WHERE is_active = 1 ORDER BY event_date DESC, id DESC";
$result = $conn->query($sql);
$events = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
echo json_encode(['success' => true, 'events' => $events]);

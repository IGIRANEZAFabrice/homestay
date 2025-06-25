<?php
require_once '../../include/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // First get the image path to delete the file
    $sql = "SELECT image FROM hero_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $imagePath = "../../" . $row['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    // Now delete the database record
    $sql = "DELETE FROM hero_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Database error: ' . $stmt->error]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>

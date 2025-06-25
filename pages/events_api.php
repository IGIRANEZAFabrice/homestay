<?php
require_once '../include/connection.php';
header('Content-Type: application/json');
$sql = "SELECT * FROM events WHERE is_active = 1 ORDER BY event_date ASC, id DESC";
$result = $conn->query($sql);
$events = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
echo json_encode(['success' => true, 'events' => $events]);

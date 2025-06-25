<?php
// Include database connection
require_once '../../include/connection.php';

// Handle form submission for adding/editing rooms
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = isset($_POST['roomId']) ? $_POST['roomId'] : null;
  $title = $_POST['roomName'];
  $description = $_POST['roomDescription'];
  $image = 'default-room.jpg'; // Default image
  
  // Handle image upload if provided
  if (isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] == 0) {
    $target_dir = "../../uploads/rooms/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
      mkdir($target_dir, 0777, true);
    }
    
    $image = time() . '_' . basename($_FILES["roomImage"]["name"]);
    $target_file = $target_dir . $image;
    
    if (move_uploaded_file($_FILES["roomImage"]["tmp_name"], $target_file)) {
      // File uploaded successfully
    } else {
      $upload_error = "Sorry, there was an error uploading your file.";
    }
  }
  
  if ($id) {
    // Update existing room
    $image_sql = isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] == 0 ? ", image='$image'" : "";
    $sql = "UPDATE rooms SET title='$title', description='$description'$image_sql WHERE id=$id";
  } else {
    // Add new room
    $sql = "INSERT INTO rooms (title, description, image) VALUES ('$title', '$description', '$image')";
  }
  
  if ($conn->query($sql) === TRUE) {
    $success_message = $id ? "Room updated successfully" : "Room added successfully";
    header("Location: rooms.php?success=1");
    exit();
  } else {
    $error_message = "Error: " . $conn->error;
  }
}

// Handle room deletion
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $sql = "DELETE FROM rooms WHERE id=$id";
  
  if ($conn->query($sql) === TRUE) {
    header("Location: rooms.php?deleted=1");
    exit();
  } else {
    $error_message = "Error deleting room: " . $conn->error;
  }
}

// Fetch all rooms
$sql = "SELECT * FROM rooms ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Rooms | Virunga Admin</title>
  <link rel="stylesheet" href="../../css/admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/4e9c2b2c0a.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include 'sidebar.php'; ?>
   <?php include 'header.php'; ?>
  <div class="main-content">
    <header class="dashboard-header">
      <div class="header-title">
        <h1>Rooms Management</h1>
      </div>
      <button class="add-room-btn" id="openAddRoomModal"><i class="fas fa-plus"></i> Add Room</button>
    </header>
    
    <?php if(isset($error_message)): ?>
    <div class="alert alert-error">
      <?php echo $error_message; ?>
    </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">
      Room saved successfully!
    </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['deleted'])): ?>
    <div class="alert alert-success">
      Room deleted successfully!
    </div>
    <?php endif; ?>
    
    <section class="dashboard-table">
      <h2>All Rooms</h2>
      <table id="roomsTable">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result && $result->num_rows > 0): ?>
            <?php while($room = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php if (!empty($room['image']) && $room['image'] !== 'default-room.jpg'): ?>
                    <img src="../../uploads/rooms/<?php echo $room['image']; ?>" alt="<?php echo $room['title']; ?>" class="room-thumbnail">
                  <?php else: ?>
                    <div class="no-image-placeholder">
                      <i class="fas fa-image"></i>
                      <span>No Image</span>
                    </div>
                  <?php endif; ?>
                </td>
                <td><?php echo $room['title']; ?></td>
                <td><?php echo substr($room['description'], 0, 100) . (strlen($room['description']) > 100 ? '...' : ''); ?></td>
                
                <td>
                  <button class="icon-btn edit-btn" title="Edit Room" onclick="editRoom(<?php echo $room['id']; ?>, <?php echo json_encode($room['title']); ?>, <?php echo json_encode($room['description']); ?>, <?php echo json_encode($room['image']); ?>)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="rooms.php?delete=<?php echo $room['id']; ?>" class="icon-btn delete-btn" title="Delete Room" onclick="return confirm('Are you sure you want to delete this room?')">
                    <i class="fas fa-trash-alt"></i>
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="no-data">No rooms found. Add your first room!</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>

  <!-- Add/Edit Room Modal -->
  <div class="modal" id="roomModal">
    <div class="modal-content">
      <span class="close-modal" id="closeRoomModal">&times;</span>
      <h2 id="modalTitle">Add Room</h2>
      <form id="roomForm" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="roomId" name="roomId">
        <input type="hidden" id="currentImage" name="currentImage">
        <div class="form-group">
          <label for="roomName">Room Name</label>
          <input type="text" id="roomName" name="roomName" required>
        </div>
        <div class="form-group">
          <label for="roomDescription">Description</label>
          <textarea id="roomDescription" name="roomDescription" rows="3" required></textarea>
        </div>
        <div class="form-group">
          <label for="roomImage">Room Image</label>
          <input type="file" id="roomImage" name="roomImage" accept="image/*">
          <div id="imagePreviewContainer" style="display: none; margin-top: 10px;">
            <p>Current image:</p>
            <img id="imagePreview" src="" alt="Room Image Preview" style="max-width: 100%; max-height: 150px;">
          </div>
          <p class="form-hint">Leave empty to keep current image when editing</p>
        </div>
        <div class="form-actions">
          <button type="submit" class="save-btn">Save</button>
          <button type="button" class="cancel-btn" id="cancelRoomModal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script src="../js/rooms.js"></script>
</body>
</html>

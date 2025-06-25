<?php
require_once '../../include/connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['aboutTitle'];
    $description = $_POST['aboutParagraph1'];
    $imagePath = '';
    $error = '';

    // Handle image upload if a file is provided
    if (isset($_FILES['aboutImage']) && $_FILES['aboutImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../../uploads/homeabout/';
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['aboutImage']['name']);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $validTypes)) {
            $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } else if (move_uploaded_file($_FILES['aboutImage']['tmp_name'], $targetFile)) {
            $imagePath = 'uploads/homeabout/' . $fileName;
        } else {
            $error = 'Error uploading image.';
        }
    } else if (!empty($_POST['existingImage'])) {
        $imagePath = $_POST['existingImage'];
    }

    if (!$error) {
        // Check if a record exists
        $sql = "SELECT id FROM homepage_about LIMIT 1";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            // Update
            $row = $result->fetch_assoc();
            $id = $row['id'];
            $stmt = $conn->prepare("UPDATE homepage_about SET title=?, description=?, image=? WHERE id=?");
            $stmt->bind_param('sssi', $title, $description, $imagePath, $id);
            $stmt->execute();
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO homepage_about (title, description, image) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $title, $description, $imagePath);
            $stmt->execute();
        }
        header('Location: homeabout.php?success=1');
        exit;
    }
}
// Fetch current about data
$about = [
    'title' => '',
    'description' => '',
    'image' => ''
];
$sql = "SELECT * FROM homepage_about LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $about = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Home About Section - Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
     <?php include 'header.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1>Manage Home About Section</h1>
        </div>
        <div class="about-edit-container">
            <?php if (!empty($error)): ?>
                <div class="error-message" style="color:red;">Error: <?php echo $error; ?></div>
            <?php elseif (isset($_GET['success'])): ?>
                <div class="success-message" style="color:green;">Changes saved successfully!</div>
            <?php endif; ?>
            <form id="aboutForm" method="post" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Content</h2>
                    <div class="form-group">
                        <label for="aboutTitle">Section Title</label>
                        <input type="text" id="aboutTitle" name="aboutTitle" value="<?php echo htmlspecialchars($about['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="aboutParagraph1">Paragraph</label>
                        <textarea id="aboutParagraph1" name="aboutParagraph1" rows="4" required><?php echo htmlspecialchars($about['description']); ?></textarea>
                    </div>
                </div>
                <div class="form-section">
                    <h2>Image</h2>
                    <div class="form-group">
                        <label for="aboutImage">Image</label>
                        <input type="file" id="aboutImage" name="aboutImage" accept="image/*">
                        <?php if (!empty($about['image'])): ?>
                            <div class="image-preview" id="imagePreview" style="background-image: url('../../<?php echo $about['image']; ?>');"></div>
                            <input type="hidden" name="existingImage" value="<?php echo htmlspecialchars($about['image']); ?>">
                        <?php else: ?>
                            <div class="image-preview" id="imagePreview"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Show image preview on file select
    document.getElementById('aboutImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>

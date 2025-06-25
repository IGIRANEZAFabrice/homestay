<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../include/connection.php';

// Handle add/edit service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Log the request for debugging
    error_log("POST request received: " . print_r($_POST, true));
    
    if ($_POST['action'] === 'save') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = $_POST['title'];
        $description = $_POST['description'];
        $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 1;
        $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
        $imagePath = '';
        $error = '';
        
        // Handle image upload if a file is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = '../../uploads/services/';
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $validTypes)) {
                $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
            } else if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/services/' . $fileName;
            } else {
                $error = 'Error uploading image.';
            }
        } else if (!empty($_POST['existingImage'])) {
            $imagePath = $_POST['existingImage'];
        }
        
        if (!$error) {
            try {
                if ($id > 0) {
                    $stmt = $conn->prepare("UPDATE services SET title=?, description=?, image=?, display_order=?, is_active=? WHERE id=?");
                    $stmt->bind_param('sssiii', $title, $description, $imagePath, $display_order, $is_active, $id);
                    $result = $stmt->execute();
                } else {
                    $stmt = $conn->prepare("INSERT INTO services (title, description, image, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param('sssii', $title, $description, $imagePath, $display_order, $is_active);
                    $result = $stmt->execute();
                }
                
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
                }
            } catch (Exception $e) {
                echo json_encode(['error' => 'Exception: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['error' => $error]);
        }
        exit;
    }
    
    if ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        // Delete image file
        $sql = "SELECT image FROM services WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $imagePath = '../../' . $row['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Delete DB record
        $stmt = $conn->prepare("DELETE FROM services WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        exit;
    }
}
// Fetch all services
$services = [];
$sql = "SELECT * FROM services ORDER BY display_order ASC, id ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Home Content - Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
     <?php include 'header.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1>Manage Home Content</h1>
            <button class="btn btn-primary" onclick="openAddServiceModal()">
                <i class="fas fa-plus"></i> Add New Service
            </button>
        </div>
        <div class="services-grid" id="servicesGrid">
            <?php foreach ($services as $service): ?>
            <div class="service-card">
                <div class="service-image">
                    <img src="../../<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                </div>
                <div class="service-content">
                    <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="service-actions">
                        <button class="btn btn-edit" onclick="editService(<?php echo $service['id']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deleteService(<?php echo $service['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add/Edit Service Modal -->
    <div class="modal" id="serviceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Service</h2>
                <button class="close-btn" onclick="closeServiceModal()">&times;</button>
            </div>
            <form id="serviceForm" onsubmit="handleServiceSubmit(event)">
                <input type="hidden" id="serviceId" name="id">
                <div class="form-group">
                    <label for="serviceTitle">Service Title</label>
                    <input type="text" id="serviceTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="serviceImage">Image</label>
                    <input type="file" id="serviceImage" name="image" accept="image/*">
                    <div class="image-preview" id="imagePreview"></div>
                </div>
                <div class="form-group">
                    <label for="serviceDescription">Description</label>
                    <textarea id="serviceDescription" name="description" rows="4" required></textarea>
                </div>
                <!-- Removed layout dropdown -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Service</button>
                    <button type="button" class="btn btn-secondary" onclick="closeServiceModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <p>Are you sure you want to delete this service? This action cannot be undone.</p>
            <div class="form-actions">
                <button class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    // Use AJAX for add/edit/delete
    function openAddServiceModal() {
        document.getElementById('modalTitle').textContent = 'Add New Service';
        document.getElementById('serviceForm').reset();
        document.getElementById('serviceId').value = '';
        document.getElementById('imagePreview').style.backgroundImage = 'none';
        document.getElementById('serviceImage').required = true;
        let existing = document.getElementById('existingImage');
        if (existing) existing.remove();
        document.getElementById('serviceModal').style.display = 'block';
    }
    
    function editService(id) {
        const service = <?php echo json_encode($services); ?>.find(s => s.id == id);
        if (service) {
            document.getElementById('modalTitle').textContent = 'Edit Service';
            document.getElementById('serviceId').value = service.id;
            document.getElementById('serviceTitle').value = service.title;
            document.getElementById('serviceDescription').value = service.description;
            document.getElementById('imagePreview').style.backgroundImage = `url(../../${service.image})`;
            document.getElementById('serviceImage').required = false;
            document.getElementById('serviceModal').style.display = 'block';
            // For edit, show existing image path in a hidden field
            let existing = document.getElementById('existingImage');
            if (!existing) {
                existing = document.createElement('input');
                existing.type = 'hidden';
                existing.id = 'existingImage';
                existing.name = 'existingImage';
                document.getElementById('serviceForm').appendChild(existing);
            }
            existing.value = service.image;
        }
    }
    
    function closeServiceModal() {
        document.getElementById('serviceModal').style.display = 'none';
    }
    
    function handleServiceSubmit(event) {
        event.preventDefault();
        console.log('Form submission started'); // Debug log
        
        const form = document.getElementById('serviceForm');
        const formData = new FormData(form);
        formData.append('action', 'save');
        
        // Debug: Log form data
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // If editing, keep existing image if no new file is selected
        if (!form.serviceImage.files.length && document.getElementById('existingImage')) {
            formData.append('existingImage', document.getElementById('existingImage').value);
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            console.log('Response headers:', response.headers); // Debug log
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.text().then(text => {
                console.log('Raw response:', text); // Debug log
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Invalid JSON response: ' + text);
                }
            });
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            if (data.success) {
                alert('Service saved successfully!');
                location.reload();
            } else {
                alert(data.error || 'Error saving service');
            }
        })
        .catch(error => {
            console.error('Error:', error); // Debug log
            alert('Error saving service: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    let serviceToDelete = null;
    
    function deleteService(id) {
        serviceToDelete = id;
        document.getElementById('deleteModal').style.display = 'block';
    }
    
    function confirmDelete() {
        if (serviceToDelete) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', serviceToDelete);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Service deleted successfully!');
                    location.reload();
                } else {
                    alert(data.error || 'Error deleting service');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting service: ' + error.message);
            });
        }
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        serviceToDelete = null;
    }
    
    // Image preview functionality
    document.getElementById('serviceImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').style.backgroundImage = `url(${e.target.result})`;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const serviceModal = document.getElementById('serviceModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === serviceModal) {
            closeServiceModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeServiceModal();
            closeDeleteModal();
        }
    });
    </script>
</body>
</html>

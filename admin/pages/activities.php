<?php
require_once '../../include/connection.php';

// Handle form submission for adding/editing activities
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['activityTitle'];
    $content = $_POST['activityContent'];
    $display_order = isset($_POST['displayOrder']) ? (int)$_POST['displayOrder'] : 1;
    $is_active = isset($_POST['isActive']) ? 1 : 0;
    
    // Handle image upload
    $image = '';
    $upload_dir = '../../uploads/activities/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    if (isset($_FILES['activityImage']) && $_FILES['activityImage']['error'] == 0) {
        $temp_name = $_FILES['activityImage']['tmp_name'];
        $file_name = time() . '_' . $_FILES['activityImage']['name'];
        $destination = $upload_dir . $file_name;
        
        if (move_uploaded_file($temp_name, $destination)) {
            $image = $file_name;
        }
    } elseif (isset($_POST['existingImage']) && !empty($_POST['existingImage'])) {
        $image = $_POST['existingImage'];
    }
    
    // Insert or update record
    if (isset($_POST['activity_id']) && !empty($_POST['activity_id'])) {
        // Update existing activity
        $id = (int)$_POST['activity_id'];
        $sql = "UPDATE activities SET 
                title = ?, 
                content = ?, 
                display_order = ?, 
                is_active = ?";
        
        $params = [$title, $content, $display_order, $is_active];
        
        if (!empty($image)) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params) - 1) . 'i'; // All strings except the last one (id) which is integer
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $success_message = "Activity updated successfully!";
        } else {
            $error_message = "Error updating activity: " . $conn->error;
        }
        $stmt->close();
    } else {
        // Insert new activity
        $sql = "INSERT INTO activities (title, content, image, display_order, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $title, $content, $image, $display_order, $is_active);
        
        if ($stmt->execute()) {
            $success_message = "Activity added successfully!";
        } else {
            $error_message = "Error adding activity: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle activity deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get image filename before deleting the record
    $stmt = $conn->prepare("SELECT image FROM activities WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_to_delete = $row['image'];
        
        // Delete the record from database
        $delete_stmt = $conn->prepare("DELETE FROM activities WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Delete the image file if it exists
            if (!empty($image_to_delete)) {
                $file_path = '../../uploads/activities/' . $image_to_delete;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $success_message = "Activity deleted successfully!";
        } else {
            $error_message = "Error deleting activity: " . $conn->error;
        }
        $delete_stmt->close();
    }
    $stmt->close();
}

// Fetch all activities
$activities = [];
$sql = "SELECT * FROM activities ORDER BY display_order ASC, created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Activities - Admin Panel</title>
    <link rel="stylesheet" href="../../css/activities-admin-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="admin-container">
        <h1>Manage Activities</h1>
        
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <div class="form-section">
            <div class="section-header">
                <h2>Activities List</h2>
                <button type="button" class="btn-add" onclick="openActivityModal()">
                    <i class="fas fa-plus"></i> Add New Activity
                </button>
            </div>
            <div class="activities-grid" id="activitiesGrid">
                <?php if (count($activities) > 0): ?>
                    <?php foreach ($activities as $index => $activity): ?>
                        <div class="activity-card<?php echo $activity['is_active'] ? '' : ' inactive'; ?>">
                            <div class="activity-card-image">
                                <?php if (!empty($activity['image'])): ?>
                                    <img src="../../uploads/activities/<?php echo $activity['image']; ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="activity-card-content">
                                <h3><?php echo htmlspecialchars($activity['title']); ?></h3>
                                <p class="content"><?php echo htmlspecialchars($activity['content']); ?></p>
                                <div class="activity-meta">
                                    <span class="order-badge" title="Display Order"><i class="fas fa-sort-numeric-down"></i> <?php echo $activity['display_order']; ?></span>
                                    <span class="status-badge <?php echo $activity['is_active'] ? 'active' : 'inactive'; ?>">
                                        <i class="fas <?php echo $activity['is_active'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                        <?php echo $activity['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="activity-card-actions">
                                <button onclick="editActivity(<?php echo $activity['id']; ?>)" class="btn-edit" data-tooltip="Edit Activity">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?php echo $activity['id']; ?>" onclick="return confirm('Are you sure you want to delete this activity?')" class="btn-delete" data-tooltip="Delete Activity">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-activities">
                        <i class="fas fa-hiking"></i>
                        <p>No activities found. Click the "Add New Activity" button to create your first activity.</p>
                        <button type="button" class="btn-primary" onclick="openActivityModal()">
                            <i class="fas fa-plus"></i> Create First Activity
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Activity Modal -->
    <div id="activityModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Activity</h2>
                <button class="close-modal" onclick="closeActivityModal()" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="activityForm" method="POST" enctype="multipart/form-data" action="">
                    <input type="hidden" id="activity_id" name="activity_id" value="">
                    <input type="hidden" id="existingImage" name="existingImage" value="">
                    
                    <div class="form-group">
                        <label for="activityTitle">Title</label>
                        <input type="text" id="activityTitle" name="activityTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="activityImage">Image</label>
                        <input type="file" id="activityImage" name="activityImage" accept="image/*">
                        <div class="image-preview" id="imagePreview"></div>
                        <small class="form-text">Recommended size: 800x600 pixels. Max file size: 2MB.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="activityContent">Content</label>
                        <textarea id="activityContent" name="activityContent" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="displayOrder">Display Order</label>
                        <input type="number" id="displayOrder" name="displayOrder" min="1" value="1">
                        <small class="form-text">Lower numbers will be displayed first.</small>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="isActive" name="isActive" checked>
                        <label for="isActive">Active</label>
                        <small class="form-text">Inactive activities won't be displayed on the website.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Activity</button>
                        <button type="button" class="btn-secondary" onclick="closeActivityModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openActivityModal() {
            // Reset form
            document.getElementById('activityForm').reset();
            document.getElementById('activity_id').value = '';
            document.getElementById('existingImage').value = '';
            document.getElementById('imagePreview').innerHTML = '';
            document.getElementById('modalTitle').textContent = 'Add New Activity';
            
            // Show modal with improved animation
            const modal = document.getElementById('activityModal');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            modal.style.display = 'flex';
            requestAnimationFrame(() => {
                modal.classList.add('show');
            });
        }

        function closeActivityModal() {
            const modal = document.getElementById('activityModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = ''; // Restore scrolling
            }, 300);
        }

        // Enhanced image preview functionality
        document.getElementById('activityImage').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const file = this.files[0];
                
                // Check file size
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    alert('File size exceeds 2MB. Please choose a smaller image.');
                    this.value = ''; // Clear the input
                    return;
                }
                
                // Show loading indicator
                preview.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
                
                reader.onload = function(e) {
                    // Create image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Preview';
                    img.style.opacity = '0';
                    
                    // Replace loading spinner with image
                    preview.innerHTML = '';
                    preview.appendChild(img);
                    
                    // Fade in the image
                    setTimeout(() => {
                        img.style.transition = 'opacity 0.3s ease-in-out';
                        img.style.opacity = '1';
                    }, 50);
                }
                
                reader.readAsDataURL(file);
            } else {
                // Show no image placeholder
                preview.innerHTML = '<div class="no-image"><i class="fas fa-image"></i></div>';
            }
        });

        // Edit activity function
        function editActivity(id) {
            // Fetch activity data via AJAX
            fetch(`get_activity.php?id=${id}`)
                .then(response => response.json())
                .then(activity => {
                    // Populate form
                    document.getElementById('activity_id').value = activity.id;
                    document.getElementById('activityTitle').value = activity.title;
                    document.getElementById('activityContent').value = activity.content;
                    document.getElementById('displayOrder').value = activity.display_order;
                    document.getElementById('isActive').checked = activity.is_active == 1;
                    
                    // Set existing image
                    if (activity.image) {
                        document.getElementById('existingImage').value = activity.image;
                        document.getElementById('imagePreview').innerHTML = 
                            `<img src="../../uploads/activities/${activity.image}" alt="Preview">`;
                    } else {
                        document.getElementById('imagePreview').innerHTML = '';
                    }
                    
                    // Update modal title
                    document.getElementById('modalTitle').textContent = 'Edit Activity';
                    
                    // Show modal
                    const modal = document.getElementById('activityModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('show'), 10);
                })
                .catch(error => {
                    console.error('Error fetching activity:', error);
                    alert('Error loading activity data. Please try again.');
                });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('activityModal');
            if (event.target === modal) {
                closeActivityModal();
            }
        }
        
        // Enhanced animations and UI interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add staggered animation to cards
            setTimeout(() => {
                const cards = document.querySelectorAll('.activity-card');
                cards.forEach((card, index) => {
                    // Staggered animation delay
                    card.style.animationDelay = `${0.08 * (index + 1)}s`;
                    
                    // Add hover effect listeners
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px)';
                        this.style.boxShadow = 'var(--shadow-lg)';
                    });
                    
                    card.addEventListener('mouseleave', function() {
                        this.style.transform = '';
                        this.style.boxShadow = '';
                    });
                });
            }, 100);
            
            // Improved alert messages with animation
            const alerts = document.querySelectorAll('.alert');
            if (alerts.length > 0) {
                // Add initial animation
                alerts.forEach(alert => {
                    alert.style.animation = 'slideInDown 0.4s ease-out';
                });
                
                // Set timeout for auto-dismiss
                setTimeout(() => {
                    alerts.forEach(alert => {
                        alert.style.animation = 'slideOutUp 0.5s ease-in forwards';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                }, 5000);
            }
            
            // Add tooltip functionality to buttons
            const buttons = document.querySelectorAll('[data-tooltip]');
            buttons.forEach(button => {
                const tooltip = document.createElement('span');
                tooltip.className = 'tooltip';
                tooltip.textContent = button.getAttribute('data-tooltip');
                button.appendChild(tooltip);
                
                button.addEventListener('mouseenter', () => {
                    tooltip.style.opacity = '1';
                    tooltip.style.transform = 'translateY(0)';
                });
                
                button.addEventListener('mouseleave', () => {
                    tooltip.style.opacity = '0';
                    tooltip.style.transform = 'translateY(10px)';
                });
            });
        });
        
        // Add keydown event for modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeActivityModal();
            }
        });
    </script>
</body>
</html>

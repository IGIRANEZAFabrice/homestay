<?php
/**
 * Activities Management - Edit Activity
 * Professional admin interface for editing activities
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Include image helpers
require_once '../../../include/image_helpers.php';

// Include database connection and helpers
require_once '../../backend/database/connection.php';
require_once '../../backend/api/utils/helpers.php';
require_once '../../backend/api/utils/validation.php';
require_once '../../backend/api/utils/image-handler.php';

// Get current user
$current_user = getCurrentUser();

// Get activity ID
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$activity_id) {
    redirectWithMessage('index.php', 'Invalid activity ID.', 'danger');
}

// Get activity data
$activity = getSingleRow("SELECT * FROM activities WHERE id = ?", 'i', [$activity_id]);

if (!$activity) {
    redirectWithMessage('index.php', 'Activity not found.', 'danger');
}

// Initialize variables
$errors = [];
$form_data = [
    'title' => $activity['title'],
    'content' => $activity['content'],
    'display_order' => $activity['display_order'],
    'is_active' => $activity['is_active']
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'display_order' => intval($_POST['display_order'] ?? 1),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'content' => ['required' => true, 'length' => [10, 5000]],
        'display_order' => ['required' => true, 'integer' => [1, 999]]
    ];
    
    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);
    
    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }
    
    // Handle image upload
    $image_path = $activity['image']; // Keep existing image by default
    $delete_existing_image = isset($_POST['delete_existing_image']);
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/activities/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85
        ]);
        
        if ($upload_result['success']) {
            // Delete old image if exists
            if (!empty($activity['image'])) {
                // Construct full path for deletion - handle both old and new formats
                $old_image_path = (strpos($activity['image'], 'uploads/') === 0)
                    ? $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $activity['image']
                    : $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/activities/' . $activity['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image_path = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    } elseif ($delete_existing_image) {
        // Delete existing image
        if (!empty($activity['image'])) {
            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $activity['image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_path = '';
    }
    
    // If no validation errors, update database
    if (empty($errors)) {
        try {
            $query = "UPDATE activities 
                      SET title = ?, content = ?, image = ?, display_order = ?, is_active = ?, updated_at = NOW() 
                      WHERE id = ?";
            
            $result = updateData($query, 'sssiii', [
                $form_data['title'],
                $form_data['content'],
                $image_path,
                $form_data['display_order'],
                $form_data['is_active'],
                $activity_id
            ]);
            
            if ($result !== false) {
                // Log activity
                logActivity('update_activity', "Updated activity: {$form_data['title']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Activity updated successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to update activity. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error updating activity: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while updating the activity.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Activities', 'url' => 'index.php'],
    ['title' => 'Edit Activity', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="../dashboard.php" class="sidebar-logo">
                    <i class="fas fa-mountain"></i>
                    <span class="nav-text">Virunga Admin</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="../dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-hiking"></i>
                        <span class="nav-text">Activities</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../blogs/index.php" class="nav-link">
                        <i class="fas fa-blog"></i>
                        <span class="nav-text">Blogs</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../cars/index.php" class="nav-link">
                        <i class="fas fa-car"></i>
                        <span class="nav-text">Cars</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../events/index.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Events</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../hero-images/index.php" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span class="nav-text">Hero Images</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../reviews/index.php" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span class="nav-text">Reviews</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../rooms/index.php" class="nav-link">
                        <i class="fas fa-bed"></i>
                        <span class="nav-text">Rooms</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../services/index.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="nav-text">Services</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="../contact-messages/index.php" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span class="nav-text">Messages</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">Edit Activity</h1>
                </div>
                
                <div class="header-right">
                    <div class="user-dropdown">
                        <div class="user-info">
                            <div class="user-avatar">
                                <?= strtoupper(substr($current_user['username'], 0, 1)) ?>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($current_user['username']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="../../backend/api/auth/logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                <!-- Breadcrumb -->
                <?= generateBreadcrumb($breadcrumbs) ?>

                <!-- Form Container -->
                <div class="form-container">
                    <div class="form-header">
                        <h2 class="form-title">Edit Activity</h2>
                        <p class="form-subtitle">Update activity information</p>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" data-validate="true">
                        <div class="form-row">
                            <div class="form-col-8">
                                <div class="form-group">
                                    <label for="title" class="form-label required">Activity Title</label>
                                    <input 
                                        type="text" 
                                        id="title" 
                                        name="title" 
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['title']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="Enter activity title"
                                    >
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input 
                                        type="number" 
                                        id="display_order" 
                                        name="display_order" 
                                        class="form-control <?= isset($errors['display_order']) ? 'is-invalid' : '' ?>" 
                                        value="<?= $form_data['display_order'] ?>"
                                        min="1"
                                        max="999"
                                    >
                                    <?php if (isset($errors['display_order'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['display_order']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label required">Activity Description</label>
                            <textarea 
                                id="content" 
                                name="content" 
                                class="form-control large <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                                required
                                data-min-length="10"
                                data-max-length="5000"
                                placeholder="Describe the activity in detail..."
                            ><?= htmlspecialchars($form_data['content']) ?></textarea>
                            <?php if (isset($errors['content'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['content']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">Activity Image</label>
                            
                            <?php if (!empty($activity['image'])): ?>
                                <div class="existing-image-preview" style="margin-bottom: 15px;">
                                    <img src="<?= buildAdminImageUrl($activity['image'], 'activities') ?>"
                                         alt="Current image"
                                         style="max-width: 200px; max-height: 150px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="margin-top: 10px;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                                            <input type="checkbox" name="delete_existing_image" value="1">
                                            Delete current image
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="file-upload-container">
                                <input 
                                    type="file" 
                                    id="image" 
                                    name="image" 
                                    class="file-upload-input" 
                                    accept="image/*"
                                >
                                <label for="image" class="file-upload-label">
                                    <div class="file-upload-content">
                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                        <div class="file-upload-text">
                                            <strong>Click to upload new image</strong> or drag and drop<br>
                                            <small>PNG, JPG, GIF up to 20MB</small>
                                        </div>
                                    </div>
                                </label>
                                <div class="image-preview"></div>
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    id="is_active" 
                                    name="is_active" 
                                    class="form-check-input" 
                                    <?= $form_data['is_active'] ? 'checked' : '' ?>
                                >
                                <label for="is_active" class="form-check-label">
                                    Active (visible on website)
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Activity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/forms.js"></script>
    <script src="../../assets/js/image-upload.js"></script>
</body>
</html>

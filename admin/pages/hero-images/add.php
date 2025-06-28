<?php
/**
 * Hero Images Management - Add New Hero Image
 * Professional admin interface for adding hero images
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Include database connection and helpers
require_once '../../backend/database/connection.php';
require_once '../../backend/api/utils/helpers.php';
require_once '../../backend/api/utils/validation.php';
require_once '../../backend/api/utils/image-handler.php';

// Get current user
$current_user = getCurrentUser();

// Initialize variables
$errors = [];
$form_data = [
    'title' => '',
    'description' => '',
    'is_active' => 1
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'description' => ['required' => false, 'length' => [0, 500]]
    ];
    
    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);
    
    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }
    
    // Handle image upload (required for hero images)
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/hero/', [
            'max_width' => 1920,
            'max_height' => 1080,
            'quality' => 90,
            'optimize' => true
        ]);
        
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    } else {
        $errors['image'] = 'Hero image is required.';
    }
    
    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            // Get next display order
            $max_order_result = getSingleRow("SELECT MAX(display_order) as max_order FROM hero_images");
            $next_order = ($max_order_result['max_order'] ?? 0) + 1;
            
            $query = "INSERT INTO hero_images (title, paragraph, image, is_active, display_order, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

            $result = insertData($query, 'sssii', [
                $form_data['title'],
                $form_data['description'],
                $image_path,
                $form_data['is_active'],
                $next_order
            ]);
            
            if ($result) {
                // Log activity (non-critical operation)
                try {
                    if (function_exists('logActivity')) {
                        logActivity('create_hero_image', "Created hero image: {$form_data['title']}");
                    }
                } catch (Exception $log_error) {
                    error_log('Activity logging failed: ' . $log_error->getMessage());
                    // Don't let logging failure break the create operation
                }

                // Redirect with success message
                redirectWithMessage('index.php', 'Hero image created successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to create hero image. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error creating hero image: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while creating the hero image.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Hero Images', 'url' => 'index.php'],
    ['title' => 'Add Hero Image', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hero Image - Virunga Homestay Admin</title>
    
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
                    <a href="../activities/index.php" class="nav-link">
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Add Hero Image</h1>
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
                        <h2 class="form-title">Add New Hero Image</h2>
                        <p class="form-subtitle">Upload a new hero image for your website homepage</p>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle alert-icon"></i>
                        <strong>Tip:</strong> For best results, use high-quality images with a resolution of 1920x1080 pixels or higher. The image will be automatically optimized for web display.
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data" data-validate="true">
                        <div class="form-group">
                            <label for="image" class="form-label required">Hero Image</label>
                            <div class="file-upload-container">
                                <input 
                                    type="file" 
                                    id="image" 
                                    name="image" 
                                    class="file-upload-input" 
                                    accept="image/*"
                                    required
                                >
                                <label for="image" class="file-upload-label">
                                    <div class="file-upload-content">
                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                        <div class="file-upload-text">
                                            <strong>Click to upload hero image</strong> or drag and drop<br>
                                            <small>PNG, JPG, GIF up to 20MB (Recommended: 1920x1080px)</small>
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
                            <label for="title" class="form-label required">Image Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                value="<?= htmlspecialchars($form_data['title']) ?>"
                                required
                                data-min-length="3"
                                data-max-length="255"
                                placeholder="Enter a descriptive title for the hero image"
                            >
                            <small class="form-text text-muted">This title may be displayed as an overlay on the image</small>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                data-max-length="500"
                                placeholder="Enter a brief description or caption for the hero image"
                                rows="3"
                            ><?= htmlspecialchars($form_data['description']) ?></textarea>
                            <small class="form-text text-muted">Optional description that may be displayed with the image</small>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
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
                            <small class="form-text text-muted">Inactive images won't be displayed on your website but will be saved for later use</small>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Hero Image
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

<?php
/**
 * Services Management - Edit Service
 * Professional admin interface for editing services
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

// Check and add status column if it doesn't exist (one-time migration)
try {
    $check_status_column = getSingleRow("SHOW COLUMNS FROM services LIKE 'status'");
    if (!$check_status_column) {
        // Add status column
        executeQuery("ALTER TABLE services ADD COLUMN status enum('active','inactive') DEFAULT 'active' AFTER is_active");
        error_log('Added status column to services table');
    }
} catch (Exception $e) {
    error_log('Error checking/adding status column: ' . $e->getMessage());
}

// Get service ID
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$service_id) {
    redirectWithMessage('index.php', 'Invalid service ID.', 'danger');
}

// Get service data
$service = getSingleRow("SELECT * FROM services WHERE id = ?", 'i', [$service_id]);

if (!$service) {
    redirectWithMessage('index.php', 'Service not found.', 'danger');
}

// Initialize variables
$errors = [];
$form_data = [
    'title' => $service['title'],
    'description' => $service['description'],
    'image' => $service['image'],
    'display_order' => $service['display_order'],
    'is_active' => $service['is_active'],
    'status' => $service['status'] ?? 'active'  // Default to 'active' if column doesn't exist
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log form submission
    error_log('Services edit form submitted for service ID: ' . $service_id);
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image' => $service['image'], // Keep existing image by default
        'display_order' => intval($_POST['display_order'] ?? 1),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
        'status' => trim($_POST['status'] ?? 'active')
    ];

    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'description' => ['required' => true, 'length' => [10, 2000]],
        'display_order' => ['required' => false, 'number' => [1, 999]],
        'status' => ['required' => false, 'in' => ['active', 'inactive']]
    ];
    
    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);
    
    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/services/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85,
            'optimize' => true
        ]);

        if ($upload_result['success']) {
            // Delete old image if exists
            if (!empty($service['image'])) {
                $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/services/' . $service['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $form_data['image'] = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    }

    // If no validation errors, update database
    if (empty($errors)) {
        try {
            // Check if status column exists
            $check_status_column = getSingleRow("SHOW COLUMNS FROM services LIKE 'status'");

            if ($check_status_column) {
                // Status column exists, include it in update
                $query = "UPDATE services
                          SET title = ?, description = ?, image = ?, display_order = ?, is_active = ?, status = ?, updated_at = NOW()
                          WHERE id = ?";

                $result = updateData($query, 'sssiisi', [
                    $form_data['title'],
                    $form_data['description'],
                    $form_data['image'],
                    $form_data['display_order'],
                    $form_data['is_active'],
                    $form_data['status'],
                    $service_id
                ]);
            } else {
                // Status column doesn't exist, exclude it from update
                $query = "UPDATE services
                          SET title = ?, description = ?, image = ?, display_order = ?, is_active = ?, updated_at = NOW()
                          WHERE id = ?";

                $result = updateData($query, 'sssiii', [
                    $form_data['title'],
                    $form_data['description'],
                    $form_data['image'],
                    $form_data['display_order'],
                    $form_data['is_active'],
                    $service_id
                ]);
            }

            // Debug: Log update attempt
            error_log('Attempting to update service with data: ' . json_encode($form_data));

            // Debug: Log update result
            error_log('Update result: ' . ($result !== false ? 'success' : 'failed'));

            if ($result !== false) {
                // Log activity
                logActivity('update_service', "Updated service: {$form_data['title']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Service updated successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to update service. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error updating service: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while updating the service.';
        }
    }
}

// Categories are not used in current schema
$existing_categories = [];

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Services', 'url' => 'index.php'],
    ['title' => 'Edit Service', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .category-suggestions {
            margin-top: 5px;
        }
        
        .category-suggestion {
            display: inline-block;
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .category-suggestion:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .duration-examples {
            margin-top: 5px;
            font-size: 12px;
            color: var(--gray-600);
        }
        
        .duration-example {
            display: inline-block;
            background: var(--info-color-light);
            color: var(--info-color);
            padding: 2px 6px;
            border-radius: 8px;
            margin-right: 5px;
            margin-bottom: 3px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .duration-example:hover {
            background: var(--info-color);
            color: white;
        }
    </style>
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Edit Service</h1>
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
                        <h2 class="form-title">Edit Service</h2>
                        <p class="form-subtitle">Update service information</p>
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
                                    <label for="title" class="form-label required">Service Title</label>
                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($form_data['title']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="Enter service title"
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
                                        placeholder="1"
                                    >
                                    <div class="form-help">
                                        Order in which services appear (1 = first)
                                    </div>
                                    <?php if (isset($errors['display_order'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['display_order']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Current Image -->
                        <?php if (!empty($service['image'])): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div class="current-image">
                                    <img src="/homestay/uploads/services/<?= htmlspecialchars($service['image']) ?>"
                                        alt="Current service image"
                                        style="max-width: 300px; height: auto; border-radius: 8px;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- New Image Upload -->
                        <div class="form-group">
                            <label for="image" class="form-label">Upload New Image</label>
                            <input
                                type="file"
                                id="image"
                                name="image"
                                class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                                accept="image/*"
                            >
                            <div class="form-help">
                                Supported formats: JPG, PNG, GIF. Maximum size: 5MB.
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label required">Service Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control large <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                required
                                data-min-length="10"
                                data-max-length="2000"
                                placeholder="Describe the service, what's included, and any special features..."
                                rows="5"
                            ><?= htmlspecialchars($form_data['description']) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-row">
                            <div class="form-col-6">
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
                                    <small class="form-text text-muted">Active services are displayed publicly on your website</small>
                                </div>
                            </div>
                            
                            <div class="form-col-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select
                                        id="status"
                                        name="status"
                                        class="form-control <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                    >
                                        <option value="active" <?= $form_data['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $form_data['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                    <small class="form-text text-muted">Service status for internal management</small>
                                    <?php if (isset($errors['status'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['status']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Service Information</label>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Created:</strong> <?= formatDateTime($service['created_at']) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Last Updated:</strong> <?= formatDateTime($service['updated_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Service
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
    
    <script>
        // Form enhancement scripts can be added here if needed
    </script>
</body>
</html>

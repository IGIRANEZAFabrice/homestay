<?php
/**
 * Rooms Management - Edit Room
 * Professional admin interface for editing rooms
 */

// Suppress warnings for production
error_reporting(E_ERROR | E_PARSE);

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

// Try to include image helpers, with fallback function
if (file_exists('../../../include/image_helpers.php')) {
    require_once '../../../include/image_helpers.php';
} else {
    // Fallback function if image helpers not found
    function buildAdminImageUrl($filename, $category) {
        if (empty($filename)) {
            return '';
        }

        // Handle both filename-only and full path cases
        if (strpos($filename, 'uploads/') === 0) {
            // Already a full path
            return '/homestay/' . $filename;
        } else {
            // Build path from filename
            return '/homestay/uploads/' . $category . '/' . $filename;
        }
    }
}

// Get current user
$current_user = getCurrentUser();

// Get room ID
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$room_id) {
    redirectWithMessage('index.php', 'Invalid room ID.', 'danger');
}

// Get room data
$room = getSingleRow("SELECT * FROM rooms WHERE id = ?", 'i', [$room_id]);

if (!$room) {
    redirectWithMessage('index.php', 'Room not found.', 'danger');
}

// Initialize variables
$errors = [];
$form_data = [
    'title' => $room['title'] ?? '',
    'description' => $room['description'] ?? '',
    'status' => $room['status'] ?? 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'status' => $_POST['status'] ?? 'active'
    ];

    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'description' => ['required' => true, 'length' => [10, 2000]]
    ];

    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);

    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }

    // Validate status
    $allowed_statuses = ['active', 'inactive', 'maintenance'];
    if (!in_array($form_data['status'], $allowed_statuses)) {
        $errors['status'] = 'Invalid status selected.';
    }

    // Handle image upload
    $image_path = $room['image']; // Keep existing image by default
    $delete_existing_image = isset($_POST['delete_existing_image']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/rooms/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85,
            'optimize' => true
        ]);

        if ($upload_result['success']) {
            // Delete old image if exists
            if (!empty($room['image'])) {
                // Handle both filename-only and full path cases
                if (strpos($room['image'], 'uploads/') === 0) {
                    // Full path stored in database
                    $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $room['image'];
                } else {
                    // Only filename stored in database
                    $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/rooms/' . $room['image'];
                }
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
        if (!empty($room['image'])) {
            // Handle both filename-only and full path cases
            if (strpos($room['image'], 'uploads/') === 0) {
                // Full path stored in database
                $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $room['image'];
            } else {
                // Only filename stored in database
                $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/rooms/' . $room['image'];
            }
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_path = '';
    }

    // If no validation errors, update database
    if (empty($errors)) {
        try {
            $query = "UPDATE rooms
                      SET title = ?, description = ?, image = ?, status = ?, updated_at = NOW()
                      WHERE id = ?";

            $result = updateData($query, 'ssssi', [
                $form_data['title'],
                $form_data['description'],
                $image_path,
                $form_data['status'],
                $room_id
            ]);

            if ($result !== false) {
                // Log activity (non-critical operation)
                try {
                    if (function_exists('logActivity')) {
                        logActivity('update_room', "Updated room: {$form_data['title']}");
                    }
                } catch (Exception $log_error) {
                    error_log('Activity logging failed: ' . $log_error->getMessage());
                    // Don't let logging failure break the update operation
                }

                // Redirect with success message
                redirectWithMessage('index.php', 'Room updated successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to update room. Please try again.';
            }

        } catch (Exception $e) {
            error_log('Error updating room: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while updating the room.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Rooms', 'url' => 'index.php'],
    ['title' => 'Edit Room', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room - Virunga Homestay Admin</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .current-image {
            margin-bottom: 15px;
        }

        .current-image img {
            max-width: 300px;
            height: auto;
            border-radius: 8px;
            border: 1px solid var(--gray-300);
        }

        .image-actions {
            margin-top: 10px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-label input[type="checkbox"] {
            margin: 0;
        }

        .form-help {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: 5px;
        }

        /* Enhanced Image Upload Styles */
        .image-upload-area {
            position: relative;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #f8fafc;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .image-upload-area:hover {
            border-color: #6366f1;
            background-color: #f1f5f9;
        }

        .image-upload-area.dragover {
            border-color: #6366f1;
            background-color: #f1f5f9;
            transform: scale(1.02);
        }

        .image-upload-area input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .upload-placeholder {
            pointer-events: none;
            color: #475569;
        }

        .upload-placeholder i {
            font-size: 2rem;
            color: #94a3b8;
            margin-bottom: 10px;
            display: block;
        }

        .upload-placeholder p {
            margin: 10px 0 5px 0;
            font-weight: 500;
            color: #334155;
        }

        .upload-placeholder small {
            color: #64748b;
            font-size: 0.875rem;
        }

        .image-preview-area {
            margin-top: 15px;
        }

        .image-preview {
            position: relative;
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--gray-300);
        }

        .image-preview img {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
        }

        .image-preview .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(231, 76, 60, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview .remove-image:hover {
            background: var(--danger);
        }

        /* Enhanced Button Styles */
        .btn-lg {
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-200);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 2px solid var(--gray-400);
            color: var(--gray-600);
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: var(--gray-100);
            border-color: var(--gray-500);
            color: var(--gray-700);
        }

        /* Loading state for buttons */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn .fa-spinner {
            margin-right: 8px;
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
                    <a href="index.php" class="nav-link">
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
                    <a href="../rooms/index.php" class="nav-link active">
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
                    <h1 class="page-title">Add room post</h1>
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
                        <h2>Edit Room</h2>
                        <p>Update room information and settings</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <div>
                                <strong>Please correct the following errors:</strong>
                                <ul class="error-list">
                                    <?php foreach ($errors as $field => $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <div class="form-grid">
                            <!-- Room Title -->
                            <div class="form-group">
                                <label for="title" class="form-label required">Room Title</label>
                                <input type="text" id="title" name="title"
                                    class="form-control <?= isset($errors['title']) ? 'error' : '' ?>"
                                    value="<?= htmlspecialchars($form_data['title']) ?>" placeholder="Enter room title"
                                    required>
                                <?php if (isset($errors['title'])): ?>
                                    <div class="form-error"><?= htmlspecialchars($errors['title']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Status -->
                            <div class="form-group">
                                <label for="status" class="form-label required">Status</label>
                                <select id="status" name="status"
                                    class="form-control <?= isset($errors['status']) ? 'error' : '' ?>" required>
                                    <option value="active" <?= $form_data['status'] === 'active' ? 'selected' : '' ?>>
                                        Active</option>
                                    <option value="inactive" <?= $form_data['status'] === 'inactive' ? 'selected' : '' ?>>
                                        Inactive</option>
                                    <option value="maintenance" <?= $form_data['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                </select>
                                <?php if (isset($errors['status'])): ?>
                                    <div class="form-error"><?= htmlspecialchars($errors['status']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label required">Description</label>
                            <textarea id="description" name="description"
                                class="form-control <?= isset($errors['description']) ? 'error' : '' ?>" rows="6"
                                placeholder="Enter room description"
                                required><?= htmlspecialchars($form_data['description']) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Current Image -->
                        <?php if (!empty($room['image'])): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div class="current-image">
                                    <?php
                                    $image_url = buildAdminImageUrl($room['image'], 'rooms');
                                    // Debug: Show the image URL being generated
                                    // echo "<!-- Debug: Image URL: " . htmlspecialchars($image_url) . " -->";
                                    ?>
                                    <img src="<?= $image_url ?>"
                                        alt="Current room image"
                                        style="max-width: 300px; height: auto; border-radius: 8px;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div style="display: none; padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-align: center;">
                                        <i class="fas fa-image" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                                        <p style="margin: 0; color: #6c757d;">Image not found</p>
                                        <small style="color: #6c757d;">File: <?= htmlspecialchars($room['image']) ?></small>
                                    </div>
                                    <div class="image-actions">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="delete_existing_image" value="1">
                                            <span class="checkmark"></span>
                                            Delete current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="form-group">
                                <label class="form-label">Current Image</label>
                                <div style="padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-align: center;">
                                    <i class="fas fa-image" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                                    <p style="margin: 0; color: #6c757d;">No image uploaded</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- New Image Upload -->
                        <div class="form-group">
                            <label for="image" class="form-label">Upload New Image</label>
                            <div class="image-upload-area">
                                <input type="file" id="image" name="image"
                                    class="form-control <?= isset($errors['image']) ? 'error' : '' ?>"
                                    accept="image/*" data-upload="image">
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to select image or drag and drop</p>
                                    <small>Supported formats: JPG, PNG, GIF. Maximum size: 5MB.</small>
                                </div>
                                <div class="image-preview-area"></div>
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg" id="updateRoomBtn">
                                <i class="fas fa-save"></i> Update Room
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back to Rooms
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo"></i> Reset Changes
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

    <script>
        // Enhanced form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.admin-form');
            const updateBtn = document.getElementById('updateRoomBtn');
            const originalFormData = new FormData(form);

            // Initialize image upload
            if (typeof ImageUpload !== 'undefined') {
                ImageUpload.init();
            }

            // Form submission handling
            form.addEventListener('submit', function(e) {
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            });

            // Reset form function
            window.resetForm = function() {
                if (confirm('Are you sure you want to reset all changes?')) {
                    form.reset();
                    // Reset image preview
                    const previewArea = document.querySelector('.image-preview-area');
                    if (previewArea) {
                        previewArea.innerHTML = '';
                    }
                    // Clear validation errors
                    const errorElements = form.querySelectorAll('.form-error');
                    errorElements.forEach(error => error.remove());

                    const errorInputs = form.querySelectorAll('.error');
                    errorInputs.forEach(input => input.classList.remove('error'));
                }
            };

            // Track form changes
            let hasChanges = false;
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('change', function() {
                    hasChanges = true;
                });
            });

            // Warn before leaving with unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Remove warning after successful submission
            form.addEventListener('submit', function() {
                hasChanges = false;
            });
        });
    </script>
</body>

</html>
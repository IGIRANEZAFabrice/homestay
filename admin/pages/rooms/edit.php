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
                                        // Handle both filename-only and full path cases
                                        $image_src = (strpos($room['image'], 'uploads/') === 0)
                                            ? '/homestay/' . $room['image']
                                            : '/homestay/uploads/rooms/' . $room['image'];
                                    ?>
                                    <img src="<?= htmlspecialchars($image_src) ?>"
                                        alt="Current room image"
                                        style="max-width: 300px; height: auto; border-radius: 8px;">
                                    <div class="image-actions">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="delete_existing_image" value="1">
                                            <span class="checkmark"></span>
                                            Delete current image
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- New Image Upload -->
                        <div class="form-group">
                            <label for="image" class="form-label">Upload New Image</label>
                            <input type="file" id="image" name="image"
                                class="form-control <?= isset($errors['image']) ? 'error' : '' ?>" accept="image/*">
                            <div class="form-help">
                                Supported formats: JPG, PNG, GIF. Maximum size: 5MB.
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="form-error"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Room
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/forms.js"></script>
</body>

</html>
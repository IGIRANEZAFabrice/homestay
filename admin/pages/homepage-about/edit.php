<?php
/**
 * Homepage About Section Management - Edit/Create Form
 * Professional admin interface for editing homepage_about content
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
require_once '../../backend/api/utils/image-handler.php';

// Get current user
$current_user = getCurrentUser();

// Initialize variables
$homepage_about = null;
$is_edit = false;
$errors = [];
$success_message = '';

// Check if editing existing content
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM homepage_about WHERE id = ?";
    $result = executeQuery($query, 'i', [$id]);

    if ($result && $result->num_rows > 0) {
        $homepage_about = $result->fetch_assoc();
        $is_edit = true;
    } else {
        redirectWithMessage('index.php', 'Homepage about content not found.', 'danger');
    }
} else {
    // Check if content already exists (only one record allowed)
    $query = "SELECT id FROM homepage_about LIMIT 1";
    $result = executeQuery($query);

    if ($result && $result->num_rows > 0) {
        $existing = $result->fetch_assoc();
        header('Location: edit.php?id=' . $existing['id']);
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Validate and sanitize input
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validation
        if (empty($title)) {
            $errors[] = 'Title is required.';
        } elseif (strlen($title) > 255) {
            $errors[] = 'Title must be less than 255 characters.';
        }

        if (empty($description)) {
            $errors[] = 'Description is required.';
        }

        // Handle image upload
        $image_path = $homepage_about['image'] ?? '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadImage($_FILES['image'], 'uploads/homeabout/', [
                'max_width' => 1200,
                'max_height' => 800,
                'quality' => 85,
                'optimize' => true
            ]);

            if ($upload_result['success']) {
                // Delete old image if exists and different
                if (!empty($image_path) && $image_path !== $upload_result['path']) {
                    deleteImage($image_path);
                }
                $image_path = $upload_result['path'];
            } else {
                $errors[] = $upload_result['message'];
            }
        } elseif (empty($image_path)) {
            $errors[] = 'Image is required.';
        }

        // If no errors, save to database
        if (empty($errors)) {
            try {
                if ($is_edit) {
                    // Update existing record
                    $query = "UPDATE homepage_about SET title = ?, description = ?, image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                    $result = executeQuery($query, 'sssi', [$title, $description, $image_path, $homepage_about['id']]);
                } else {
                    // Insert new record
                    $query = "INSERT INTO homepage_about (title, description, image) VALUES (?, ?, ?)";
                    $result = executeQuery($query, 'sss', [$title, $description, $image_path]);
                }

                if ($result) {
                    $success_message = $is_edit ? 'Homepage about content updated successfully!' : 'Homepage about content created successfully!';
                    redirectWithMessage('index.php', $success_message, 'success');
                } else {
                    $errors[] = 'Failed to save homepage about content. Please try again.';
                }
            } catch (Exception $e) {
                error_log("Homepage about save error: " . $e->getMessage());
                $errors[] = 'An error occurred while saving. Please try again.';
            }
        }
    }
}

$page_title = $is_edit ? "Edit Homepage About Content" : "Create Homepage About Content";
$current_section = "homepage-about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Virunga Homestay Admin</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Enhanced styles for homepage about edit form */
        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-section {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            color: var(--white);
            padding: 25px 30px;
            border-bottom: none;
        }

        .section-header h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .form-grid {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--gray-800);
            font-size: 14px;
        }

        .form-label.required::after {
            content: ' *';
            color: var(--danger-color);
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 14px;
            line-height: 1.5;
            color: var(--gray-800);
            background-color: var(--white);
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-control::placeholder {
            color: var(--gray-500);
            font-style: italic;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .form-help {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 13px;
            color: var(--gray-600);
            line-height: 1.4;
        }

        .form-help i {
            color: var(--secondary-color);
        }

        /* File Upload Styles */
        .file-upload-container {
            position: relative;
            margin-bottom: 15px;
        }

        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 40px 20px;
            border: 2px dashed var(--gray-300);
            border-radius: var(--border-radius);
            background-color: var(--gray-50);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }

        .file-upload-label:hover {
            border-color: var(--secondary-color);
            background-color: rgba(52, 152, 219, 0.05);
        }

        .file-upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .file-upload-icon {
            font-size: 48px;
            color: var(--gray-400);
        }

        .file-upload-text {
            color: var(--gray-600);
            font-size: 14px;
            line-height: 1.5;
        }

        .file-upload-text strong {
            color: var(--secondary-color);
            font-weight: 600;
        }

        /* Current Image Display */
        .current-image-container {
            margin-top: 20px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .current-image-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
        }

        .current-image-header h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .image-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--success-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .current-image-preview {
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .current-image-display {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-sm);
            flex-shrink: 0;
        }

        .image-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .image-path {
            margin: 0;
            font-size: 13px;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: monospace;
            background: var(--white);
            padding: 8px 12px;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--gray-200);
        }

        .image-note {
            margin: 0;
            font-size: 13px;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 6px;
            font-style: italic;
        }

        /* Form Actions */
        .form-actions {
            background: var(--gray-50);
            padding: 30px;
            border-top: 1px solid var(--gray-200);
            text-align: center;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .btn-lg {
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
        }

        .btn-outline-secondary {
            color: var(--gray-600);
            border-color: var(--gray-300);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            color: var(--white);
            background: var(--gray-600);
            border-color: var(--gray-600);
        }

        .form-help-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-600);
            font-style: italic;
        }

        .form-help-text i {
            color: var(--secondary-color);
        }

        /* Character Counter */
        .char-counter {
            display: block;
            text-align: right;
            margin-top: 5px;
            font-size: 12px;
            color: var(--gray-500);
        }

        /* Image Preview for New Upload */
        .image-preview-new {
            margin-top: 15px;
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 2px solid var(--success-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-container {
                margin: 0 15px;
            }

            .section-header {
                padding: 20px;
            }

            .form-grid {
                padding: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .current-image-preview {
                flex-direction: column;
                gap: 15px;
            }

            .current-image-display {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="../../dashboard.php" class="sidebar-logo">
                    <i class="fas fa-mountain"></i>
                    <span class="nav-text">Virunga Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="../../dashboard.php" class="nav-link">
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
                    <a href="../services/index.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="nav-text">Services</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="../about-us/index.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        <span class="nav-text">About Us</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Homepage About</span>
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
                    <h1 class="page-title">Homepage About Section</h1>
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
                            <a href="../backend/api/auth/logout.php" class="dropdown-item">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="flash-message flash-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button class="flash-close">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if ($success_message): ?>
                <div class="flash-message flash-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                    <button class="flash-close">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Content Area -->
            <div class="admin-content">
                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data" class="admin-form" id="homepageAboutForm">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                        <!-- Content Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-home"></i> Homepage About Content</h3>
                                <p>Content that will appear in the about section on your homepage</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label required">Section Title</label>
                                    <input type="text" id="title" name="title" class="form-control"
                                        value="<?php echo htmlspecialchars($homepage_about['title'] ?? ''); ?>"
                                        maxlength="255" required
                                        placeholder="Enter a compelling title for your homepage about section">
                                    <small class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        Main title that will grab visitors' attention
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="form-label required">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="10"
                                        required
                                        placeholder="Write a compelling description that introduces visitors to your homestay. Tell them what makes your place special, the experience they can expect, and why they should choose you."><?php echo htmlspecialchars($homepage_about['description'] ?? ''); ?></textarea>
                                    <small class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        Compelling description that introduces visitors to your homestay and highlights
                                        what makes it special
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Image Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h3><i class="fas fa-image"></i> Featured Image</h3>
                                <p>Main image for the homepage about section</p>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="image"
                                        class="form-label <?php echo empty($homepage_about['image']) ? 'required' : ''; ?>">
                                        Upload New Image
                                    </label>

                                    <div class="file-upload-container">
                                        <input type="file" id="image" name="image" accept="image/*"
                                            class="file-upload-input" <?php echo empty($homepage_about['image']) ? 'required' : ''; ?>>
                                        <label for="image" class="file-upload-label">
                                            <div class="file-upload-content">
                                                <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                <div class="file-upload-text">
                                                    <strong>Click to upload</strong> or drag and drop<br>
                                                    <small>JPG, PNG, WebP (Max: 10MB)</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <small class="form-help">
                                        <i class="fas fa-info-circle"></i>
                                        Upload a high-quality image that represents your homestay. This will be
                                        prominently displayed on your homepage.
                                    </small>

                                    <?php if (!empty($homepage_about['image'])): ?>
                                        <div class="current-image-container">
                                            <div class="current-image-header">
                                                <h4><i class="fas fa-image"></i> Current Image</h4>
                                                <span class="image-status">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            </div>
                                            <div class="current-image-preview">
                                                <img src="../../../<?php echo htmlspecialchars($homepage_about['image']); ?>"
                                                    alt="Current homepage about image" class="current-image-display">
                                                <div class="image-info">
                                                    <p class="image-path">
                                                        <i class="fas fa-folder"></i>
                                                        <?php echo htmlspecialchars($homepage_about['image']); ?>
                                                    </p>
                                                    <p class="image-note">
                                                        <i class="fas fa-info-circle"></i>
                                                        Upload a new image to replace this one
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i>
                                    <?php echo $is_edit ? 'Update Content' : 'Create Content'; ?>
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>

                        <!-- Help Text Below Buttons -->
                        <div class="form-help-text" style="text-align: center; margin-top: 5px; padding: 5px; background: var(--gray-50); border-radius: var(--border-radius);">
                            <i class="fas fa-info-circle"></i>
                            <?php if ($is_edit): ?>
                                Changes will be immediately visible on your homepage after saving.
                            <?php else: ?>
                                Your homepage about content will be published immediately after creation.
                            <?php endif; ?>
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
    <script src="../../assets/js/utils.js"></script>

    <script>
        // Enhanced form validation and functionality
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('homepageAboutForm');
            const imageInput = document.getElementById('image');
            const fileUploadLabel = document.querySelector('.file-upload-label');

            // Enhanced image preview functionality
            if (imageInput) {
                imageInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (10MB max)
                        if (file.size > 10 * 1024 * 1024) {
                            showAlert('File size must be less than 10MB', 'error');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            showAlert('Please select a valid image file (JPG, PNG, WebP)', 'error');
                            this.value = '';
                            return;
                        }

                        // Update file upload label
                        const fileName = file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2);
                        fileUploadLabel.innerHTML = `
                            <div class="file-upload-content">
                                <i class="fas fa-check-circle file-upload-icon" style="color: var(--success-color);"></i>
                                <div class="file-upload-text">
                                    <strong>${fileName}</strong><br>
                                    <small>${fileSize} MB - Click to change</small>
                                </div>
                            </div>
                        `;

                        // Show preview
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            let preview = document.querySelector('.image-preview-new');
                            if (!preview) {
                                preview = document.createElement('img');
                                preview.className = 'image-preview-new';
                                imageInput.closest('.form-group').appendChild(preview);
                            }
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // Drag and drop functionality
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    fileUploadLabel.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    fileUploadLabel.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    fileUploadLabel.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e) {
                    fileUploadLabel.style.borderColor = 'var(--secondary-color)';
                    fileUploadLabel.style.backgroundColor = 'rgba(52, 152, 219, 0.1)';
                }

                function unhighlight(e) {
                    fileUploadLabel.style.borderColor = 'var(--gray-300)';
                    fileUploadLabel.style.backgroundColor = 'var(--gray-50)';
                }

                fileUploadLabel.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    imageInput.files = files;
                    imageInput.dispatchEvent(new Event('change'));
                }
            }

            // Enhanced form validation
            form.addEventListener('submit', function (e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                let firstInvalidField = null;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    showAlert('Please fill in all required fields', 'error');
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            // Real-time validation
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function () {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                });

                field.addEventListener('input', function () {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // Character count for text fields
            const textFields = form.querySelectorAll('input[maxlength], textarea');
            textFields.forEach(field => {
                const maxLength = field.getAttribute('maxlength');
                if (maxLength) {
                    const counter = document.createElement('small');
                    counter.className = 'char-counter';
                    field.parentNode.appendChild(counter);

                    const updateCounter = () => {
                        const remaining = maxLength - field.value.length;
                        counter.textContent = `${field.value.length}/${maxLength} characters`;
                        counter.style.color = remaining < 20 ? 'var(--danger-color)' : 'var(--gray-500)';
                    };

                    field.addEventListener('input', updateCounter);
                    updateCounter();
                }
            });

            // Auto-resize textarea
            const textareas = form.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });
            });
        });

        // Alert function
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} alert-icon"></i>
                ${message}
                <button type="button" class="alert-close">&times;</button>
            `;

            const container = document.querySelector('.admin-content');
            container.insertBefore(alertDiv, container.firstChild);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.style.animation = 'slideOutRight 0.3s ease-out forwards';
                    setTimeout(() => alertDiv.remove(), 300);
                }
            }, 5000);

            // Close button functionality
            alertDiv.querySelector('.alert-close').addEventListener('click', function () {
                alertDiv.style.animation = 'slideOutRight 0.3s ease-out forwards';
                setTimeout(() => alertDiv.remove(), 300);
            });
        }
    </script>
</body>

</html>
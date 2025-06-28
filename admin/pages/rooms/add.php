<?php
/**
 * Rooms Management - Add New Room
 * Professional admin interface for adding rooms
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

// Initialize variables
$errors = [];
$form_data = [
    'title' => '',
    'description' => '',
    'status' => 'active'
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
    
    // Handle single image upload
    $uploaded_image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/rooms/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85,
            'optimize' => true
        ]);

        if ($upload_result['success']) {
            $uploaded_image = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    }
    
    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            $query = "INSERT INTO rooms (title, description, image, status, created_at, updated_at)
                      VALUES (?, ?, ?, ?, NOW(), NOW())";

            $result = insertData($query, 'ssss', [
                $form_data['title'],
                $form_data['description'],
                $uploaded_image,
                $form_data['status']
            ]);

            if ($result) {
                // Log activity (non-critical operation)
                try {
                    if (function_exists('logActivity')) {
                        logActivity('create_room', "Created room: {$form_data['title']}");
                    }
                } catch (Exception $log_error) {
                    error_log('Activity logging failed: ' . $log_error->getMessage());
                    // Don't let logging failure break the create operation
                }

                // Redirect with success message
                redirectWithMessage('index.php', 'Room created successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to create room. Please try again.';
            }

        } catch (Exception $e) {
            error_log('Error creating room: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while creating the room.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Rooms', 'url' => 'index.php'],
    ['title' => 'Add Room', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>

        
        .multiple-file-upload {
            border: 2px dashed var(--gray-300);
            border-radius: var(--border-radius);
            padding: 40px 20px;
            text-align: center;
            background-color: var(--gray-50);
            transition: all 0.3s ease;
        }
        
        .multiple-file-upload:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-color-light);
        }
        
        .multiple-file-upload.dragover {
            border-color: var(--primary-color);
            background-color: var(--primary-color-light);
        }
        
        .file-preview-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .file-preview {
            position: relative;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .file-preview img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }
        
        .file-preview-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        
        .file-preview-remove:hover {
            background: #c0392b;
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Add Room</h1>
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
                        <h2 class="form-title">Add New Room</h2>
                        <p class="form-subtitle">Create a new room for your homestay</p>
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
                                    <label for="title" class="form-label required">Room Title</label>
                                    <input
                                        type="text"
                                        id="title"
                                        name="title"
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($form_data['title']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="Enter room title"
                                    >
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="status" class="form-label required">Status</label>
                                    <select
                                        id="status"
                                        name="status"
                                        class="form-control <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="active" <?= $form_data['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $form_data['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="maintenance" <?= $form_data['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                    </select>
                                    <?php if (isset($errors['status'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['status']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label for="description" class="form-label required">Room Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control large <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                required
                                data-min-length="10"
                                data-max-length="2000"
                                placeholder="Describe the room features, layout, and what makes it special..."
                                rows="5"
                            ><?= htmlspecialchars($form_data['description']) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>



                        <div class="form-group">
                            <label for="image" class="form-label">Room Image</label>
                            <input type="file"
                                   id="image"
                                   name="image"
                                   class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                                   accept="image/*">
                            <div class="form-help">
                                Supported formats: JPG, PNG, GIF. Maximum size: 5MB.
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>



                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Room
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

        
        // Multiple file upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('images');
            const previewContainer = document.getElementById('file-preview-container');
            let selectedFiles = [];
            
            // Click to upload
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            // Drag and drop
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
            });
            
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
            });
            
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
                
                const files = Array.from(e.dataTransfer.files);
                handleFiles(files);
            });
            
            // File input change
            fileInput.addEventListener('change', function() {
                const files = Array.from(this.files);
                handleFiles(files);
            });
            
            function handleFiles(files) {
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        selectedFiles.push(file);
                        createPreview(file, selectedFiles.length - 1);
                    }
                });
                updateFileInput();
            }
            
            function createPreview(file, index) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'file-preview';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="file-preview-remove" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
            
            window.removeFile = function(index) {
                selectedFiles.splice(index, 1);
                updatePreviews();
                updateFileInput();
            };
            
            function updatePreviews() {
                previewContainer.innerHTML = '';
                selectedFiles.forEach((file, index) => {
                    createPreview(file, index);
                });
            }
            
            function updateFileInput() {
                const dt = new DataTransfer();
                selectedFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }
        });
        

    </script>
</body>
</html>

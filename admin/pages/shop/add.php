<?php
/**
 * Shop Items Management - Add New Item
 * Admin Dashboard for Virunga Homestay
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
require_once '../../backend/api/utils/image-handler.php';

// Get current user
$current_user = getCurrentUser();

// Initialize variables
$errors = [];
$form_data = [
    'title' => '',
    'description' => '',
    'price' => '',
    'tag' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data['title'] = trim($_POST['title'] ?? '');
    $form_data['description'] = trim($_POST['description'] ?? '');
    $form_data['price'] = trim($_POST['price'] ?? '');
    $form_data['tag'] = trim($_POST['tag'] ?? '');

    // Validate form data
    if (empty($form_data['title'])) {
        $errors['title'] = 'Title is required.';
    } elseif (strlen($form_data['title']) > 255) {
        $errors['title'] = 'Title must be less than 255 characters.';
    }

    if (empty($form_data['description'])) {
        $errors['description'] = 'Description is required.';
    }

    if (empty($form_data['price'])) {
        $errors['price'] = 'Price is required.';
    } elseif (!is_numeric($form_data['price']) || floatval($form_data['price']) < 0) {
        $errors['price'] = 'Price must be a valid positive number.';
    }

    if (!empty($form_data['tag']) && strlen($form_data['tag']) > 100) {
        $errors['tag'] = 'Tag must be less than 100 characters.';
    }

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadImage($_FILES['image'], 'uploads/shop/', [
                'max_width' => 800,
                'max_height' => 600,
                'quality' => 85
            ]);

            if ($upload_result['success']) {
                $image_path = $upload_result['path'];
            } else {
                $errors['image'] = $upload_result['message'];
            }
        } else {
            $errors['image'] = 'Error uploading image: ' . $_FILES['image']['error'];
        }
    }

    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            $query = "INSERT INTO shop_items (title, description, price, image, tag, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, NOW(), NOW())";

            $result = insertData($query, 'ssdss', [
                $form_data['title'],
                $form_data['description'],
                floatval($form_data['price']),
                $image_path,
                $form_data['tag']
            ]);

            if ($result) {
                // Log activity
                logActivity('create_shop_item', "Created shop item: {$form_data['title']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Shop item created successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to create shop item. Please try again.';
            }
        } catch (Exception $e) {
            $errors['general'] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Shop Item - Virunga Homestay Admin</title>

    <!-- CSS Files -->
      <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
 

    <style>
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 4px;
        }
        .image-preview.has-image {
            border-color: #28a745;
            border-style: solid;
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
                    <a href="../services/index.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="nav-text">Services</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="nav-text">Shop</span>
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
                    <h1 class="page-title">Add New Shop Item</h1>
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
                <div class="content-wrapper">
                    <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Add New Shop Item</h1>
                        <p class="text-muted">Create a new product for your shop</p>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Shop Items
                    </a>
                </div>

                <!-- Form Card -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Shop Item Details</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors['general'])): ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <?= htmlspecialchars($errors['general']) ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" enctype="multipart/form-data" novalidate>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                                                   id="title" name="title" value="<?= htmlspecialchars($form_data['title']) ?>"
                                                   placeholder="Enter item title" required>
                                            <?php if (isset($errors['title'])): ?>
                                                <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="price" class="form-label">Price (USD) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" 
                                                       class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                                                       id="price" name="price" value="<?= htmlspecialchars($form_data['price']) ?>"
                                                       placeholder="0.00" required>
                                                <?php if (isset($errors['price'])): ?>
                                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="tag" class="form-label">Tag/Category</label>
                                            <input type="text" class="form-control <?= isset($errors['tag']) ? 'is-invalid' : '' ?>"
                                                   id="tag" name="tag" value="<?= htmlspecialchars($form_data['tag']) ?>"
                                                   placeholder="e.g., crafts, clothing">
                                            <?php if (isset($errors['tag'])): ?>
                                                <div class="invalid-feedback"><?= htmlspecialchars($errors['tag']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                                  id="description" name="description" rows="4"
                                                  placeholder="Enter item description" required><?= htmlspecialchars($form_data['description']) ?></textarea>
                                        <?php if (isset($errors['description'])): ?>
                                            <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">Item Image</label>
                                        <input type="file" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                                               id="image" name="image" accept="image/*">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Supported formats: JPG, PNG, GIF. Max size: 5MB. Recommended: 800x600px
                                        </div>
                                        <?php if (isset($errors['image'])): ?>
                                            <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                                        <?php endif; ?>
                                        <div id="imagePreview" class="image-preview" style="display: none;">
                                            <img id="previewImg" src="" alt="Preview">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Create Shop Item
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    preview.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                preview.classList.remove('has-image');
            }
        });
    </script>
</body>
</html>

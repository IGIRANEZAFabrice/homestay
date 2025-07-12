<?php
/**
 * Cars Management - Edit Car
 * Professional admin interface for editing cars
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

// Get car ID
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$car_id) {
    redirectWithMessage('index.php', 'Invalid car ID.', 'danger');
}

// Get car data
$car = getSingleRow("SELECT * FROM cars WHERE id = ?", 'i', [$car_id]);

if (!$car) {
    redirectWithMessage('index.php', 'Car not found.', 'danger');
}

// Initialize variables
$errors = [];
$form_data = [
    'name' => $car['name'],
    'make' => $car['make'] ?? '',
    'model' => $car['model'] ?? '',
    'type' => $car['type'],
    'transmission' => $car['transmission'],
    'fuel_type' => $car['fuel_type'],
    'price' => $car['price'],
    'features' => json_decode($car['features'], true) ?? [],
    'badge' => $car['badge'] ?? '',
    'status' => $car['status'] ?? 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'name' => trim($_POST['name'] ?? ''),
        'make' => trim($_POST['make'] ?? ''),
        'model' => trim($_POST['model'] ?? ''),
        'type' => trim($_POST['type'] ?? ''),
        'transmission' => trim($_POST['transmission'] ?? ''),
        'fuel_type' => trim($_POST['fuel_type'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'features' => array_filter(array_map('trim', $_POST['features'] ?? [])),
        'badge' => trim($_POST['badge'] ?? ''),
        'status' => trim($_POST['status'] ?? 'active')
    ];
    
    // Validation rules
    $validation_rules = [
        'name' => ['required' => true, 'length' => [2, 255]],
        'description' => ['required' => true, 'length' => [10, 2000]],
        'price_per_day' => ['required' => true, 'number' => [0, 9999.99]]
    ];
    
    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);
    
    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }
    
    // Validate features
    if (!empty($form_data['features'])) {
        foreach ($form_data['features'] as $index => $feature) {
            if (strlen($feature) < 2 || strlen($feature) > 100) {
                $errors['features'] = 'Each feature must be between 2 and 100 characters.';
                break;
            }
        }
    }
    
    // Handle image upload
    $image_path = $car['image']; // Keep existing image by default
    $delete_existing_image = isset($_POST['delete_existing_image']);
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/cars/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85
        ]);
        
        if ($upload_result['success']) {
            // Delete old image if exists
            if (!empty($car['image'])) {
                // Construct full path for deletion - handle both old and new formats
                $old_image_path = (strpos($car['image'], 'uploads/') === 0)
                    ? $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $car['image']
                    : $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/cars/' . $car['image'];
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
        if (!empty($car['image'])) {
            // Construct full path for deletion - handle both old and new formats
            $old_image_path = (strpos($car['image'], 'uploads/') === 0)
                ? $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $car['image']
                : $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/cars/' . $car['image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_path = '';
    }
    
    // If no validation errors, update database
    if (empty($errors)) {
        try {
            $features_json = json_encode($form_data['features']);
            
            $query = "UPDATE cars
                      SET name = ?, make = ?, model = ?, type = ?, transmission = ?, fuel_type = ?, price = ?, image = ?, features = ?, badge = ?, status = ?
                      WHERE id = ?";

            $result = updateData($query, 'ssssssdsssi', [
                $form_data['name'],
                $form_data['make'],
                $form_data['model'],
                $form_data['type'],
                $form_data['transmission'],
                $form_data['fuel_type'],
                $form_data['price'],
                $image_path,
                $features_json,
                $form_data['badge'],
                $form_data['status'],
                $car_id
            ]);
            
            if ($result !== false) {
                // Log activity
                logActivity('update_car', "Updated car: {$form_data['name']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Car updated successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to update car. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error updating car: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while updating the car.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Cars', 'url' => 'index.php'],
    ['title' => 'Edit Car', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Car - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .features-container {
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            padding: 20px;
            background-color: var(--gray-50);
        }
        
        .feature-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .feature-input {
            flex: 1;
        }
        
        .remove-feature {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: var(--border-radius-sm);
            width: 32px;
            height: 32px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .remove-feature:hover {
            background: #c0392b;
        }
        
        .add-feature {
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 8px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-feature:hover {
            background: #2980b9;
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Edit Car</h1>
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
                        <h2 class="form-title">Edit Car</h2>
                        <p class="form-subtitle">Update car information</p>
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
                                    <label for="name" class="form-label required">Car Name</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['name']) ?>"
                                        required
                                        data-min-length="2"
                                        data-max-length="255"
                                        placeholder="Enter car name"
                                    >
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="price_per_day" class="form-label required">Price per Day ($)</label>
                                    <input 
                                        type="number" 
                                        id="price_per_day" 
                                        name="price_per_day" 
                                        class="form-control <?= isset($errors['price_per_day']) ? 'is-invalid' : '' ?>" 
                                        value="<?= $form_data['price_per_day'] ?>"
                                        required
                                        min="0"
                                        max="9999.99"
                                        step="0.01"
                                        placeholder="0.00"
                                    >
                                    <?php if (isset($errors['price_per_day'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['price_per_day']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label required">Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control large <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                required
                                data-min-length="10"
                                data-max-length="2000"
                                placeholder="Describe the car..."
                            ><?= htmlspecialchars($form_data['description']) ?></textarea>
                            <?php if (isset($errors['description'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Car Features</label>
                            <div class="features-container">
                                <div id="features-list">
                                    <?php if (!empty($form_data['features'])): ?>
                                        <?php foreach ($form_data['features'] as $feature): ?>
                                            <div class="feature-item">
                                                <input type="text" name="features[]" value="<?= htmlspecialchars($feature) ?>" class="form-control feature-input" placeholder="Enter feature">
                                                <button type="button" class="remove-feature" onclick="removeFeature(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="feature-item">
                                            <input type="text" name="features[]" class="form-control feature-input" placeholder="Enter feature">
                                            <button type="button" class="remove-feature" onclick="removeFeature(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="add-feature" onclick="addFeature()">
                                    <i class="fas fa-plus"></i> Add Feature
                                </button>
                                <?php if (isset($errors['features'])): ?>
                                    <div class="invalid-feedback" style="display: block; margin-top: 10px;">
                                        <?= htmlspecialchars($errors['features']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">Car Image</label>
                            
                            <?php if (!empty($car['image'])): ?>
                                <div class="existing-image-preview" style="margin-bottom: 15px;">
                                    <img src="<?= buildAdminImageUrl($car['image'], 'cars') ?>"
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

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Car
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
        // Features management
        function addFeature() {
            const featuresList = document.getElementById('features-list');
            const featureItem = document.createElement('div');
            featureItem.className = 'feature-item';
            featureItem.innerHTML = `
                <input type="text" name="features[]" class="form-control feature-input" placeholder="Enter feature">
                <button type="button" class="remove-feature" onclick="removeFeature(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            featuresList.appendChild(featureItem);
            
            // Focus on the new input
            featureItem.querySelector('input').focus();
        }
        
        function removeFeature(button) {
            const featuresList = document.getElementById('features-list');
            const featureItems = featuresList.querySelectorAll('.feature-item');
            
            // Don't remove if it's the last item
            if (featureItems.length > 1) {
                button.parentElement.remove();
            } else {
                // Clear the input instead
                button.parentElement.querySelector('input').value = '';
            }
        }
        
        // Remove empty features before form submission
        document.querySelector('form').addEventListener('submit', function() {
            const featureInputs = document.querySelectorAll('input[name="features[]"]');
            featureInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.remove();
                }
            });
        });
    </script>
</body>
</html>

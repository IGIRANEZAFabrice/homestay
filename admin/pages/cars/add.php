<?php
/**
 * Cars Management - Add New Car
 * Professional admin interface for adding cars
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

// Initialize variables
$errors = [];
$form_data = [
    'name' => '',
    'make' => '',
    'model' => '',
    'type' => '',
    'transmission' => '',
    'fuel_type' => '',
    'price' => '',
    'features' => [],
    'badge' => '',
    'status' => 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'name' => trim($_POST['name'] ?? ''),
        'type' => trim($_POST['type'] ?? ''),
        'transmission' => trim($_POST['transmission'] ?? ''),
        'fuel_type' => trim($_POST['fuel_type'] ?? ''),
        'price' => floatval($_POST['price'] ?? 0),
        'features' => array_filter(array_map('trim', $_POST['features'] ?? []))
    ];
    
    // Validation rules
    $validation_rules = [
        'name' => ['required' => true, 'length' => [2, 255]],
        'type' => ['required' => true, 'length' => [2, 50]],
        'transmission' => ['required' => true, 'length' => [2, 20]],
        'fuel_type' => ['required' => true, 'length' => [2, 20]],
        'price' => ['required' => true, 'number' => [0, 9999.99]]
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
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/cars/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85
        ]);
        
        if ($upload_result['success']) {
            $image_path = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    }
    
    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            $features_json = json_encode($form_data['features']);

            // Debug: Log the data being inserted
            error_log('Car data: ' . print_r([
                'name' => $form_data['name'],
                'type' => $form_data['type'],
                'transmission' => $form_data['transmission'],
                'fuel_type' => $form_data['fuel_type'],
                'price' => $form_data['price'],
                'image_path' => $image_path,
                'features' => $features_json
            ], true));

            // Use basic required fields only to ensure compatibility
            $query = "INSERT INTO cars (name, type, transmission, fuel_type, price, image, features, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $result = insertData($query, 'ssssdss', [
                $form_data['name'],
                $form_data['type'],
                $form_data['transmission'],
                $form_data['fuel_type'],
                $form_data['price'],
                $image_path,
                $features_json
            ]);
            
            if ($result) {
                // Log activity
                logActivity('create_car', "Created car: {$form_data['name']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Car created successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to create car. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error creating car: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while creating the car: ' . $e->getMessage();
        }
    }
} else {
    // Initialize empty form data for GET request
    $form_data = [
        'name' => '',
        'type' => '',
        'transmission' => '',
        'fuel_type' => '',
        'price' => '',
        'features' => []
    ];
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Cars', 'url' => 'index.php'],
    ['title' => 'Add Car', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Car - Virunga Homestay Admin</title>
    
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
                    <h1 class="page-title">Add Car</h1>
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
                        <h2 class="form-title">Add New Car</h2>
                        <p class="form-subtitle">Add a new car to your fleet</p>
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
                                    <label for="price" class="form-label required">Price per Day ($)</label>
                                    <input
                                        type="number"
                                        id="price"
                                        name="price"
                                        class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                                        value="<?= $form_data['price'] ?>"
                                        required
                                        min="0"
                                        max="9999.99"
                                        step="0.01"
                                        placeholder="0.00"
                                    >
                                    <?php if (isset($errors['price'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="type" class="form-label required">Car Type</label>
                                    <select
                                        id="type"
                                        name="type"
                                        class="form-control <?= isset($errors['type']) ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="">Select car type</option>
                                        <option value="SUV" <?= $form_data['type'] === 'SUV' ? 'selected' : '' ?>>SUV</option>
                                        <option value="Sedan" <?= $form_data['type'] === 'Sedan' ? 'selected' : '' ?>>Sedan</option>
                                        <option value="Hatchback" <?= $form_data['type'] === 'Hatchback' ? 'selected' : '' ?>>Hatchback</option>
                                        <option value="Coupe" <?= $form_data['type'] === 'Coupe' ? 'selected' : '' ?>>Coupe</option>
                                        <option value="Convertible" <?= $form_data['type'] === 'Convertible' ? 'selected' : '' ?>>Convertible</option>
                                        <option value="Pickup" <?= $form_data['type'] === 'Pickup' ? 'selected' : '' ?>>Pickup</option>
                                        <option value="Van" <?= $form_data['type'] === 'Van' ? 'selected' : '' ?>>Van</option>
                                    </select>
                                    <?php if (isset($errors['type'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['type']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="transmission" class="form-label required">Transmission</label>
                                    <select
                                        id="transmission"
                                        name="transmission"
                                        class="form-control <?= isset($errors['transmission']) ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="">Select transmission</option>
                                        <option value="Manual" <?= $form_data['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
                                        <option value="Automatic" <?= $form_data['transmission'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                                        <option value="CVT" <?= $form_data['transmission'] === 'CVT' ? 'selected' : '' ?>>CVT</option>
                                    </select>
                                    <?php if (isset($errors['transmission'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['transmission']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="fuel_type" class="form-label required">Fuel Type</label>
                                    <select
                                        id="fuel_type"
                                        name="fuel_type"
                                        class="form-control <?= isset($errors['fuel_type']) ? 'is-invalid' : '' ?>"
                                        required
                                    >
                                        <option value="">Select fuel type</option>
                                        <option value="Petrol" <?= $form_data['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                                        <option value="Diesel" <?= $form_data['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                        <option value="Hybrid" <?= $form_data['fuel_type'] === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                                        <option value="Electric" <?= $form_data['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
                                    </select>
                                    <?php if (isset($errors['fuel_type'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['fuel_type']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
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
                                            <strong>Click to upload</strong> or drag and drop<br>
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
                                <i class="fas fa-save"></i> Save Car
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

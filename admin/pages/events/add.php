<?php
/**
 * Events Management - Add New Event
 * Professional admin interface for adding events
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
    'title' => '',
    'description' => '',
    'event_date' => '',
    'location' => '',
    'is_active' => 1
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'event_date' => trim($_POST['event_date'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Handle image upload (required for events)
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/events/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85
        ]);

        if ($upload_result['success']) {
            $image_path = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    } else {
        $errors['image'] = 'Event image is required.';
    }

    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'description' => ['required' => true, 'length' => [10, 2000]],
        'event_date' => ['required' => true],
        'location' => ['required' => true, 'length' => [3, 255]]
    ];

    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);

    if (!$validation_result['valid']) {
        $errors = array_merge($errors, $validation_result['errors']);
    }
    
    // Validate event date format and future date
    if (!empty($form_data['event_date'])) {
        $event_datetime = DateTime::createFromFormat('Y-m-d\TH:i', $form_data['event_date']);
        if (!$event_datetime) {
            $errors['event_date'] = 'Invalid date and time format.';
        } else {
            // Convert to MySQL datetime format
            $form_data['event_date'] = $event_datetime->format('Y-m-d H:i:s');
            
            // Check if event is in the past (optional warning)
            $now = new DateTime();
            if ($event_datetime < $now) {
                // You might want to show a warning but still allow past events
                // $errors['event_date'] = 'Event date cannot be in the past.';
            }
        }
    }
    
    // If no validation errors, save to database
    if (empty($errors)) {
        try {
            $query = "INSERT INTO events (title, image, description, event_date, location, is_active, created_at, updated_at)
                      VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $result = insertData($query, 'sssssi', [
                $form_data['title'],
                $image_path,
                $form_data['description'],
                $form_data['event_date'],
                $form_data['location'],
                $form_data['is_active']
            ]);
            
            if ($result) {
                // Log activity
                logActivity('create_event', "Created event: {$form_data['title']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Event created successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to create event. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error creating event: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while creating the event.';
        }
    }
}

// Set default event date to tomorrow at 10:00 AM
if (empty($form_data['event_date'])) {
    $tomorrow = new DateTime('tomorrow');
    $tomorrow->setTime(10, 0);
    $form_data['event_date'] = $tomorrow->format('Y-m-d\TH:i');
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Events', 'url' => 'index.php'],
    ['title' => 'Add Event', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Virunga Homestay Admin</title>
    
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Add Event</h1>
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
                        <h2 class="form-title">Add New Event</h2>
                        <p class="form-subtitle">Create a new event for your homestay</p>
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
                                    <label for="title" class="form-label required">Event Title</label>
                                    <input 
                                        type="text" 
                                        id="title" 
                                        name="title" 
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['title']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="Enter event title"
                                    >
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="event_date" class="form-label required">Event Date & Time</label>
                                    <input 
                                        type="datetime-local" 
                                        id="event_date" 
                                        name="event_date" 
                                        class="form-control <?= isset($errors['event_date']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['event_date']) ?>"
                                        required
                                    >
                                    <?php if (isset($errors['event_date'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['event_date']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label required">Event Location</label>
                            <input
                                type="text"
                                id="location"
                                name="location"
                                class="form-control <?= isset($errors['location']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($form_data['location']) ?>"
                                required
                                data-min-length="3"
                                data-max-length="255"
                                placeholder="Enter event location"
                            >
                            <?php if (isset($errors['location'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['location']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label required">Event Image</label>
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

                        <div class="form-group">
                            <label for="description" class="form-label required">Event Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control large <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                                required
                                data-min-length="10"
                                data-max-length="2000"
                                placeholder="Describe the event in detail..."
                            ><?= htmlspecialchars($form_data['description']) ?></textarea>
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
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Event
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
        // Set minimum date to today
        document.addEventListener('DOMContentLoaded', function() {
            const eventDateInput = document.getElementById('event_date');
            const now = new Date();
            
            // Set minimum date to current date and time
            const minDateTime = now.toISOString().slice(0, 16);
            eventDateInput.setAttribute('min', minDateTime);
            
            // Show warning for past dates
            eventDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const currentDate = new Date();
                
                if (selectedDate < currentDate) {
                    if (confirm('The selected date is in the past. Are you sure you want to create a past event?')) {
                        // User confirmed, keep the date
                    } else {
                        // Reset to minimum date
                        this.value = minDateTime;
                    }
                }
            });
        });
    </script>
</body>
</html>

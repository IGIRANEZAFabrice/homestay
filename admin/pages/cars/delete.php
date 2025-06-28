<?php
/**
 * Cars Management - Delete Car
 * Professional admin interface for deleting cars
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

// Get current user
$current_user = getCurrentUser();

// Get car ID
$car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$car_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid car ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid car ID.', 'danger');
    }
}

// Get car data
$car = getSingleRow("SELECT * FROM cars WHERE id = ?", 'i', [$car_id]);

if (!$car) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Car not found.');
    } else {
        redirectWithMessage('index.php', 'Car not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the car from database
        $result = deleteData("DELETE FROM cars WHERE id = ?", 'i', [$car_id]);
        
        if ($result > 0) {
            // Delete associated image file if exists
            if (!empty($car['image'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $car['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Log activity
            logActivity('delete_car', "Deleted car: {$car['name']}");
            
            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Car deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Car deleted successfully!', 'success');
            }
        } else {
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete car.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete car.', 'danger');
            }
        }
        
    } catch (Exception $e) {
        error_log('Error deleting car: ' . $e->getMessage());
        
        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the car.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the car.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Parse features
    $features = json_decode($car['features'], true) ?? [];
    
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Cars', 'url' => 'index.php'],
        ['title' => 'Delete Car', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Car - Virunga Homestay Admin</title>
    
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
                    <h1 class="page-title">Delete Car</h1>
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

                <!-- Confirmation Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Confirm Deletion
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle alert-icon"></i>
                            <strong>Warning:</strong> This action cannot be undone. The car and its associated image will be permanently deleted.
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <?php if (!empty($car['image'])): ?>
                                    <img src="/homestay/<?= htmlspecialchars($car['image']) ?>" 
                                         alt="<?= htmlspecialchars($car['name']) ?>" 
                                         class="img-fluid rounded shadow-sm">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-car fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <h4><?= htmlspecialchars($car['name']) ?></h4>
                                <p class="text-muted">
                                    <?= htmlspecialchars(truncateText($car['description'], 200)) ?>
                                </p>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Price per Day:</strong> $<?= number_format($car['price_per_day'], 2) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Features:</strong> <?= count($features) ?> features
                                    </div>
                                </div>
                                <?php if (!empty($features)): ?>
                                    <div class="mt-2">
                                        <strong>Features:</strong>
                                        <ul class="list-inline mt-1">
                                            <?php foreach ($features as $feature): ?>
                                                <li class="list-inline-item">
                                                    <span class="badge badge-secondary"><?= htmlspecialchars($feature) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <strong>Created:</strong> <?= formatDateTime($car['created_at']) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Updated:</strong> <?= formatDateTime($car['updated_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this car?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($car['name']) ?></em>" from your fleet.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Car
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
</body>
</html>
<?php
} else {
    // AJAX request without POST - not allowed
    sendJSONResponse(false, 'Method not allowed.');
}
?>

<?php
/**
 * Services Management - Delete Service
 * Professional admin interface for deleting services
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

// Get service ID
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$service_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid service ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid service ID.', 'danger');
    }
}

// Get service data
$service = getSingleRow("SELECT * FROM services WHERE id = ?", 'i', [$service_id]);

if (!$service) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Service not found.');
    } else {
        redirectWithMessage('index.php', 'Service not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the service from database
        $result = deleteData("DELETE FROM services WHERE id = ?", 'i', [$service_id]);
        
        if ($result > 0) {
            // Log activity
            logActivity('delete_service', "Deleted service: {$service['name']}");
            
            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Service deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Service deleted successfully!', 'success');
            }
        } else {
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete service.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete service.', 'danger');
            }
        }
        
    } catch (Exception $e) {
        error_log('Error deleting service: ' . $e->getMessage());
        
        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the service.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the service.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Services', 'url' => 'index.php'],
        ['title' => 'Delete Service', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Service - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .duration-badge {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            margin-left: 10px;
        }
        
        .service-preview {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 0 6px 6px 0;
            margin: 20px 0;
        }
        
        .price-display {
            font-size: 24px;
            font-weight: 700;
            color: var(--success-color);
            margin: 10px 0;
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
                    <h1 class="page-title">Delete Service</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The service will be permanently deleted from your website.
                        </div>

                        <div class="service-details">
                            <h4><?= htmlspecialchars($service['name']) ?></h4>
                            
                            <?php if (!empty($service['category'])): ?>
                                <span class="category-badge"><?= htmlspecialchars($service['category']) ?></span>
                            <?php endif; ?>
                            
                            <?php if (!empty($service['duration'])): ?>
                                <span class="duration-badge"><?= htmlspecialchars($service['duration']) ?></span>
                            <?php endif; ?>
                            
                            <div class="price-display">
                                $<?= number_format($service['price'], 2) ?>
                            </div>
                            
                            <div class="service-preview">
                                <p style="margin: 0; line-height: 1.6;">
                                    <?= htmlspecialchars($service['description']) ?>
                                </p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status:</strong> 
                                    <?= getStatusBadge($service['is_active']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Availability:</strong> 
                                    <?= $service['is_available'] ? '<span class="status-badge approved">Available</span>' : '<span class="status-badge pending">Unavailable</span>' ?>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Created:</strong> <?= formatDateTime($service['created_at']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Updated:</strong> <?= formatDateTime($service['updated_at']) ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this service?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($service['name']) ?></em>" from your service offerings.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Service
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

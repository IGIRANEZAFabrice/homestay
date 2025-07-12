<?php
/**
 * Activities Management - Delete Activity
 * Professional admin interface for deleting activities
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

// Get activity ID
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$activity_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid activity ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid activity ID.', 'danger');
    }
}

// Get activity data
$activity = getSingleRow("SELECT * FROM activities WHERE id = ?", 'i', [$activity_id]);

if (!$activity) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Activity not found.');
    } else {
        redirectWithMessage('index.php', 'Activity not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the activity from database
        $result = deleteData("DELETE FROM activities WHERE id = ?", 'i', [$activity_id]);
        
        if ($result > 0) {
            // Delete associated image file if exists
            if (!empty($activity['image'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $activity['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Log activity
            logActivity('delete_activity', "Deleted activity: {$activity['title']}");
            
            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Activity deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Activity deleted successfully!', 'success');
            }
        } else {
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete activity.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete activity.', 'danger');
            }
        }
        
    } catch (Exception $e) {
        error_log('Error deleting activity: ' . $e->getMessage());
        
        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the activity.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the activity.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Activities', 'url' => 'index.php'],
        ['title' => 'Delete Activity', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Activity - Virunga Homestay Admin</title>
    
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Delete Activity</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The activity and its associated image will be permanently deleted.
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <?php if (!empty($activity['image'])): ?>
                                    <img src="<?= buildAdminImageUrl($activity['image'], 'activities') ?>"
                                         alt="<?= htmlspecialchars($activity['title']) ?>"
                                         class="img-fluid rounded shadow-sm">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-9">
                                <h4><?= htmlspecialchars($activity['title']) ?></h4>
                                <p class="text-muted">
                                    <?= htmlspecialchars(truncateText($activity['content'], 200)) ?>
                                </p>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Display Order:</strong> <?= $activity['display_order'] ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Status:</strong> 
                                        <?= getStatusBadge($activity['is_active']) ?>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <strong>Created:</strong> <?= formatDateTime($activity['created_at']) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Updated:</strong> <?= formatDateTime($activity['updated_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this activity?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($activity['title']) ?></em>" from your website.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Activity
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

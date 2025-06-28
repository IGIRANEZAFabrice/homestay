<?php
/**
 * Hero Images Management - Delete Hero Image
 * Professional admin interface for deleting hero images
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

// Get hero image ID
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$image_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid hero image ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid hero image ID.', 'danger');
    }
}

// Get hero image data
$hero_image = getSingleRow("SELECT * FROM hero_images WHERE id = ?", 'i', [$image_id]);

if (!$hero_image) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Hero image not found.');
    } else {
        redirectWithMessage('index.php', 'Hero image not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn = getConnection();
        $conn->begin_transaction();
        
        // Delete the hero image from database
        $result = deleteData("DELETE FROM hero_images WHERE id = ?", 'i', [$image_id]);
        
        if ($result > 0) {
            // Delete associated image file if exists
            if (!empty($hero_image['image'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $hero_image['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Commit the deletion first
            $conn->commit();

            // Reorder remaining hero images to fill the gap (non-critical operation)
            try {
                $remaining_images = getMultipleRows("SELECT id FROM hero_images ORDER BY display_order ASC");
                if ($remaining_images && is_array($remaining_images)) {
                    $update_query = "UPDATE hero_images SET display_order = ?, updated_at = NOW() WHERE id = ?";
                    $stmt = $conn->prepare($update_query);

                    if ($stmt) {
                        foreach ($remaining_images as $index => $image) {
                            $new_order = $index + 1;
                            $stmt->bind_param('ii', $new_order, $image['id']);
                            $stmt->execute();
                        }
                        $stmt->close();
                    }
                }
            } catch (Exception $reorder_error) {
                error_log('Hero image reordering failed: ' . $reorder_error->getMessage());
                // Don't let reordering failure break the delete operation
            }

            // Log activity (non-critical operation)
            try {
                if (function_exists('logActivity')) {
                    logActivity('delete_hero_image', "Deleted hero image: {$hero_image['title']}");
                }
            } catch (Exception $log_error) {
                error_log('Activity logging failed: ' . $log_error->getMessage());
                // Don't let logging failure break the delete operation
            }

            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Hero image deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Hero image deleted successfully!', 'success');
            }
        } else {
            $conn->rollback();
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete hero image.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete hero image.', 'danger');
            }
        }

    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        error_log('Error deleting hero image: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());

        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the hero image.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the hero image.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Hero Images', 'url' => 'index.php'],
        ['title' => 'Delete Hero Image', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Hero Image - Virunga Homestay Admin</title>
    
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
                    <a href="../events/index.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Events</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Delete Hero Image</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The hero image and its associated file will be permanently deleted. The display order of remaining images will be automatically adjusted.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($hero_image['image'])): ?>
                                    <img src="/homestay/<?= htmlspecialchars($hero_image['image']) ?>"
                                         alt="<?= htmlspecialchars($hero_image['title']) ?>"
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 300px; width: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h4><?= htmlspecialchars($hero_image['title']) ?></h4>
                                
                                <?php if (!empty($hero_image['description'])): ?>
                                    <p class="text-muted">
                                        <?= htmlspecialchars($hero_image['description']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Status:</strong> 
                                        <?= getStatusBadge($hero_image['is_active']) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Display Order:</strong> <?= $hero_image['display_order'] ?>
                                    </div>
                                </div>
                                
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <strong>Created:</strong> <?= formatDateTime($hero_image['created_at']) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Updated:</strong> <?= formatDateTime($hero_image['updated_at']) ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($hero_image['image'])): ?>
                                    <div class="mt-2">
                                        <strong>Image File:</strong><br>
                                        <code class="text-muted"><?= htmlspecialchars($hero_image['image']) ?></code>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this hero image?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($hero_image['title']) ?></em>" from your website's hero section.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Hero Image
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

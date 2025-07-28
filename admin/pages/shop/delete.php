<?php
/**
 * Shop Items Management - Delete Item
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

// Get current user
$current_user = getCurrentUser();

// Get item ID from URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id <= 0) {
    redirectWithMessage('index.php', 'Invalid item ID.', 'error');
}

// Get existing item data
$item = getSingleRow("SELECT * FROM shop_items WHERE id = ?", 'i', [$item_id]);

if (!$item) {
    redirectWithMessage('index.php', 'Shop item not found.', 'error');
}

// Handle form submission (confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
        try {
            // Delete from database
            $result = deleteData("DELETE FROM shop_items WHERE id = ?", 'i', [$item_id]);
            
            if ($result) {
                // Delete associated image file
                if (!empty($item['image'])) {
                    $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $item['image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                // Log activity
                logActivity('delete_shop_item', "Deleted shop item: {$item['title']}");
                
                // Redirect with success message
                redirectWithMessage('index.php', 'Shop item deleted successfully!', 'success');
            } else {
                redirectWithMessage('index.php', 'Failed to delete shop item. Please try again.', 'error');
            }
        } catch (Exception $e) {
            redirectWithMessage('index.php', 'Database error: ' . $e->getMessage(), 'error');
        }
    } else {
        // User cancelled deletion
        redirectWithMessage('index.php', 'Deletion cancelled.', 'info');
    }
}

// Helper function to get image display path
function getImageDisplayPath($image_path) {
    if (empty($image_path)) {
        return '';
    }
    
    // If image path starts with 'uploads/', use it directly
    if (strpos($image_path, 'uploads/') === 0) {
        return '/homestay/' . $image_path;
    } else {
        // Otherwise assume it's in uploads/shop/
        return '/homestay/uploads/shop/' . $image_path;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Shop Item - Virunga Homestay Admin</title>

    <!-- CSS Files -->
      <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/admin-dashboard.css" rel="stylesheet">

    <style>
        .item-preview {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
        }
        .item-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        .delete-warning {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
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
                    <h1 class="page-title">Delete Shop Item</h1>
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
                        <h1 class="h3 mb-0">Delete Shop Item</h1>
                        <p class="text-muted">Confirm deletion of shop item</p>
                    </div>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Shop Items
                    </a>
                </div>

                <!-- Delete Confirmation -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Warning Alert -->
                        <div class="delete-warning">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0">Warning: Permanent Deletion</h4>
                                    <p class="mb-0">This action cannot be undone. The item and its image will be permanently deleted.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Item Preview Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Item to be Deleted</h5>
                            </div>
                            <div class="card-body">
                                <div class="item-preview">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= htmlspecialchars(getImageDisplayPath($item['image'])) ?>" 
                                                     alt="Item image" class="item-image">
                                            <?php else: ?>
                                                <div class="item-image d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-9">
                                            <h4><?= htmlspecialchars($item['title']) ?></h4>
                                            <p class="text-muted mb-2"><?= htmlspecialchars($item['description']) ?></p>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <strong>Price:</strong> $<?= number_format($item['price'], 2) ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Tag:</strong> 
                                                    <?= !empty($item['tag']) ? htmlspecialchars($item['tag']) : '<span class="text-muted">None</span>' ?>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-sm-6">
                                                    <strong>Created:</strong> <?= date('M j, Y', strtotime($item['created_at'])) ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong>Updated:</strong> <?= date('M j, Y', strtotime($item['updated_at'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation Form -->
                                <form method="POST" class="mt-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-0 text-muted">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Are you sure you want to delete this shop item?
                                            </p>
                                        </div>
                                        <div class="btn-group">
                                            <a href="index.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Cancel
                                            </a>
                                            <button type="submit" name="confirm_delete" value="yes" 
                                                    class="btn btn-danger"
                                                    onclick="return confirm('Are you absolutely sure? This action cannot be undone!')">
                                                <i class="fas fa-trash me-2"></i>Yes, Delete Item
                                            </button>
                                        </div>
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
</body>
</html>

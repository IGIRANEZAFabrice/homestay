<?php
/**
 * Rooms Management - Delete Room
 * Professional admin interface for deleting rooms
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

// Get room ID
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$room_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid room ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid room ID.', 'danger');
    }
}

// Get room data
$room = getSingleRow("SELECT * FROM rooms WHERE id = ?", 'i', [$room_id]);

if (!$room) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Room not found.');
    } else {
        redirectWithMessage('index.php', 'Room not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the room from database
        $result = deleteData("DELETE FROM rooms WHERE id = ?", 'i', [$room_id]);
        
        if ($result > 0) {
            // Delete associated image file if it exists
            if (!empty($room['image'])) {
                // Handle both filename-only and full path cases
                if (strpos($room['image'], 'uploads/') === 0) {
                    // Full path stored in database
                    $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $room['image'];
                } else {
                    // Only filename stored in database
                    $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/rooms/' . $room['image'];
                }
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Log activity (non-critical operation)
            try {
                if (function_exists('logActivity')) {
                    logActivity('delete_room', "Deleted room: {$room['title']}");
                }
            } catch (Exception $log_error) {
                error_log('Activity logging failed: ' . $log_error->getMessage());
                // Don't let logging failure break the delete operation
            }

            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Room deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Room deleted successfully!', 'success');
            }
        } else {
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete room.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete room.', 'danger');
            }
        }
        
    } catch (Exception $e) {
        error_log('Error deleting room: ' . $e->getMessage());
        
        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the room.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the room.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Rooms', 'url' => 'index.php'],
        ['title' => 'Delete Room', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Room - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .room-images-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        
        .room-image-preview {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--gray-300);
        }
        
        .amenities-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 10px 0;
        }
        
        .amenity-tag {
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .room-type-badge {
            background: var(--secondary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
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
                    <h1 class="page-title">Delete Room</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The room and all its associated images will be permanently deleted.
                        </div>

                        <div class="room-details">
                            <h4><?= htmlspecialchars($room['name']) ?></h4>
                            
                            <?php if (!empty($room['room_type'])): ?>
                                <span class="room-type-badge"><?= htmlspecialchars($room['room_type']) ?></span>
                            <?php endif; ?>
                            
                            <p><?= htmlspecialchars($room['description']) ?></p>
                            
                            <?php if (!empty($room['image'])): ?>
                                <div>
                                    <strong>Room Image:</strong>
                                    <div class="room-images-preview">
                                        <?php
                                            // Handle both filename-only and full path cases
                                            $image_src = (strpos($room['image'], 'uploads/') === 0)
                                                ? '/homestay/' . $room['image']
                                                : '/homestay/uploads/rooms/' . $room['image'];
                                        ?>
                                        <img src="<?= htmlspecialchars($image_src) ?>"
                                             alt="Room image"
                                             class="room-image-preview">
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Price per Night:</strong> $<?= number_format($room['price_per_night'], 2) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Max Occupancy:</strong> <?= $room['max_occupancy'] ?> guests
                                </div>
                            </div>
                            
                            <?php if (!empty($amenities)): ?>
                                <div class="mt-3">
                                    <strong>Amenities (<?= count($amenities) ?> amenities):</strong>
                                    <div class="amenities-preview">
                                        <?php foreach ($amenities as $amenity): ?>
                                            <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong> 
                                    <?= getStatusBadge($room['is_active']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Availability:</strong> 
                                    <?= $room['is_available'] ? '<span class="status-badge approved">Available</span>' : '<span class="status-badge pending">Unavailable</span>' ?>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Created:</strong> <?= formatDateTime($room['created_at']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Updated:</strong> <?= formatDateTime($room['updated_at']) ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this room?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($room['name']) ?></em>" from your accommodation listings.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Room
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

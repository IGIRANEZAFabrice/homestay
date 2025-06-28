<?php
/**
 * Events Management - Delete Event
 * Professional admin interface for deleting events
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

// Get event ID
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$event_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid event ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid event ID.', 'danger');
    }
}

// Get event data
$event = getSingleRow("SELECT * FROM events WHERE id = ?", 'i', [$event_id]);

if (!$event) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Event not found.');
    } else {
        redirectWithMessage('index.php', 'Event not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Force JSON response for all POST requests
    header('Content-Type: application/json');

    try {
        // Delete the event from database
        $result = deleteData("DELETE FROM events WHERE id = ?", 'i', [$event_id]);
        
        if ($result > 0) {
            // Delete associated image file if exists
            if (!empty($event['image'])) {
                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $event['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Log activity
            try {
                if (function_exists('logActivity')) {
                    logActivity('delete_event', "Deleted event: {$event['title']}");
                }
            } catch (Exception $log_error) {
                error_log('Activity logging failed: ' . $log_error->getMessage());
            }

            // Always return JSON for POST requests
            echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete event.']);
            exit();
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred while deleting.']);
        exit();
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Check if event is upcoming or past
    $event_date = new DateTime($event['event_date']);
    $now = new DateTime();
    $is_upcoming = $event_date > $now;
    $is_today = $event_date->format('Y-m-d') === $now->format('Y-m-d');
    
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Events', 'url' => 'index.php'],
        ['title' => 'Delete Event', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event - Virunga Homestay Admin</title>
    
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
                    <h1 class="page-title">Delete Event</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The event will be permanently deleted.
                            <?php if ($is_upcoming): ?>
                                <br><strong>Note:</strong> This is an upcoming event that may have attendees expecting it.
                            <?php endif; ?>
                        </div>

                        <div class="event-details">
                            <h4><?= htmlspecialchars($event['title']) ?></h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date & Time:</strong><br>
                                    <?= formatDateTime($event['event_date'], 'l, F j, Y \a\t g:i A') ?>
                                    <?php if ($is_today): ?>
                                        <span class="badge badge-warning ml-2">Today</span>
                                    <?php elseif ($is_upcoming): ?>
                                        <span class="badge badge-info ml-2">Upcoming</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary ml-2">Past</span>
                                    <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Location:</strong><br>
                                    <?= htmlspecialchars($event['location']) ?></p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong><br>
                                    <?= getStatusBadge($event['is_active']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created:</strong><br>
                                    <?= formatDateTime($event['created_at']) ?></p>
                                </div>
                            </div>
                            
                            <p><strong>Description:</strong><br>
                            <?= htmlspecialchars(truncateText($event['description'], 300)) ?></p>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this event?</strong><br>
                            This will permanently remove "<em><?= htmlspecialchars($event['title']) ?></em>" from your events.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;" id="deleteForm">
                                <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">
                                    <i class="fas fa-trash"></i> Yes, Delete Event
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

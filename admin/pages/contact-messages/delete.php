<?php
/**
 * Contact Messages - Delete Message
 * Professional admin interface for deleting contact messages
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

// Get message ID
$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$message_id) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Invalid message ID.');
    } else {
        redirectWithMessage('index.php', 'Invalid message ID.', 'danger');
    }
}

// Get message data
$message = getSingleRow("SELECT * FROM contact_messages WHERE id = ?", 'i', [$message_id]);

if (!$message) {
    if (isAjaxRequest()) {
        sendJSONResponse(false, 'Message not found.');
    } else {
        redirectWithMessage('index.php', 'Message not found.', 'danger');
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Delete the message from database
        $result = deleteData("DELETE FROM contact_messages WHERE id = ?", 'i', [$message_id]);
        
        if ($result > 0) {
            // Log activity
            logActivity('delete_message', "Deleted message from {$message['name']} ({$message['email']})");
            
            if (isAjaxRequest()) {
                sendJSONResponse(true, 'Message deleted successfully.');
            } else {
                redirectWithMessage('index.php', 'Message deleted successfully!', 'success');
            }
        } else {
            if (isAjaxRequest()) {
                sendJSONResponse(false, 'Failed to delete message.');
            } else {
                redirectWithMessage('index.php', 'Failed to delete message.', 'danger');
            }
        }
        
    } catch (Exception $e) {
        error_log('Error deleting message: ' . $e->getMessage());
        
        if (isAjaxRequest()) {
            sendJSONResponse(false, 'An error occurred while deleting the message.');
        } else {
            redirectWithMessage('index.php', 'An error occurred while deleting the message.', 'danger');
        }
    }
}

// If not POST request and not AJAX, show confirmation page
if (!isAjaxRequest()) {
    // Breadcrumb data
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => '../dashboard.php'],
        ['title' => 'Contact Messages', 'url' => 'index.php'],
        ['title' => 'Delete Message', 'url' => '']
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Message - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .message-preview {
            background: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 0 6px 6px 0;
            margin: 20px 0;
        }
        
        .sender-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .sender-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 18px;
        }
        
        .sender-details h4 {
            margin: 0 0 5px 0;
            color: var(--gray-800);
        }
        
        .sender-email {
            color: var(--gray-600);
            font-size: 14px;
        }
        
        .message-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .status-new {
            background: var(--warning-color-light);
            color: var(--warning-color);
        }
        
        .status-read {
            background: var(--info-color-light);
            color: var(--info-color);
        }
        
        .status-replied {
            background: var(--success-color-light);
            color: var(--success-color);
        }
        
        .message-content {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid var(--gray-200);
            margin: 15px 0;
        }
        
        .message-subject {
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .message-body {
            line-height: 1.6;
            color: var(--gray-700);
            white-space: pre-wrap;
            word-wrap: break-word;
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
                    <h1 class="page-title">Delete Message</h1>
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
                            <strong>Warning:</strong> This action cannot be undone. The message will be permanently deleted.
                        </div>

                        <div class="message-preview">
                            <div class="sender-info">
                                <div class="sender-avatar">
                                    <?= strtoupper(substr($message['name'], 0, 1)) ?>
                                </div>
                                <div class="sender-details">
                                    <h4><?= htmlspecialchars($message['name']) ?></h4>
                                    <div class="sender-email">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($message['email']) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <span class="message-status status-<?= $message['status'] ?>">
                                <?= ucfirst($message['status']) ?>
                            </span>
                            
                            <div class="message-content">
                                <div class="message-subject">
                                    <i class="fas fa-comment-alt"></i>
                                    <?= htmlspecialchars($message['subject']) ?>
                                </div>
                                <div class="message-body">
                                    <?= htmlspecialchars($message['message']) ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Received:</strong> <?= formatDateTime($message['created_at']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Last Updated:</strong> <?= formatDateTime($message['updated_at']) ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <p class="mb-4">
                            <strong>Are you sure you want to delete this message?</strong><br>
                            This will permanently remove the message from "<em><?= htmlspecialchars($message['name']) ?></em>" from your system.
                        </p>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <form method="POST" action="" style="display: inline;">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you absolutely sure? This action cannot be undone.')">
                                    <i class="fas fa-trash"></i> Yes, Delete Message
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

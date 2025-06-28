<?php
/**
 * Contact Messages - View Message
 * Professional admin interface for viewing contact messages
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
    redirectWithMessage('index.php', 'Invalid message ID.', 'danger');
}

// Get message data
$message = getSingleRow("SELECT * FROM contact_messages WHERE id = ?", 'i', [$message_id]);

if (!$message) {
    redirectWithMessage('index.php', 'Message not found.', 'danger');
}

// Log activity for viewing the message
logActivity('view_message', "Viewed message from {$message['name']}");

// Get flash message if any
$flash_message = getFlashMessage();

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Contact Messages', 'url' => 'index.php'],
    ['title' => 'View Message', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .message-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .message-header {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }
        
        .sender-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .sender-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 24px;
        }
        
        .sender-details h3 {
            margin: 0 0 5px 0;
            color: var(--gray-800);
        }
        
        .sender-email {
            color: var(--gray-600);
            font-size: 14px;
        }
        
        .message-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
            padding: 20px;
            background: var(--gray-50);
            border-radius: var(--border-radius);
        }
        
        .meta-item {
            text-align: center;
        }
        
        .meta-label {
            font-size: 12px;
            color: var(--gray-600);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .meta-value {
            font-weight: 600;
            color: var(--gray-800);
        }
        
        .message-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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
            border-radius: var(--border-radius);
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }
        
        .message-subject {
            font-size: 20px;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .message-body {
            line-height: 1.8;
            color: var(--gray-700);
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .message-actions {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        

        
        .quick-reply-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }
        
        .quick-reply-btn:hover {
            background: var(--primary-color-dark);
            color: white;
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
                    <h1 class="page-title">View Message</h1>
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
                <?php if ($flash_message): ?>
                    <div class="alert alert-<?= $flash_message['type'] ?> alert-dismissible">
                        <i class="fas fa-info-circle alert-icon"></i>
                        <?= htmlspecialchars($flash_message['message']) ?>
                        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>

                <!-- Breadcrumb -->
                <?= generateBreadcrumb($breadcrumbs) ?>

                <div class="message-container">
                    <!-- Message Header -->
                    <div class="message-header">
                        <div class="sender-info">
                            <div class="sender-avatar">
                                <?= strtoupper(substr($message['name'], 0, 1)) ?>
                            </div>
                            <div class="sender-details">
                                <h3><?= htmlspecialchars($message['name']) ?></h3>
                                <div class="sender-email">
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="message-meta">
                            <div class="meta-item">
                                <div class="meta-label">Status</div>
                                <div class="meta-value">
                                    <span class="message-status status-<?= $message['status'] ?>">
                                        <?= ucfirst($message['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-label">Received</div>
                                <div class="meta-value"><?= formatDateTime($message['created_at']) ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Content -->
                    <div class="message-content">
                        <div class="message-subject">
                            <i class="fas fa-comment-alt"></i>
                            <?= htmlspecialchars($message['subject']) ?>
                        </div>
                        <div class="message-body">
                            <?= htmlspecialchars($message['message']) ?>
                        </div>
                    </div>

                    <!-- Message Actions -->
                    <div class="message-actions">
                        <div class="form-row">
                            <div class="form-col-12" style="text-align: center;">
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= htmlspecialchars($message['subject']) ?>"
                                   class="quick-reply-btn">
                                    <i class="fas fa-reply"></i> Reply via Email
                                </a>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Messages
                            </a>
                            <a href="delete.php?id=<?= $message['id'] ?>" 
                               class="btn btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this message?')">
                                <i class="fas fa-trash"></i> Delete Message
                            </a>
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

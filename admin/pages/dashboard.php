<?php
/**
 * Main Dashboard Page
 * Professional admin dashboard for Virunga Homestay
 */

// Define admin access and start session
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}
session_start();

// Include authentication middleware
require_once '../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Include database connection and helpers
require_once '../backend/database/connection.php';
require_once '../backend/api/utils/helpers.php';

// Get current user
$current_user = getCurrentUser();

// Get flash message if any
$flash_message = getFlashMessage();

// Get dashboard statistics
$stats = [
    'activities' => getSingleRow("SELECT COUNT(*) as count FROM activities")['count'] ?? 0,
    'blogs' => getSingleRow("SELECT COUNT(*) as count FROM blogs")['count'] ?? 0,
    'cars' => getSingleRow("SELECT COUNT(*) as count FROM cars")['count'] ?? 0,
    'events' => getSingleRow("SELECT COUNT(*) as count FROM events")['count'] ?? 0,
    'hero_images' => getSingleRow("SELECT COUNT(*) as count FROM hero_images")['count'] ?? 0,
    'reviews' => getSingleRow("SELECT COUNT(*) as count FROM reviews")['count'] ?? 0,
    'rooms' => getSingleRow("SELECT COUNT(*) as count FROM rooms")['count'] ?? 0,
    'services' => getSingleRow("SELECT COUNT(*) as count FROM services")['count'] ?? 0,
    'about_us_page' => getSingleRow("SELECT COUNT(*) as count FROM about_us_page")['count'] ?? 0,
    'homepage_about' => getSingleRow("SELECT COUNT(*) as count FROM homepage_about")['count'] ?? 0,
    'contact_messages' => getSingleRow("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0
];

// Get recent activities (simplified for now)
$recent_activities = [
    [
        'icon' => 'fas fa-plus-circle',
        'title' => 'New activity added',
        'description' => 'A hands-on eco-organic farming experience',
        'time' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'type' => 'success'
    ],
    [
        'icon' => 'fas fa-edit',
        'title' => 'Blog post updated',
        'description' => 'Gorilla Trekking Experience',
        'time' => date('Y-m-d H:i:s', strtotime('-4 hours')),
        'type' => 'info'
    ],
    [
        'icon' => 'fas fa-star',
        'title' => 'New review received',
        'description' => '5-star rating from IGIRANEZA Fabrice',
        'time' => date('Y-m-d H:i:s', strtotime('-6 hours')),
        'type' => 'warning'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/tables.css">
    <link rel="stylesheet" href="../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="dashboard.php" class="sidebar-logo">
                    <i class="fas fa-mountain"></i>
                    <span class="nav-text">Virunga Admin</span>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="activities/index.php" class="nav-link">
                        <i class="fas fa-hiking"></i>
                        <span class="nav-text">Activities</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="blogs/index.php" class="nav-link">
                        <i class="fas fa-blog"></i>
                        <span class="nav-text">Blogs</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="cars/index.php" class="nav-link">
                        <i class="fas fa-car"></i>
                        <span class="nav-text">Cars</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="events/index.php" class="nav-link">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Events</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="hero-images/index.php" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span class="nav-text">Hero Images</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="reviews/index.php" class="nav-link">
                        <i class="fas fa-star"></i>
                        <span class="nav-text">Reviews</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="rooms/index.php" class="nav-link">
                        <i class="fas fa-bed"></i>
                        <span class="nav-text">Rooms</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="services/index.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="nav-text">Services</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="about-us/index.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        <span class="nav-text">About Us</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="homepage-about/index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Homepage About</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="contact-messages/index.php" class="nav-link">
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
                    <h1 class="page-title">Dashboard</h1>
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
                            <a href="../backend/api/auth/logout.php" class="dropdown-item">
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

                <!-- Welcome Message -->
                <div class="card">
                    <div class="card-body">
                        <h2>Welcome back, <?= htmlspecialchars($current_user['full_name'] ?? $current_user['username']) ?>!</h2>
                        <p class="text-muted">Here's what's happening with your homestay today.</p>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-hiking"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="activities"><?= $stats['activities'] ?></h3>
                            <p>Activities</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon secondary">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="blogs"><?= $stats['blogs'] ?></h3>
                            <p>Blog Posts</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="cars"><?= $stats['cars'] ?></h3>
                            <p>Cars</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="reviews"><?= $stats['reviews'] ?></h3>
                            <p>Reviews</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="rooms"><?= $stats['rooms'] ?></h3>
                            <p>Rooms</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="about_us"><?= $stats['about_us_page'] ?></h3>
                            <p>About Us</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon secondary">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="homepage_about"><?= $stats['homepage_about'] ?></h3>
                            <p>Homepage About</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-content">
                            <h3 data-stat="messages"><?= $stats['contact_messages'] ?></h3>
                            <p>Messages</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions and Recent Activity -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Quick Actions</h3>
                            </div>
                            <div class="card-body">
                                <div class="quick-actions-grid">
                                    <a href="activities/add.php" class="action-btn">
                                        <i class="fas fa-plus"></i>
                                        <span>Add Activity</span>
                                    </a>
                                    <a href="blogs/add.php" class="action-btn">
                                        <i class="fas fa-plus"></i>
                                        <span>Add Blog Post</span>
                                    </a>
                                    <a href="cars/add.php" class="action-btn">
                                        <i class="fas fa-plus"></i>
                                        <span>Add Car</span>
                                    </a>
                                    <a href="events/add.php" class="action-btn">
                                        <i class="fas fa-plus"></i>
                                        <span>Add Event</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Activity</h3>
                            </div>
                            <div class="card-body">
                                <div class="activity-list">
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon">
                                                <i class="<?= $activity['icon'] ?>"></i>
                                            </div>
                                            <div class="activity-info">
                                                <h4><?= htmlspecialchars($activity['title']) ?></h4>
                                                <p><?= htmlspecialchars($activity['description']) ?></p>
                                            </div>
                                            <div class="activity-time">
                                                <?= formatDateTime($activity['time'], 'M j, g:i A') ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../assets/js/dashboard.js"></script>
    <script src="../assets/js/forms.js"></script>
    <script src="../assets/js/table-actions.js"></script>
    <script src="../assets/js/image-upload.js"></script>
</body>
</html>

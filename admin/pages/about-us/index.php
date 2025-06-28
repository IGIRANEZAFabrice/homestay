<?php
/**
 * About Us Page Management - List View
 * Professional admin interface for managing about_us_page content
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

// Get flash message if any
$flash_message = getFlashMessage();

// Get about us page data (there should only be one record)
$about_us = null;
$query = "SELECT * FROM about_us_page ORDER BY id DESC LIMIT 1";
$result = executeQuery($query);

if ($result && $result->num_rows > 0) {
    $about_us = $result->fetch_assoc();
}

$page_title = "About Us Page Management";
$current_section = "about-us";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Virunga Homestay Admin</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Enhanced styles for about us management */
        .content-card {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
            color: var(--white);
            padding: 25px 30px;
            border-bottom: none;
        }

        .card-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: var(--border-radius-sm);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(39, 174, 96, 0.2);
        }

        .card-body {
            padding: 30px;
        }

        .content-preview {
            display: grid;
            gap: 30px;
        }

        .preview-section {
            background: var(--gray-50);
            border-radius: var(--border-radius);
            padding: 25px;
            border: 1px solid var(--gray-200);
        }

        .preview-section h4 {
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .preview-item {
            margin-bottom: 20px;
        }

        .preview-item:last-child {
            margin-bottom: 0;
        }

        .preview-item label {
            display: block;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .preview-item p,
        .content-text {
            margin: 0;
            color: var(--gray-700);
            line-height: 1.6;
            font-size: 14px;
        }

        .image-preview {
            text-align: center;
        }

        .preview-image {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-sm);
            border: 2px solid var(--white);
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .metadata-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .metadata-item label {
            font-weight: 600;
            color: var(--gray-600);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metadata-item span {
            color: var(--gray-800);
            font-size: 14px;
        }

        .card-footer {
            background: var(--gray-50);
            padding: 25px 30px;
            border-top: 1px solid var(--gray-200);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 60px 30px;
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--box-shadow);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: var(--white);
        }

        .empty-state h3 {
            margin: 0 0 15px 0;
            font-size: 24px;
            color: var(--gray-800);
        }

        .empty-state p {
            margin: 0 0 30px 0;
            color: var(--gray-600);
            font-size: 16px;
            line-height: 1.6;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header {
                padding: 20px;
            }

            .card-body {
                padding: 20px;
            }

            .preview-section {
                padding: 20px;
            }

            .metadata-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="../../dashboard.php" class="sidebar-logo">
                    <i class="fas fa-mountain"></i>
                    <span class="nav-text">Virunga Admin</span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-item">
                    <a href="../../dashboard.php" class="nav-link">
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
                        <i class="fas fa-info-circle"></i>
                        <span class="nav-text">About Us</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="../homepage-about/index.php" class="nav-link">
                        <i class="fas fa-home"></i>
                        <span class="nav-text">Homepage About</span>
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
                    <h1 class="page-title">About Us Page Management</h1>
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


                <?php if ($about_us): ?>
                    <!-- About Us Content Display -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3><i class="fas fa-file-alt"></i> Current About Us Content</h3>
                            <div class="card-actions">
                                <span class="status-badge status-active">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="content-preview">
                                <div class="preview-section">
                                    <h4><i class="fas fa-heading"></i> Main Content</h4>
                                    <div class="preview-item">
                                        <label>Title:</label>
                                        <p><?php echo htmlspecialchars($about_us['title']); ?></p>
                                    </div>
                                    <?php if (!empty($about_us['subtitle'])): ?>
                                        <div class="preview-item">
                                            <label>Subtitle:</label>
                                            <p><?php echo htmlspecialchars($about_us['subtitle']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="preview-item">
                                        <label>Content:</label>
                                        <div class="content-text"><?php echo nl2br(htmlspecialchars(substr($about_us['content'], 0, 300))); ?><?php echo strlen($about_us['content']) > 300 ? '...' : ''; ?></div>
                                    </div>
                                </div>

                                <div class="preview-section">
                                    <h4><i class="fas fa-bullseye"></i> Mission, Vision & Values</h4>
                                    <?php if (!empty($about_us['mission'])): ?>
                                        <div class="preview-item">
                                            <label>Mission:</label>
                                            <div class="content-text"><?php echo nl2br(htmlspecialchars(substr($about_us['mission'], 0, 200))); ?><?php echo strlen($about_us['mission']) > 200 ? '...' : ''; ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($about_us['vision'])): ?>
                                        <div class="preview-item">
                                            <label>Vision:</label>
                                            <div class="content-text"><?php echo nl2br(htmlspecialchars(substr($about_us['vision'], 0, 200))); ?><?php echo strlen($about_us['vision']) > 200 ? '...' : ''; ?></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($about_us['values'])): ?>
                                        <div class="preview-item">
                                            <label>Values:</label>
                                            <div class="content-text"><?php echo nl2br(htmlspecialchars(substr($about_us['values'], 0, 200))); ?><?php echo strlen($about_us['values']) > 200 ? '...' : ''; ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($about_us['image'])): ?>
                                    <div class="preview-section">
                                        <h4><i class="fas fa-image"></i> Featured Image</h4>
                                        <div class="image-preview">
                                            <img src="../../../<?php echo htmlspecialchars($about_us['image']); ?>" alt="About Us Image" class="preview-image">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="preview-section">
                                    <h4><i class="fas fa-info-circle"></i> Metadata</h4>
                                    <div class="metadata-grid">
                                        <div class="metadata-item">
                                            <label>Created:</label>
                                            <span><?php echo date('M j, Y g:i A', strtotime($about_us['created_at'])); ?></span>
                                        </div>
                                        <div class="metadata-item">
                                            <label>Last Updated:</label>
                                            <span><?php echo date('M j, Y g:i A', strtotime($about_us['updated_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="action-buttons">
                                <a href="edit.php?id=<?php echo $about_us['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Content
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- No Content State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3>No About Us Content</h3>
                        <p>Create your about us page content to get started. This will include your main story, mission, vision, and values.</p>
                        <a href="edit.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create About Us Content
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script>
        // Enhanced dashboard functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle functionality
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const adminWrapper = document.querySelector('.admin-wrapper');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    adminWrapper.classList.toggle('sidebar-collapsed');
                });
            }

            // User dropdown functionality
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                const userInfo = userDropdown.querySelector('.user-info');
                const dropdownMenu = userDropdown.querySelector('.dropdown-menu');

                userInfo.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    dropdownMenu.classList.remove('show');
                });
            }

            // Alert close functionality
            const alertCloses = document.querySelectorAll('.alert-close');
            alertCloses.forEach(function(closeBtn) {
                closeBtn.addEventListener('click', function() {
                    this.parentElement.remove();
                });
            });

            // Add smooth transitions
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                link.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>

</html>

<?php
/**
 * Homepage About Section Management - List View
 * Professional admin interface for managing homepage_about content
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

// Get homepage about data (there should only be one record)
$homepage_about = null;
$query = "SELECT * FROM homepage_about ORDER BY id DESC LIMIT 1";
$result = executeQuery($query);

if ($result && $result->num_rows > 0) {
    $homepage_about = $result->fetch_assoc();
}

$page_title = "Homepage About Section Management";
$current_section = "homepage-about";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Virunga Homestay Admin</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Enhanced styles for homepage about management */
        .content-preview-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 20px;
        }

        .content-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .content-section {
            background: var(--gray-50);
            padding: 20px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--secondary-color);
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-label i {
            color: var(--secondary-color);
        }

        .content-title-display {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: var(--gray-800);
            line-height: 1.4;
        }

        .content-text {
            color: var(--gray-700);
            line-height: 1.6;
            margin: 0;
        }

        .content-text p {
            margin: 0 0 10px 0;
        }

        .content-text p:last-child {
            margin-bottom: 0;
        }

        .btn-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
            padding: 0;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn-link:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .content-metadata {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            padding: 20px;
        }

        .metadata-grid {
            display: grid;
            gap: 15px;
        }

        .metadata-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .metadata-item i {
            color: var(--secondary-color);
            width: 16px;
            text-align: center;
        }

        .metadata-content {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .metadata-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metadata-value {
            font-size: 14px;
            color: var(--gray-800);
        }

        .content-image-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .image-container {
            position: relative;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            background: var(--gray-100);
        }

        .content-image {
            width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: cover;
            display: block;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: var(--transition);
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }

        .btn-outline-secondary {
            color: var(--gray-600);
            border-color: var(--gray-300);
            background: transparent;
        }

        .btn-outline-secondary:hover {
            color: var(--white);
            background: var(--gray-600);
            border-color: var(--gray-600);
        }

        .empty-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .content-preview-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .content-actions {
                flex-direction: column;
                gap: 10px;
            }

            .empty-actions {
                flex-direction: column;
                align-items: center;
            }
        }

        /* Animation for slide out */
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
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
                    <a href="../about-us/index.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        <span class="nav-text">About Us</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Homepage About Section</h1>
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

            <!-- Flash Messages -->
            <?php if ($flash_message): ?>
                <div class="flash-message flash-<?php echo $flash_message['type']; ?>">
                    <i
                        class="fas fa-<?php echo $flash_message['type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($flash_message['message']); ?>
                    <button class="flash-close">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Content Area -->
            <div class="admin-content">
                

                <?php if ($homepage_about): ?>
                    <!-- Homepage About Content Display -->
                    <div class="content-card">
                        <div class="card-body">
                            <div class="content-preview-grid">
                                <!-- Content Information -->
                                <div class="content-info">
                                    <div class="content-section">
                                        <div class="section-label">
                                            <i class="fas fa-heading"></i>
                                            <span>Title</span>
                                        </div>
                                        <h4 class="content-title-display">
                                            <?php echo htmlspecialchars($homepage_about['title']); ?></h4>
                                    </div>

                                    <div class="content-section">
                                        <div class="section-label">
                                            <i class="fas fa-align-left"></i>
                                            <span>Description</span>
                                        </div>
                                        <div class="content-text description-preview">
                                            <?php
                                            $description = htmlspecialchars($homepage_about['description']);
                                            if (strlen($description) > 250) {
                                                echo '<p>' . substr($description, 0, 250) . '...</p>';
                                                echo '<button class="btn btn-link btn-sm show-more" onclick="toggleDescription()">Show More</button>';
                                                echo '<div class="full-description" style="display: none;">';
                                                echo '<p>' . nl2br($description) . '</p>';
                                                echo '<button class="btn btn-link btn-sm show-less" onclick="toggleDescription()">Show Less</button>';
                                                echo '</div>';
                                            } else {
                                                echo '<p>' . nl2br($description) . '</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="content-metadata">
                                        <div class="metadata-grid">
                                            <div class="metadata-item">
                                                <i class="fas fa-calendar-plus"></i>
                                                <div class="metadata-content">
                                                    <span class="metadata-label">Created</span>
                                                    <span
                                                        class="metadata-value"><?php echo date('M j, Y g:i A', strtotime($homepage_about['created_at'])); ?></span>
                                                </div>
                                            </div>
                                            <div class="metadata-item">
                                                <i class="fas fa-calendar-edit"></i>
                                                <div class="metadata-content">
                                                    <span class="metadata-label">Last Updated</span>
                                                    <span
                                                        class="metadata-value"><?php echo date('M j, Y g:i A', strtotime($homepage_about['updated_at'])); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Featured Image -->
                                <?php if (!empty($homepage_about['image'])): ?>
                                    <div class="content-image-section">
                                        <div class="section-label">
                                            <i class="fas fa-image"></i>
                                            <span>Featured Image</span>
                                        </div>
                                        <div class="image-container">
                                            <img src="/homestay/uploads/homeabout/<?php echo htmlspecialchars($homepage_about['image']); ?>"
                                                alt="Homepage about image" class="content-image">
                                            <div class="image-overlay">
                                                <a href="/homestay/uploads/homeabout/<?php echo htmlspecialchars($homepage_about['image']); ?>"
                                                    target="_blank" class="btn btn-sm btn-light">
                                                    <i class="fas fa-expand"></i> View Full Size
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="action-buttons">
                                <a href="edit.php?id=<?php echo $homepage_about['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Content
                                </a>
                                <a href="../../../" target="_blank" class="btn btn-outline-secondary">
                                    <i class="fas fa-external-link-alt"></i> View on Homepage
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- No Content State -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3>No Homepage About Content</h3>
                        <p>Create your homepage about section content to introduce visitors to your homestay. This content
                            will appear prominently on your homepage and help visitors understand what makes your homestay
                            special.</p>
                        <div class="empty-actions">
                            <a href="edit.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create Homepage About Content
                            </a>
                            <a href="../../../" target="_blank" class="btn btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i> View Current Homepage
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
    <script src="../../assets/js/utils.js"></script>

    <script>
        // Toggle description functionality
        function toggleDescription() {
            const preview = document.querySelector('.description-preview p');
            const fullDescription = document.querySelector('.full-description');
            const showMore = document.querySelector('.show-more');
            const showLess = document.querySelector('.show-less');

            if (fullDescription.style.display === 'none') {
                preview.style.display = 'none';
                showMore.style.display = 'none';
                fullDescription.style.display = 'block';
            } else {
                preview.style.display = 'block';
                showMore.style.display = 'inline-block';
                fullDescription.style.display = 'none';
            }
        }

        // Flash message auto-hide
        document.addEventListener('DOMContentLoaded', function () {
            const flashMessages = document.querySelectorAll('.flash-message');
            flashMessages.forEach(function (message) {
                const closeBtn = message.querySelector('.flash-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        message.style.animation = 'slideOutRight 0.3s ease-out forwards';
                        setTimeout(() => message.remove(), 300);
                    });
                }

                // Auto-hide after 5 seconds
                setTimeout(() => {
                    if (message.parentNode) {
                        message.style.animation = 'slideOutRight 0.3s ease-out forwards';
                        setTimeout(() => message.remove(), 300);
                    }
                }, 5000);
            });
        });
    </script>
</body>

</html>
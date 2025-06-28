<?php
/**
 * Reviews Management - List View
 * Professional admin interface for managing reviews with rating system and moderation
 */

// Suppress warnings for production
error_reporting(E_ERROR | E_PARSE);

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

// Pagination settings
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$rating_filter = isset($_GET['rating']) ? $_GET['rating'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$featured_filter = isset($_GET['featured']) ? $_GET['featured'] : '';

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR review_content LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

if ($rating_filter !== '') {
    $where_conditions[] = "rating = ?";
    $params[] = intval($rating_filter);
    $param_types .= 'i';
}

if ($status_filter !== '') {
    $where_conditions[] = "is_active = ?";
    $params[] = intval($status_filter);
    $param_types .= 'i';
}

if ($featured_filter !== '') {
    $where_conditions[] = "is_featured = ?";
    $params[] = intval($featured_filter);
    $param_types .= 'i';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM reviews $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Get reviews data
$query = "SELECT id, name, rating, review_content, is_active, is_featured, created_at, updated_at
          FROM reviews
          $where_clause
          ORDER BY created_at DESC
          LIMIT $limit OFFSET $offset";

$reviews = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Ensure reviews is always an array
if (!is_array($reviews)) {
    $reviews = [];
}

// Get review statistics
$stats_query = "SELECT
    COUNT(*) as total_reviews,
    AVG(rating) as avg_rating,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as approved_reviews,
    COUNT(CASE WHEN is_active = 0 THEN 1 END) as pending_reviews,
    COUNT(CASE WHEN is_featured = 1 THEN 1 END) as featured_reviews
    FROM reviews";
$stats = getSingleRow($stats_query);

// Ensure stats has default values
if (!is_array($stats)) {
    $stats = [
        'total_reviews' => 0,
        'avg_rating' => 0,
        'approved_reviews' => 0,
        'pending_reviews' => 0,
        'featured_reviews' => 0
    ];
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Reviews', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .rating-stars {
            color: #ffc107;
            font-size: 16px;
        }
        
        .rating-stars .empty {
            color: #e9ecef;
        }
        
        .review-text {
            max-width: 300px;
            line-height: 1.4;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--gray-600);
            font-size: 14px;
        }
        
        .featured-badge {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #8b5a00;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .quick-actions {
            display: flex;
            gap: 5px;
        }
        
        .quick-action-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .approve-btn {
            background: var(--success-color);
            color: white;
        }
        
        .approve-btn:hover {
            background: #27ae60;
        }
        
        .reject-btn {
            background: var(--danger-color);
            color: white;
        }
        
        .reject-btn:hover {
            background: #c0392b;
        }
        
        .feature-btn {
            background: #ffd700;
            color: #8b5a00;
        }
        
        .feature-btn:hover {
            background: #ffed4e;
        }
        
        .unfeature-btn {
            background: var(--gray-400);
            color: white;
        }
        
        .unfeature-btn:hover {
            background: var(--gray-500);
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Reviews Management</h1>
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

                <!-- Breadcrumb -->
                <?= generateBreadcrumb($breadcrumbs) ?>

                <!-- Statistics Cards -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['total_reviews']) ?></div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['avg_rating'], 1) ?></div>
                        <div class="stat-label">Average Rating</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['approved_reviews']) ?></div>
                        <div class="stat-label">Active Reviews</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['pending_reviews']) ?></div>
                        <div class="stat-label">Inactive Reviews</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['featured_reviews']) ?></div>
                        <div class="stat-label">Featured Reviews</div>
                    </div>
                </div>

                <!-- Reviews Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Customer Reviews (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search reviews..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Rating Filter -->
                            <select class="filter-select" id="rating-filter">
                                <option value="">All Ratings</option>
                                <option value="5" <?= $rating_filter === '5' ? 'selected' : '' ?>>5 Stars</option>
                                <option value="4" <?= $rating_filter === '4' ? 'selected' : '' ?>>4 Stars</option>
                                <option value="3" <?= $rating_filter === '3' ? 'selected' : '' ?>>3 Stars</option>
                                <option value="2" <?= $rating_filter === '2' ? 'selected' : '' ?>>2 Stars</option>
                                <option value="1" <?= $rating_filter === '1' ? 'selected' : '' ?>>1 Star</option>
                            </select>
                            
                            <!-- Status Filter -->
                            <select class="filter-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="1" <?= $status_filter === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $status_filter === '0' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            
                            <!-- Featured Filter -->
                            <select class="filter-select" id="featured-filter">
                                <option value="">All Reviews</option>
                                <option value="1" <?= $featured_filter === '1' ? 'selected' : '' ?>>Featured</option>
                                <option value="0" <?= $featured_filter === '0' ? 'selected' : '' ?>>Not Featured</option>
                            </select>
                            

                        </div>
                    </div>

                    <?php if (empty($reviews)): ?>
                        <div class="table-empty">
                            <i class="fas fa-star"></i>
                            <h3>No Reviews Found</h3>
                            <p>No reviews match your current search criteria.</p>
                            <p class="text-muted">Reviews will appear here when customers submit them.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="sortable">Customer</th>
                                    <th width="120">Rating</th>
                                    <th width="300">Review</th>
                                    <th width="100">Status</th>
                                    <th width="100">Featured</th>
                                    <th width="150">Date</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($review['name'] ?? 'Unknown') ?></strong>
                                        </td>
                                        <td>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?= $i <= ($review['rating'] ?? 0) ? '' : 'empty' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <small class="text-muted">(<?= $review['rating'] ?? 0 ?>/5)</small>
                                        </td>
                                        <td>
                                            <div class="review-text">
                                                <?= htmlspecialchars(truncateText($review['review_content'] ?? '', 150)) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($review['is_active']): ?>
                                                <span class="status-badge approved">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge pending">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($review['is_featured']): ?>
                                                <span class="featured-badge">Featured</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="table-date">
                                            <?= formatDateTime($review['created_at'] ?? '') ?>
                                        </td>
                                        <td class="table-actions-cell">
                                            <div class="quick-actions">
                                                <?php if (!$review['is_active']): ?>
                                                    <button class="btn btn-sm btn-success"
                                                            onclick="quickAction(<?= $review['id'] ?>, 'approve')"
                                                            title="Approve Review">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-warning"
                                                            onclick="quickAction(<?= $review['id'] ?>, 'reject')"
                                                            title="Reject Review">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($review['is_active']): ?>
                                                    <?php if (!$review['is_featured']): ?>
                                                        <button class="btn btn-sm btn-primary"
                                                                onclick="quickAction(<?= $review['id'] ?>, 'feature')"
                                                                title="Feature Review">
                                                            <i class="fas fa-star"></i> Feature
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-secondary"
                                                                onclick="quickAction(<?= $review['id'] ?>, 'unfeature')"
                                                                title="Remove from Featured">
                                                            <i class="fas fa-star-half-alt"></i> Unfeature
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="table-pagination">
                                <div class="pagination-info">
                                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?> entries
                                </div>
                                <?= generatePagination($page, $total_pages, 'index.php', [
                                    'search' => $search, 
                                    'rating' => $rating_filter, 
                                    'status' => $status_filter, 
                                    'featured' => $featured_filter
                                ]) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
    
    <script>
        // Quick action functions
        function quickAction(reviewId, action) {
            const actionText = {
                'approve': 'approve',
                'reject': 'reject',
                'feature': 'feature',
                'unfeature': 'remove from featured'
            };
            
            if (confirm(`Are you sure you want to ${actionText[action]} this review?`)) {
                fetch('quick-actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        review_id: reviewId,
                        action: action
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Action failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }
        }

        // Filter functionality
        function updateFilters() {
            const url = new URL(window.location);
            const search = document.getElementById('search-input').value.trim();
            const rating = document.getElementById('rating-filter').value;
            const status = document.getElementById('status-filter').value;
            const featured = document.getElementById('featured-filter').value;

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (rating) {
                url.searchParams.set('rating', rating);
            } else {
                url.searchParams.delete('rating');
            }

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            if (featured) {
                url.searchParams.set('featured', featured);
            } else {
                url.searchParams.delete('featured');
            }

            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        // Search functionality
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                updateFilters();
            }, 500);
        });

        // Filter change events
        document.getElementById('rating-filter').addEventListener('change', updateFilters);
        document.getElementById('status-filter').addEventListener('change', updateFilters);
        document.getElementById('featured-filter').addEventListener('change', updateFilters);
    </script>
</body>
</html>

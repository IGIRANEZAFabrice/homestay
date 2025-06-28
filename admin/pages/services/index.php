<?php
/**
 * Services Management - List View
 * Professional admin interface for managing services with categories and pricing
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

// Pagination settings
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

if ($status_filter !== '') {
    if ($status_filter === 'active') {
        $where_conditions[] = "is_active = 1";
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = "is_active = 0";
    }
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM services $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Get services data
$query = "SELECT id, title, description, image, display_order, is_active, status, created_at, updated_at
          FROM services
          $where_clause
          ORDER BY display_order ASC, title ASC
          LIMIT $limit OFFSET $offset";

$services = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Get service statistics
$stats_query = "SELECT
    COUNT(*) as total_services,
    COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_services,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as status_active_services
    FROM services";
$stats = getSingleRow($stats_query);

// Categories are not used in current schema
$categories = [];

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Services', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
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
        
        .category-badge {
            background: var(--secondary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .duration-badge {
            background: var(--info-color);
            color: white;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .availability-indicator {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        
        .availability-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        .available .availability-dot {
            background: var(--success-color);
        }
        
        .unavailable .availability-dot {
            background: var(--danger-color);
        }
        
        .service-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-color-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 14px;
            margin-right: 10px;
        }
        
        .service-name-cell {
            display: flex;
            align-items: center;
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Services Management</h1>
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
                        <div class="stat-number"><?= number_format($stats['total_services']) ?></div>
                        <div class="stat-label">Total Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['active_services']) ?></div>
                        <div class="stat-label">Active Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['status_active_services']) ?></div>
                        <div class="stat-label">Status Active</div>
                    </div>
                </div>

                <!-- Services Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Services (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Status Filter -->
                            <select class="filter-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            
                            <!-- Add Button -->
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Service
                            </a>
                        </div>
                    </div>

                    <?php if (empty($services)): ?>
                        <div class="table-empty">
                            <i class="fas fa-concierge-bell"></i>
                            <h3>No Services Found</h3>
                            <p>No services match your current search criteria.</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Service
                            </a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="sortable">Service Title</th>
                                    <th width="300">Description</th>
                                    <th width="100">Display Order</th>
                                    <th width="100">Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                    <tr>
                                        <td>
                                            <div class="service-name-cell">
                                                <div class="service-icon">
                                                    <i class="fas fa-concierge-bell"></i>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($service['title']) ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-text">
                                                <?= htmlspecialchars(truncateText($service['description'], 150)) ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="order-badge"><?= $service['display_order'] ?></span>
                                        </td>
                                        <td>
                                            <?= getStatusBadge($service['is_active']) ?>
                                        </td>
                                        <td class="table-actions-cell">
                                            <div class="action-buttons">
                                                <a href="edit.php?id=<?= $service['id'] ?>"
                                                   class="action-btn edit"
                                                   title="Edit Service">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $service['id'] ?>"
                                                   class="action-btn delete"
                                                   title="Delete Service"
                                                   data-item-name="<?= htmlspecialchars($service['title']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
                                    'status' => $status_filter
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
        // Filter functionality
        function updateFilters() {
            const url = new URL(window.location);
            const search = document.getElementById('search-input').value.trim();
            const category = document.getElementById('category-filter').value;
            const status = document.getElementById('status-filter').value;
            const availability = document.getElementById('availability-filter').value;

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (category) {
                url.searchParams.set('category', category);
            } else {
                url.searchParams.delete('category');
            }

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }

            if (availability) {
                url.searchParams.set('availability', availability);
            } else {
                url.searchParams.delete('availability');
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
        document.getElementById('category-filter').addEventListener('change', updateFilters);
        document.getElementById('status-filter').addEventListener('change', updateFilters);
        document.getElementById('availability-filter').addEventListener('change', updateFilters);
    </script>
</body>
</html>

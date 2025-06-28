<?php
/**
 * Rooms Management - List View
 * Professional admin interface for managing rooms with types, amenities, and pricing
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
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$availability_filter = isset($_GET['availability']) ? $_GET['availability'] : '';

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
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM rooms $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Get rooms data
$query = "SELECT id, title, description, image, status, created_at, updated_at
          FROM rooms
          $where_clause
          ORDER BY created_at DESC
          LIMIT $limit OFFSET $offset";

$rooms = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Ensure rooms is always an array
if (!is_array($rooms)) {
    $rooms = [];
}

// Get room statistics
$stats_query = "SELECT
    COUNT(*) as total_rooms,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_rooms,
    COUNT(CASE WHEN status = 'inactive' THEN 1 END) as inactive_rooms,
    COUNT(CASE WHEN status = 'maintenance' THEN 1 END) as maintenance_rooms
    FROM rooms";
$stats = getSingleRow($stats_query);

// Ensure stats has default values
if (!is_array($stats)) {
    $stats = [
        'total_rooms' => 0,
        'active_rooms' => 0,
        'inactive_rooms' => 0,
        'maintenance_rooms' => 0
    ];
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Rooms', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms Management - Virunga Homestay Admin</title>
    
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
        
        .room-images {
            display: flex;
            gap: 5px;
        }
        
        .room-image-thumb {
            width: 40px;
            height: 30px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid var(--gray-300);
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-width: 200px;
        }
        
        .amenity-tag {
            background: var(--gray-100);
            color: var(--gray-700);
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 11px;
            white-space: nowrap;
        }
        
        .room-type-badge {
            background: var(--secondary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
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
                    <h1 class="page-title">Rooms Management</h1>
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
                        <div class="stat-number"><?= number_format($stats['total_rooms']) ?></div>
                        <div class="stat-label">Total Rooms</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['active_rooms']) ?></div>
                        <div class="stat-label">Active Rooms</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['inactive_rooms']) ?></div>
                        <div class="stat-label">Inactive Rooms</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['maintenance_rooms']) ?></div>
                        <div class="stat-label">Maintenance</div>
                    </div>
                </div>

                <!-- Rooms Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Rooms (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search rooms..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Status Filter -->
                            <select class="filter-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="maintenance" <?= $status_filter === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                            </select>
                            
                            <!-- Add Button -->
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Room
                            </a>
                        </div>
                    </div>

                    <?php if (empty($rooms)): ?>
                        <div class="table-empty">
                            <i class="fas fa-bed"></i>
                            <h3>No Rooms Found</h3>
                            <p>No rooms match your current search criteria.</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Room
                            </a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="80">Image</th>
                                    <th class="sortable">Room Name</th>
                                    <th>Description</th>
                                    <th width="100">Status</th>
                                    <th width="150">Date Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td>
                                            <div class="room-images">
                                                <?php if (!empty($room['image']) && $room['image'] !== 'default-room.jpg'): ?>
                                                    <?php
                                                        // Handle both filename-only and full path cases
                                                        $image_src = (strpos($room['image'], 'uploads/') === 0)
                                                            ? '/homestay/' . $room['image']
                                                            : '/homestay/uploads/rooms/' . $room['image'];
                                                    ?>
                                                    <img src="<?= htmlspecialchars($image_src) ?>"
                                                         alt="Room image"
                                                         class="room-image-thumb">
                                                <?php else: ?>
                                                    <div class="room-image-thumb" style="background-color: var(--gray-200); display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-bed text-muted" style="font-size: 12px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($room['title'] ?? 'Untitled Room') ?></strong>
                                            <?php if (!empty($room['description'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars(truncateText($room['description'], 50)) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="room-description">
                                                <?= htmlspecialchars(truncateText($room['description'] ?? '', 100)) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $room['status'] ?? 'active';
                                            $status_class = $status === 'active' ? 'approved' : ($status === 'maintenance' ? 'warning' : 'pending');
                                            ?>
                                            <span class="status-badge <?= $status_class ?>"><?= ucfirst($status) ?></span>
                                        </td>
                                        <td class="table-date">
                                            <?= formatDateTime($room['created_at'] ?? '') ?>
                                        </td>
                                        <td class="table-actions-cell">
                                            <div class="action-buttons">
                                                <a href="edit.php?id=<?= $room['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Edit Room">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $room['id'] ?>" 
                                                   class="action-btn delete" 
                                                   title="Delete Room"
                                                   data-item-name="<?= htmlspecialchars($room['name']) ?>">
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
            const status = document.getElementById('status-filter').value;

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
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
        document.getElementById('status-filter').addEventListener('change', updateFilters);
    </script>
</body>
</html>

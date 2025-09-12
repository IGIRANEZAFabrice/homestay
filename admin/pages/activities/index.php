<?php
/**
 * Activities Management - List View
 * Professional admin interface for managing activities
 */

// Basic test to see if the page is loading
error_log("Activities page is loading...");

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
try {
    requireAuth();
} catch (Exception $e) {
    error_log("Authentication error: " . $e->getMessage());
    // For debugging, you can temporarily comment out the authentication
    // requireAuth();
}

// Include image helpers
require_once '../../../include/image_helpers.php';

// Include database connection and helpers
require_once '../../backend/database/connection.php';
require_once '../../backend/api/utils/helpers.php';

// Get current user
$current_user = getCurrentUser();

// Get flash message if any
$flash_message = getFlashMessage();

// No pagination - show all activities

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR content LIKE ? OR duration LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'sss';
}

if ($status_filter !== '') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for display
$count_query = "SELECT COUNT(*) as total FROM activities $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);

if ($total_result === false) {
    error_log("Failed to get total count from database");
    $total_records = 0;
} else {
    $total_records = $total_result['total'] ?? 0;
}

// Get activities data
$query = "SELECT id, title, content, duration, price, image, display_order, is_active, status, created_at, updated_at 
          FROM activities 
          $where_clause 
          ORDER BY display_order ASC, created_at DESC";

$activities = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Debug: Check if activities were loaded
if ($activities === false) {
    error_log("Failed to load activities from database");
    $activities = [];
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Activities', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities Management - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">Activities Management</h1>
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

                                 <!-- Debug Information (remove in production) -->
                 <?php if (isset($_GET['debug'])): ?>
                     <div class="alert alert-info">
                         <strong>Debug Info:</strong><br>
                         Total Records: <?= $total_records ?><br>
                         Search: "<?= htmlspecialchars($search) ?>"<br>
                         Status Filter: "<?= htmlspecialchars($status_filter) ?>"<br>
                         Activities Count: <?= count($activities) ?><br>
                         Query: <?= htmlspecialchars($query) ?><br>
                         Where Clause: <?= htmlspecialchars($where_clause) ?><br>
                         Params: <?= htmlspecialchars(json_encode($params)) ?>
                     </div>
                 <?php endif; ?>

                <!-- Activities Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Activities (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search activities..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Status Filter -->
                            <select class="filter-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                            
                            <!-- Add Button -->
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Activity
                            </a>
                        </div>
                    </div>

                    <?php if (empty($activities)): ?>
                        <div class="table-empty">
                            <i class="fas fa-hiking"></i>
                            <h3>No Activities Found</h3>
                            <p>No activities match your current search criteria.</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Activity
                            </a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="60">Image</th>
                                    <th class="sortable">Title</th>
                                    <th width="200">Content</th>
                                    <th width="100" class="sortable">Order</th>
                                    <th width="100">Status</th>
                                    <th width="150">Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($activity['image'])): ?>
                                                <img src="/homestay/uploads/activities/<?= htmlspecialchars($activity['image']) ?>" 
                                                     alt="<?= htmlspecialchars($activity['title']) ?>" 
                                                     class="table-image">
                                            <?php else: ?>
                                                <div class="table-image" style="background-color: var(--gray-200); display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($activity['title']) ?></strong>
                                        </td>
                                        <td>
                                            <div class="table-text">
                                                <?= htmlspecialchars(truncateText($activity['content'], 100)) ?>
                                            </div>
                                        </td>
                                        <td class="table-number">
                                            <?= $activity['display_order'] ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $status_text = ucfirst($activity['status']);
                                            $is_active = $activity['status'] === 'active';
                                            echo getStatusBadge($is_active, $status_text);
                                            ?>
                                        </td>
                                        <td class="table-date">
                                            <?= formatDateTime($activity['created_at']) ?>
                                        </td>
                                        <td class="table-actions-cell">
                                            <div class="action-buttons">
                                                <a href="edit.php?id=<?= $activity['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Edit Activity">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $activity['id'] ?>" 
                                                   class="action-btn delete" 
                                                   title="Delete Activity"
                                                   data-item-name="<?= htmlspecialchars($activity['title']) ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                                                 <!-- Total Records Info -->
                         <?php if ($total_records > 0): ?>
                             <div class="table-pagination">
                                 <div class="pagination-info">
                                     Showing all <?= $total_records ?> entries
                                 </div>
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
         // Search functionality
         document.getElementById('search-input').addEventListener('input', function() {
             clearTimeout(this.searchTimeout);
             this.searchTimeout = setTimeout(() => {
                 const url = new URL(window.location);
                 if (this.value.trim()) {
                     url.searchParams.set('search', this.value.trim());
                 } else {
                     url.searchParams.delete('search');
                 }
                 window.location.href = url.toString();
             }, 500);
         });

         // Status filter
         document.getElementById('status-filter').addEventListener('change', function() {
             const url = new URL(window.location);
             if (this.value) {
                 url.searchParams.set('status', this.value);
             } else {
                 url.searchParams.delete('status');
             }
             window.location.href = url.toString();
         });
     </script>
</body>
</html>

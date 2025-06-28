<?php
/**
 * Cars Management - List View
 * Professional admin interface for managing cars
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

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM cars $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Get cars data
$query = "SELECT id, name, type, transmission, fuel_type, price, image, features, badge, status, created_at
          FROM cars
          $where_clause
          ORDER BY created_at DESC
          LIMIT $limit OFFSET $offset";

$cars = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Cars', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cars Management - Virunga Homestay Admin</title>
    
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Cars Management</h1>
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

                <!-- Cars Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Cars (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search cars..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Add Button -->
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Car
                            </a>
                        </div>
                    </div>

                    <?php if (empty($cars)): ?>
                        <div class="table-empty">
                            <i class="fas fa-car"></i>
                            <h3>No Cars Found</h3>
                            <p>No cars match your current search criteria.</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Car
                            </a>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="60">Image</th>
                                    <th class="sortable">Name</th>
                                    <th width="100">Type</th>
                                    <th width="100">Transmission</th>
                                    <th width="100">Fuel</th>
                                    <th width="120">Price</th>
                                    <th width="100">Status</th>
                                    <th width="150">Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cars as $car): ?>
                                    <?php
                                    // Parse features JSON
                                    $features = json_decode($car['features'], true) ?? [];
                                    $features_display = !empty($features) ? implode(', ', array_slice($features, 0, 3)) : 'No features';
                                    if (count($features) > 3) {
                                        $features_display .= '... +' . (count($features) - 3) . ' more';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($car['image'])): ?>
                                                <img src="/homestay/<?= htmlspecialchars($car['image']) ?>" 
                                                     alt="<?= htmlspecialchars($car['name']) ?>" 
                                                     class="table-image">
                                            <?php else: ?>
                                                <div class="table-image" style="background-color: var(--gray-200); display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-car text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($car['name']) ?></strong>
                                            <?php if (!empty($car['badge'])): ?>
                                                <span class="badge badge-info ml-2"><?= htmlspecialchars(ucfirst($car['badge'])) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-light"><?= htmlspecialchars(ucfirst($car['type'])) ?></span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars(ucfirst($car['transmission'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars(ucfirst($car['fuel_type'])) ?>
                                        </td>
                                        <td class="table-currency">
                                            $<?= number_format($car['price'], 2) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_class = '';
                                            $status_text = ucfirst($car['status']);
                                            switch($car['status']) {
                                                case 'active':
                                                    $status_class = 'badge-success';
                                                    break;
                                                case 'inactive':
                                                    $status_class = 'badge-secondary';
                                                    break;
                                                case 'maintenance':
                                                    $status_class = 'badge-warning';
                                                    break;
                                                default:
                                                    $status_class = 'badge-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                        <td class="table-date">
                                            <?= formatDateTime($car['created_at']) ?>
                                        </td>
                                        <td class="table-actions-cell">
                                            <div class="action-buttons">
                                                <a href="edit.php?id=<?= $car['id'] ?>" 
                                                   class="action-btn edit" 
                                                   title="Edit Car">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete.php?id=<?= $car['id'] ?>" 
                                                   class="action-btn delete" 
                                                   title="Delete Car"
                                                   data-item-name="<?= htmlspecialchars($car['name']) ?>">
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
                                <?= generatePagination($page, $total_pages, 'index.php', ['search' => $search]) ?>
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
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            }, 500);
        });
    </script>
</body>
</html>

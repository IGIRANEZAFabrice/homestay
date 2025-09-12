<?php
/**
 * Shop Items Management - List View
 * Admin Dashboard for Virunga Homestay
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

// Ensure database connection
if (!$conn) {
    die("Database connection failed");
}

// Get current user
$current_user = getCurrentUser();

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_items = $_POST['selected_items'] ?? [];

    if (!empty($selected_items) && in_array($action, ['delete'])) {
        $success_count = 0;
        $error_count = 0;

        foreach ($selected_items as $item_id) {
            $item_id = intval($item_id);
            if ($item_id > 0) {
                // Get item details for image deletion
                $item = getSingleRow("SELECT * FROM shop_items WHERE id = ?", 'i', [$item_id]);

                if ($action === 'delete') {
                    $result = deleteData("DELETE FROM shop_items WHERE id = ?", 'i', [$item_id]);
                    if ($result) {
                        // Delete associated image file
                        if (!empty($item['image'])) {
                            $image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $item['image'];
                            if (file_exists($image_path)) {
                                unlink($image_path);
                            }
                        }
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
        }

        if ($success_count > 0) {
            $message = "Successfully processed $success_count item(s).";
            $message_type = 'success';
        }
        if ($error_count > 0) {
            $message .= " Failed to process $error_count item(s).";
            $message_type = 'warning';
        }

        redirectWithMessage('index.php', $message, $message_type);
    }
}

// Pagination settings
$items_per_page = 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Search functionality
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];
$param_types = '';

if (!empty($search_query)) {
    $where_clause = "WHERE title LIKE ? OR description LIKE ? OR tag LIKE ?";
    $search_param = "%$search_query%";
    $params = [$search_param, $search_param, $search_param];
    $param_types = 'sss';
}

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM shop_items $where_clause";
$stmt = $conn->prepare($count_query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_items = $total_result['total'] ?? 0;
$stmt->close();
$total_pages = ceil($total_items / $items_per_page);

// Get shop items
$query = "SELECT * FROM shop_items $where_clause ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$shop_items = [];
while ($row = $result->fetch_assoc()) {
    $shop_items[] = $row;
}
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Items Management - Virunga Homestay Admin</title>

    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">

    <style>
        .shop-item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .no-image-placeholder {
            width: 60px;
            height: 60px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .price-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .tag-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75em;
        }
    </style>
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
                    <h1 class="page-title">Shop Items Management</h1>
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

        <!-- Page Content -->
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Shop Items Management</h1>
                        <p class="text-muted">Manage your shop products and inventory</p>
                    </div>
                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Item
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="mb-0"><?= $total_items ?></h4>
                                        <p class="mb-0">Total Items</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-shopping-bag fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" name="search"
                                           placeholder="Search items..." value="<?= htmlspecialchars($search_query) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <?php if (!empty($search_query)): ?>
                                    <a href="index.php" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Shop Items Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Shop Items</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" disabled>
                                <i class="fas fa-trash me-2"></i>Delete Selected
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($shop_items)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No shop items found</h5>
                                <p class="text-muted">
                                    <?= !empty($search_query) ? 'Try adjusting your search criteria.' : 'Start by adding your first shop item.' ?>
                                </p>
                                <?php if (empty($search_query)): ?>
                                    <a href="add.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add First Item
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <form id="bulkActionForm" method="POST">
                                <input type="hidden" name="bulk_action" id="bulkActionInput">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                                </th>
                                                <th width="80">Image</th>
                                                <th>Item Details</th>
                                                <th width="120">Price</th>
                                                <th width="100">Tag</th>
                                                <th width="120">Created</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($shop_items as $item): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="selected_items[]"
                                                               value="<?= $item['id'] ?>" class="form-check-input item-checkbox">
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($item['image'])): ?>
                                                            <?php
                                                            // Fix image path for admin display
                                                            $image_src = (strpos($item['image'], 'uploads/') === 0)
                                                                ? '/homestay/' . $item['image']
                                                                : '/homestay/uploads/shop/' . $item['image'];
                                                            ?>
                                                            <img src="<?= htmlspecialchars($image_src) ?>"
                                                                 alt="Item image"
                                                                 class="shop-item-image">
                                                        <?php else: ?>
                                                            <div class="no-image-placeholder">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?= htmlspecialchars($item['title'] ?? 'Untitled Item') ?></strong>
                                                            <?php if (!empty($item['description'])): ?>
                                                                <br><small class="text-muted"><?= htmlspecialchars(truncateText($item['description'], 60)) ?></small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="price-badge">$<?= number_format($item['price'], 2) ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($item['tag'])): ?>
                                                            <span class="tag-badge"><?= htmlspecialchars($item['tag']) ?></span>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?= date('M j, Y', strtotime($item['created_at'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="edit.php?id=<?= $item['id'] ?>"
                                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete.php?id=<?= $item['id'] ?>"
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('Are you sure you want to delete this item?')" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="card-footer">
                            <nav aria-label="Shop items pagination">
                                <ul class="pagination justify-content-center mb-0">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $current_page - 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                        <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $current_page + 1 ?><?= !empty($search_query) ? '&search=' . urlencode($search_query) : '' ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script
        // Bulk actions functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkActionForm = document.getElementById('bulkActionForm');
            const bulkActionInput = document.getElementById('bulkActionInput');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkActionButtons();
            });

            // Individual checkbox change
            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkActionButtons();

                    // Update select all checkbox
                    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                    selectAllCheckbox.checked = checkedCount === itemCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
                });
            });

            // Update bulk action buttons
            function updateBulkActionButtons() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                bulkDeleteBtn.disabled = checkedCount === 0;
            }

            // Bulk delete
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                if (checkedCount > 0 && confirm(`Are you sure you want to delete ${checkedCount} selected item(s)?`)) {
                    bulkActionInput.value = 'delete';
                    bulkActionForm.submit();
                }
            });
        });
    </script>
</body>
</html>
<?php
/**
 * House Rules Management - List View
 */

// Define admin access and start session
if (!defined('ADMIN_ACCESS')) {
    define('ADMIN_ACCESS', true);
}
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

// Handle delete operations
if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];
    
    switch ($type) {
        case 'rule':
            $result = deleteData("DELETE FROM house_rules WHERE id = ?", "i", [$id]);
            break;
        case 'cancellation':
            $result = deleteData("DELETE FROM cancellation_policy WHERE id = ?", "i", [$id]);
            break;
        case 'info':
            $result = deleteData("DELETE FROM house_info_cards WHERE id = ?", "i", [$id]);
            break;
        default:
            // Invalid type
            header("Location: index.php?error=invalid_type");
            exit();
    }
    
    if ($result !== false && $result > 0) {
        header("Location: index.php?success=deleted");
    } else {
        header("Location: index.php?error=delete_failed");
    }
    exit();
}

// Get flash message if any
$flash_message = getFlashMessage();

// Get house rules
$house_rules = getMultipleRows("SELECT * FROM house_rules ORDER BY display_order ASC");

// Get cancellation policies
$cancellation_policies = getMultipleRows("SELECT * FROM cancellation_policy ORDER BY display_order ASC");

// Get info cards
$info_cards = getMultipleRows("SELECT * FROM house_info_cards ORDER BY display_order ASC");

// Page title and breadcrumbs
$page_title = "House Rules Management";
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'House Rules', 'url' => '#']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Virunga Homestay Admin</title>
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom styles for House Rules page */
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            font-weight: 600;
            padding: 12px 20px;
        }
        
        .card-header h5 {
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .card-header h5 i {
            margin-right: 10px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .btn-group .btn {
            margin-right: 5px;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.76rem;
            border-radius: 4px;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
        }
        
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            border-radius: 4px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .btn-toolbar {
                margin-top: 10px;
                justify-content: flex-start;
            }
            
            .btn-group {
                display: flex;
                flex-wrap: wrap;
            }
            
            .btn-group .btn {
                margin-bottom: 5px;
            }
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
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <a href="edit.php?type=rule" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add House Rule
                        </a>
                        <a href="edit.php?type=cancellation" class="btn btn-secondary">
                            <i class="fas fa-plus"></i> Add Cancellation Policy
                        </a>
                        <a href="edit.php?type=info" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Info Card
                        </a>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="admin-content">
                
                <!-- Breadcrumbs -->
                <?php echo generateBreadcrumb($breadcrumbs); ?>
                
                <!-- Flash message -->
                <?php if ($flash_message): ?>
                <div class="alert alert-<?php echo $flash_message['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $flash_message['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- House Rules Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> House Rules</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($house_rules)): ?>
                        <div class="alert alert-info">No house rules found. Click "Add House Rule" to create one.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Icon</th>
                                        <th width="20%">Title</th>
                                        <th width="35%">Content</th>
                                        <th width="10%">Order</th>
                                        <th width="5%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($house_rules as $index => $rule): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><i class="<?php echo htmlspecialchars($rule['icon']); ?> fa-2x"></i></td>
                                        <td><?php echo htmlspecialchars($rule['title']); ?></td>
                                        <td><?php echo truncateText(strip_tags($rule['content']), 100); ?></td>
                                        <td><?php echo $rule['display_order']; ?></td>
                                        <td><?php echo getStatusBadge($rule['is_active']); ?></td>
                                        <td>
                                            <a href="edit.php?type=rule&id=<?php echo $rule['id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger delete-item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $rule['id']; ?>" data-type="rule" data-name="<?php echo htmlspecialchars($rule['title']); ?>" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Cancellation Policy Section -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Cancellation Policies</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($cancellation_policies)): ?>
                        <div class="alert alert-info">No cancellation policies found. Click "Add Cancellation Policy" to create one.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="15%">Icon</th>
                                        <th width="20%">Title</th>
                                        <th width="35%">Content</th>
                                        <th width="10%">Order</th>
                                        <th width="5%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cancellation_policies as $index => $policy): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><i class="<?php echo htmlspecialchars($policy['icon']); ?> fa-2x"></i></td>
                                        <td><?php echo htmlspecialchars($policy['title']); ?></td>
                                        <td><?php echo truncateText(strip_tags($policy['content']), 100); ?></td>
                                        <td><?php echo $policy['display_order']; ?></td>
                                        <td><?php echo getStatusBadge($policy['is_active']); ?></td>
                                        <td>
                                            <a href="edit.php?type=cancellation&id=<?php echo $policy['id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger delete-item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $policy['id']; ?>" data-type="cancellation" data-name="<?php echo htmlspecialchars($policy['title']); ?>" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Info Cards Section -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Info Cards</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($info_cards)): ?>
                        <div class="alert alert-info">No info cards found. Click "Add Info Card" to create one.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Icon</th>
                                        <th width="45%">Content</th>
                                        <th width="10%">Order</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($info_cards as $index => $card): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><i class="<?php echo htmlspecialchars($card['icon']); ?> fa-2x"></i></td>
                                        <td><?php echo htmlspecialchars($card['content']); ?></td>
                                        <td><?php echo $card['display_order']; ?></td>
                                        <td><?php echo getStatusBadge($card['is_active']); ?></td>
                                        <td>
                                            <a href="edit.php?type=info&id=<?php echo $card['id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger delete-item" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $card['id']; ?>" data-type="info" data-name="<?php echo htmlspecialchars($card['content']); ?>" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete-item-name"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirm-delete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/forms.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Delete confirmation modal
        $('.delete-item').on('click', function() {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const name = $(this).data('name');
            
            $('#delete-item-name').text(name);
            $('#confirm-delete').attr('href', 'index.php?delete=1&type=' + type + '&id=' + id);
        });
    </script>
</body>
</html>
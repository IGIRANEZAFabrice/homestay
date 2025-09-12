<?php
/**
 * About Us Management - List View
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
        case 'feature':
            $result = deleteData("DELETE FROM about_features WHERE id = ?", "i", [$id]);
            break;
        case 'guideline':
            $result = deleteData("DELETE FROM about_guidelines WHERE id = ?", "i", [$id]);
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

// Get sections data
$sections_rows = getMultipleRows("SELECT * FROM about_sections ORDER BY display_order ASC");
$sections = [];
foreach ($sections_rows as $row) {
    $sections[$row['section_name']] = $row;
}

// Get features data
$features = getMultipleRows("SELECT * FROM about_features ORDER BY display_order ASC");

// Get guidelines data
$guidelines = getMultipleRows("SELECT * FROM about_guidelines ORDER BY display_order ASC");// Page title
$page_title = "About Us Management";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Virunga Homestay Admin - Manage About Us Content">
    <meta name="robots" content="noindex, nofollow">
    <title>About Us Management - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    <link rel="stylesheet" href="../../assets/css/responsive.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
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
                    <h1 class="page-title">About Us Management</h1>
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
                <div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">About Us Page Management</h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php 
            switch ($_GET['success']) {
                case 'updated':
                    echo "Content updated successfully!";
                    break;
                case 'deleted':
                    echo "Item deleted successfully!";
                    break;
                case 'added':
                    echo "New item added successfully!";
                    break;
                default:
                    echo "Operation completed successfully!";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php 
            switch ($_GET['error']) {
                case 'update_failed':
                    echo "Failed to update content. Please try again.";
                    break;
                case 'delete_failed':
                    echo "Failed to delete item. Please try again.";
                    break;
                case 'invalid_type':
                    echo "Invalid operation type.";
                    break;
                default:
                    echo "An error occurred. Please try again.";
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Main Sections Management -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-puzzle-piece me-2"></i>
                Main Sections
            </h6>
            <div class="header-actions">
                <button class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="left" title="These sections form the main structure of your About Us page">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                                                <?php foreach ($sections as $section): ?>
                            <tr>
                                <td><?php echo ucfirst(str_replace('_', ' ', $section['section_name'])); ?></td>
                                <td><?php echo htmlspecialchars($section['title']); ?></td>
                                <td><?php echo $section['subtitle'] ? htmlspecialchars(substr($section['subtitle'], 0, 100)) . '...' : 'N/A'; ?></td>
                                <td>
                                    <a href="edit.php?type=section&id=<?php echo $section['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Features Management -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Features (Why Choose Us)</h6>
            <a href="edit.php?type=feature&action=add" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Add New Feature
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($features as $feature): ?>
                            <tr>
                                <td><?php echo $feature['display_order']; ?></td>
                                <td><?php echo $feature['icon']; ?></td>
                                <td><?php echo htmlspecialchars($feature['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($feature['description'], 0, 100)) . '...'; ?></td>
                                <td>
                                    <a href="edit.php?type=feature&id=<?php echo $feature['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?delete=1&type=feature&id=<?php echo $feature['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this feature?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Guidelines Management -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Guest Guidelines</h6>
            <a href="edit.php?type=guideline&action=add" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Add New Guideline
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guidelines as $guideline): ?>
                            <tr>
                                <td><?php echo $guideline['display_order']; ?></td>
                                <td><?php echo htmlspecialchars($guideline['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($guideline['content'], 0, 100)) . '...'; ?></td>
                                <td>
                                    <a href="edit.php?type=guideline&id=<?php echo $guideline['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?delete=1&type=guideline&id=<?php echo $guideline['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this guideline?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

                </div><!-- /.container-fluid -->
            </div><!-- /.admin-content -->
        </main><!-- /.admin-main -->
    </div><!-- /.admin-wrapper -->

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
    
    <script>
        $(function() {
            // Initialize DataTables
            $('.table').each(function() {
                $(this).DataTable({
                    pageLength: 10,
                    responsive: true,
                    order: [[0, "asc"]],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            });

            // Initialize Bootstrap tooltips
            $('[data-bs-toggle="tooltip"]').each(function() {
                new bootstrap.Tooltip(this);
            });

            // User dropdown functionality
            $('.user-dropdown').on('click', function(e) {
                e.stopPropagation();
                $(this).find('.dropdown-menu').toggleClass('show');
            });

            // Close dropdown when clicking outside
            $(document).on('click', function() {
                $('.dropdown-menu.show').removeClass('show');
            });

            // Handle alert dismissal
            $('.alert').each(function() {
                new bootstrap.Alert(this);
            });

            // Sidebar toggle for mobile
            $('.sidebar-toggle').on('click', function() {
                $('.admin-sidebar').toggleClass('show');
            });

            // Prevent default on # links
            $('a[href="#"]').on('click', function(e) {
                e.preventDefault();
            });

            // Add active class to current nav item
            $('.nav-link').each(function() {
                if (window.location.pathname.includes($(this).attr('href'))) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>
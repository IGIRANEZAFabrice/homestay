<?php
/**
 * About Us Management - Edit View
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include database connection first
require_once '../../backend/database/connection.php';

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Get current user
$current_user = getCurrentUser();

// Initialize variables
$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'edit';
$item = null;
$error = '';
$success = '';

// Validate type
if (!in_array($type, ['section', 'feature', 'guideline'])) {
    header("Location: index.php?error=invalid_type");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($type) {
        case 'section':
            $title = $_POST['title'] ?? '';
            $subtitle = $_POST['subtitle'] ?? '';
            $content = $_POST['content'] ?? '';
            $image_path = $_POST['image_path'] ?? '';
            
            // Update section
            $result = updateData(
                "UPDATE about_sections SET title = ?, subtitle = ?, content = ?, image_path = ? WHERE id = ?",
                "ssssi",
                [$title, $subtitle, $content, $image_path, $id]
            );
            
            if ($result !== false) {
                $success = "Section updated successfully!";
            } else {
                $error = "Failed to update section.";
            }
            break;
            
        case 'feature':
            $icon = $_POST['icon'] ?? '';
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $display_order = (int)($_POST['display_order'] ?? 0);
            
            if ($action === 'add') {
                // Add new feature
                $result = insertData(
                    "INSERT INTO about_features (icon, title, description, display_order) VALUES (?, ?, ?, ?)",
                    "sssi",
                    [$icon, $title, $description, $display_order]
                );
                
                if ($result !== false) {
                    header("Location: index.php?success=added");
                    exit();
                } else {
                    $error = "Failed to add feature.";
                }
            } else {
                // Update feature
                $result = updateData(
                    "UPDATE about_features SET icon = ?, title = ?, description = ?, display_order = ? WHERE id = ?",
                    "sssii",
                    [$icon, $title, $description, $display_order, $id]
                );
                
                if ($result !== false) {
                    header("Location: index.php?success=updated");
                    exit();
                } else {
                    $error = "Failed to update feature.";
                }
            }
            break;
            
        case 'guideline':
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $display_order = (int)($_POST['display_order'] ?? 0);
            
            if ($action === 'add') {
                // Add new guideline
                $result = insertData(
                    "INSERT INTO about_guidelines (title, content, display_order) VALUES (?, ?, ?)",
                    "ssi",
                    [$title, $content, $display_order]
                );
                
                if ($result !== false) {
                    header("Location: index.php?success=added");
                    exit();
                } else {
                    $error = "Failed to add guideline.";
                }
            } else {
                // Update guideline
                $result = updateData(
                    "UPDATE about_guidelines SET title = ?, content = ?, display_order = ? WHERE id = ?",
                    "ssii",
                    [$title, $content, $display_order, $id]
                );
                
                if ($result !== false) {
                    header("Location: index.php?success=updated");
                    exit();
                } else {
                    $error = "Failed to update guideline.";
                }
            }
            break;
    }
}

// Get item data for editing
if ($action === 'edit' && $id > 0) {
    switch ($type) {
        case 'section':
            $item = getSingleRow("SELECT * FROM about_sections WHERE id = ?", "i", [$id]);
            break;
            
        case 'feature':
            $item = getSingleRow("SELECT * FROM about_features WHERE id = ?", "i", [$id]);
            break;
            
        case 'guideline':
            $item = getSingleRow("SELECT * FROM about_guidelines WHERE id = ?", "i", [$id]);
            break;
    }
    
    if (!$item) {
        header("Location: index.php?error=item_not_found");
        exit();
    }
}

// Set page title based on action and type
$page_title = ($action === 'add' ? 'Add New ' : 'Edit ') . ucfirst($type);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Virunga Homestay Admin</title>
    
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
                    <h1 class="page-title"><?= $page_title ?></h1>
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
        <h1 class="h3 mb-0 text-gray-800"><?php echo $page_title; ?></h1>
        <a href="index.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $page_title; ?> Details</h6>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <?php if ($type === 'section'): ?>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subtitle">Subtitle</label>
                        <textarea class="form-control" id="subtitle" name="subtitle" rows="3"><?php echo htmlspecialchars($item['subtitle'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="6"><?php echo htmlspecialchars($item['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image_path">Image Path</label>
                        <input type="text" class="form-control" id="image_path" name="image_path" value="<?php echo htmlspecialchars($item['image_path'] ?? ''); ?>">
                        <small class="form-text text-muted">Relative path to the image (e.g., ../img/about.jpg)</small>
                    </div>
                    
                <?php elseif ($type === 'feature'): ?>
                    <div class="form-group">
                        <label for="icon">Icon</label>
                        <input type="text" class="form-control" id="icon" name="icon" value="<?php echo htmlspecialchars($item['icon'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Use emoji or icon code (e.g., 🏠, ❤️, 🗺️)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo (int)($item['display_order'] ?? count($features) + 1); ?>" min="1" required>
                    </div>
                    
                <?php elseif ($type === 'guideline'): ?>
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($item['title'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required><?php echo htmlspecialchars($item['content'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="<?php echo (int)($item['display_order'] ?? count($guidelines) + 1); ?>" min="1" required>
                    </div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $action === 'add' ? 'Add' : 'Update'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
</body>
</html>
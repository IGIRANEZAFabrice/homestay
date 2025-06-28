<?php
/**
 * Hero Images Management - List View
 * Professional admin interface for managing hero images with drag-and-drop ordering
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

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR paragraph LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

if ($status_filter !== '') {
    $where_conditions[] = "is_active = ?";
    $params[] = intval($status_filter);
    $param_types .= 'i';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get hero images data ordered by display_order
$query = "SELECT id, title, paragraph, image, is_active, display_order, created_at, updated_at
          FROM hero_images
          $where_clause
          ORDER BY display_order ASC, created_at DESC";

$hero_images = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);
$total_records = count($hero_images);

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Hero Images', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Images Management - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/tables.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Sortable.js for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    
    <style>
        .hero-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .hero-image-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: move;
            position: relative;
        }
        
        .hero-image-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .hero-image-card.sortable-ghost {
            opacity: 0.5;
        }
        
        .hero-image-card.sortable-chosen {
            transform: rotate(5deg);
        }
        
        .hero-image-preview {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        .hero-image-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
        }
        
        .image-overlay {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            color: white;
            z-index: 2;
        }
        
        .image-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }
        
        .image-description {
            font-size: 12px;
            opacity: 0.9;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }
        
        .hero-image-info {
            padding: 15px;
        }
        
        .image-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .display-order {
            background: var(--primary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .image-actions {
            display: flex;
            gap: 8px;
        }
        
        .drag-handle {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px;
            border-radius: 50%;
            cursor: move;
            z-index: 3;
        }
        
        .drag-handle:hover {
            background: rgba(0,0,0,0.9);
        }
        
        .no-image-placeholder {
            background: var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            font-size: 48px;
        }
        
        .save-order-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--success-color);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 50px;
            box-shadow: var(--shadow-lg);
            font-weight: 600;
            display: none;
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .save-order-btn:hover {
            background: #27ae60;
            transform: scale(1.05);
        }
        
        .save-order-btn.show {
            display: flex;
            align-items: center;
            gap: 8px;
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Hero Images Management</h1>
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

                <!-- Hero Images Container -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Hero Images (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search hero images..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            
                            <!-- Status Filter -->
                            <select class="filter-select" id="status-filter">
                                <option value="">All Status</option>
                                <option value="1" <?= $status_filter === '1' ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $status_filter === '0' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            
                            <!-- Add Button -->
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Hero Image
                            </a>
                        </div>
                    </div>

                    <?php if (empty($hero_images)): ?>
                        <div class="table-empty">
                            <i class="fas fa-images"></i>
                            <h3>No Hero Images Found</h3>
                            <p>No hero images match your current search criteria.</p>
                            <a href="add.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add First Hero Image
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle alert-icon"></i>
                            <strong>Tip:</strong> Drag and drop the images to reorder them. The order here determines how they appear on your website.
                        </div>

                        <div class="hero-images-grid" id="hero-images-container">
                            <?php foreach ($hero_images as $image): ?>
                                <div class="hero-image-card" data-id="<?= $image['id'] ?>">
                                    <div class="drag-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    
                                    <div class="hero-image-preview <?= empty($image['image']) ? 'no-image-placeholder' : '' ?>"
                                         <?php if (!empty($image['image'])): ?>
                                         style="background-image: url('/homestay/<?= htmlspecialchars($image['image']) ?>')"
                                         <?php endif; ?>>

                                        <?php if (empty($image['image'])): ?>
                                            <i class="fas fa-image"></i>
                                        <?php else: ?>
                                            <div class="image-overlay">
                                                <div class="image-title"><?= htmlspecialchars($image['title']) ?></div>
                                                <?php if (!empty($image['paragraph'])): ?>
                                                    <div class="image-description"><?= htmlspecialchars(truncateText($image['paragraph'], 80)) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="hero-image-info">
                                        <div class="image-meta">
                                            <div class="display-order">Order: <?= $image['display_order'] ?></div>
                                            <?= getStatusBadge($image['is_active']) ?>
                                        </div>
                                        
                                        <?php if (empty($image['image'])): ?>
                                            <h4><?= htmlspecialchars($image['title']) ?></h4>
                                            <?php if (!empty($image['paragraph'])): ?>
                                                <p class="text-muted"><?= htmlspecialchars(truncateText($image['paragraph'], 100)) ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <div class="image-actions">
                                            <a href="edit.php?id=<?= $image['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?= $image['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               data-item-name="<?= htmlspecialchars($image['title']) ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Save Order Button -->
    <button class="save-order-btn" id="save-order-btn">
        <i class="fas fa-save"></i>
        Save Order
    </button>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/table-actions.js"></script>
    
    <script>
        // Initialize drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('hero-images-container');
            const saveOrderBtn = document.getElementById('save-order-btn');
            let originalOrder = [];
            
            if (container) {
                // Store original order
                originalOrder = Array.from(container.children).map(card => card.dataset.id);
                
                // Initialize Sortable
                const sortable = Sortable.create(container, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.drag-handle',
                    onEnd: function(evt) {
                        // Check if order changed
                        const newOrder = Array.from(container.children).map(card => card.dataset.id);
                        const orderChanged = !arraysEqual(originalOrder, newOrder);
                        
                        if (orderChanged) {
                            saveOrderBtn.classList.add('show');
                        } else {
                            saveOrderBtn.classList.remove('show');
                        }
                        
                        // Update display order numbers
                        updateDisplayOrderNumbers();
                    }
                });
                
                // Save order button click
                saveOrderBtn.addEventListener('click', function() {
                    const newOrder = Array.from(container.children).map((card, index) => ({
                        id: card.dataset.id,
                        order: index + 1
                    }));
                    
                    saveOrder(newOrder);
                });
            }
            
            function updateDisplayOrderNumbers() {
                const cards = container.querySelectorAll('.hero-image-card');
                cards.forEach((card, index) => {
                    const orderElement = card.querySelector('.display-order');
                    if (orderElement) {
                        orderElement.textContent = `Order: ${index + 1}`;
                    }
                });
            }
            
            function arraysEqual(a, b) {
                return a.length === b.length && a.every((val, i) => val === b[i]);
            }
            
            function saveOrder(newOrder) {
                saveOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveOrderBtn.disabled = true;
                
                fetch('update-order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order: newOrder })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update original order
                        originalOrder = Array.from(container.children).map(card => card.dataset.id);
                        
                        // Hide save button
                        saveOrderBtn.classList.remove('show');
                        
                        // Show success message
                        showAlert('Order updated successfully!', 'success');
                    } else {
                        showAlert(data.message || 'Failed to update order', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while updating the order', 'danger');
                })
                .finally(() => {
                    saveOrderBtn.innerHTML = '<i class="fas fa-save"></i> Save Order';
                    saveOrderBtn.disabled = false;
                });
            }
            
            function showAlert(message, type) {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible`;
                alert.innerHTML = `
                    <i class="fas fa-info-circle alert-icon"></i>
                    ${message}
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                const content = document.querySelector('.admin-content');
                content.insertBefore(alert, content.firstChild);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            }
        });

        // Search functionality
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                updateFilters();
            }, 500);
        });

        // Status filter
        document.getElementById('status-filter').addEventListener('change', function() {
            updateFilters();
        });

        function updateFilters() {
            const url = new URL(window.location);
            const search = document.getElementById('search-input').value.trim();
            const statusFilter = document.getElementById('status-filter').value;

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (statusFilter) {
                url.searchParams.set('status', statusFilter);
            } else {
                url.searchParams.delete('status');
            }

            window.location.href = url.toString();
        }
    </script>
</body>
</html>

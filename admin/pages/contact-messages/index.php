<?php
/**
 * Contact Messages Management - List View
 * Professional admin interface for managing contact messages with status tracking
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
$limit = 15;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query conditions
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ssss';
}



if (!empty($date_filter)) {
    $where_conditions[] = "DATE(created_at) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM contact_messages $where_clause";
$total_result = !empty($params) ? getSingleRow($count_query, $param_types, $params) : getSingleRow($count_query);
$total_records = $total_result['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Get contact messages data
$query = "SELECT id, name, email, subject, message, status, created_at
          FROM contact_messages
          $where_clause
          ORDER BY created_at DESC
          LIMIT $limit OFFSET $offset";

$messages = !empty($params) ? getMultipleRows($query, $param_types, $params) : getMultipleRows($query);

// Get message statistics
$stats_query = "SELECT
    COUNT(*) as total_messages,
    COUNT(CASE WHEN status = 'new' OR status IS NULL THEN 1 END) as new_messages,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_messages
    FROM contact_messages";
$stats = getSingleRow($stats_query);

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Contact Messages', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Virunga Homestay Admin</title>
    
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
        
        .message-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-new {
            background: var(--warning-color-light);
            color: var(--warning-color);
        }
        
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .sender-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sender-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-color-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 12px;
        }
        
        .sender-details {
            flex: 1;
        }
        
        .sender-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .sender-email {
            font-size: 12px;
            color: var(--gray-600);
        }
        
        .quick-actions {
            display: flex;
            gap: 5px;
        }
        
        .quick-action-btn {
            background: none;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            font-size: 11px;
            transition: all 0.2s ease;
        }
        
        .quick-action-btn:hover {
            background: var(--gray-100);
        }
        
        .message-row.unread {
            background: #fefefe;
            border-left: 3px solid var(--warning-color);
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
                    <a href="../services/index.php" class="nav-link">
                        <i class="fas fa-concierge-bell"></i>
                        <span class="nav-text">Services</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Contact Messages</h1>
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
                        <div class="stat-number"><?= number_format($stats['total_messages']) ?></div>
                        <div class="stat-label">Total Messages</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['new_messages']) ?></div>
                        <div class="stat-label">New Messages</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= number_format($stats['today_messages']) ?></div>
                        <div class="stat-label">Today's Messages</div>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2 class="table-title">Messages (<?= $total_records ?> total)</h2>
                        <div class="table-actions">
                            <!-- Search -->
                            <div class="table-search">
                                <input type="text" placeholder="Search messages..." value="<?= htmlspecialchars($search) ?>" id="search-input">
                                <i class="fas fa-search"></i>
                            </div>
                            

                            <!-- Date Filter -->
                            <input type="date" class="filter-select" id="date-filter" value="<?= htmlspecialchars($date_filter) ?>">
                        </div>
                    </div>

                    <?php if (empty($messages)): ?>
                        <div class="table-empty">
                            <i class="fas fa-envelope-open"></i>
                            <h3>No Messages Found</h3>
                            <p>No contact messages match your current search criteria.</p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="250">Sender</th>
                                    <th width="200">Subject</th>
                                    <th>Message Preview</th>
                                    <th width="100">Status</th>
                                    <th width="120">Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                    <tr class="message-row <?= $message['status'] === 'new' ? 'unread' : '' ?>">
                                        <td>
                                            <div class="sender-info">
                                                <div class="sender-avatar">
                                                    <?= strtoupper(substr($message['name'], 0, 1)) ?>
                                                </div>
                                                <div class="sender-details">
                                                    <div class="sender-name"><?= htmlspecialchars($message['name']) ?></div>
                                                    <div class="sender-email"><?= htmlspecialchars($message['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($message['subject']) ?></strong>
                                        </td>
                                        <td>
                                            <div class="message-preview">
                                                <?= htmlspecialchars(truncateText($message['message'], 100)) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="message-status status-<?= $message['status'] ?>">
                                                <?= ucfirst($message['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-size: 12px;">
                                                <?= date('M j, Y', strtotime($message['created_at'])) ?><br>
                                                <span class="text-muted"><?= date('g:i A', strtotime($message['created_at'])) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="view.php?id=<?= $message['id'] ?>" 
                                                   class="action-btn view" 
                                                   title="View Message">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <a href="delete.php?id=<?= $message['id'] ?>" 
                                                   class="action-btn delete" 
                                                   title="Delete Message"
                                                   data-item-name="message from <?= htmlspecialchars($message['name']) ?>">
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
                                    'date' => $date_filter
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
            const date = document.getElementById('date-filter').value;

            if (search) {
                url.searchParams.set('search', search);
            } else {
                url.searchParams.delete('search');
            }

            if (date) {
                url.searchParams.set('date', date);
            } else {
                url.searchParams.delete('date');
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
        document.getElementById('date-filter').addEventListener('change', updateFilters);

    </script>
</body>
</html>

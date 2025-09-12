<?php
/**
 * House Rules Management - Edit/Add View
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

// Initialize variables
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'rule';
$item = null;
$is_edit = ($id > 0);

// Set page title and form action based on type and mode
switch ($type) {
    case 'rule':
        $table = 'house_rules';
        $page_title = $is_edit ? 'Edit House Rule' : 'Add New House Rule';
        $form_fields = [
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'icon' => ['label' => 'Icon Class', 'type' => 'text', 'required' => true, 'placeholder' => 'fas fa-utensils'],
            'content' => ['label' => 'Content', 'type' => 'textarea', 'required' => true],
            'section' => ['label' => 'Section', 'type' => 'select', 'options' => [
                'general' => 'General Rules',
                'safety' => 'Safety Rules',
                'amenities' => 'Amenities Rules'
            ]],
            'display_order' => ['label' => 'Display Order', 'type' => 'number', 'required' => true],
            'is_active' => ['label' => 'Active', 'type' => 'checkbox']
        ];
        break;
    case 'cancellation':
        $table = 'cancellation_policy';
        $page_title = $is_edit ? 'Edit Cancellation Policy' : 'Add New Cancellation Policy';
        $form_fields = [
            'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
            'icon' => ['label' => 'Icon Class', 'type' => 'text', 'required' => true, 'placeholder' => 'fas fa-money-bill-wave'],
            'content' => ['label' => 'Content', 'type' => 'textarea', 'required' => true],
            'section' => ['label' => 'Section', 'type' => 'select', 'options' => [
                'refund' => 'Refund Policy',
                'cancellation' => 'Cancellation Rules',
                'other' => 'Other Policies'
            ]],
            'display_order' => ['label' => 'Display Order', 'type' => 'number', 'required' => true],
            'is_active' => ['label' => 'Active', 'type' => 'checkbox']
        ];
        break;
    case 'info':
        $table = 'house_info_cards';
        $page_title = $is_edit ? 'Edit Info Card' : 'Add New Info Card';
        $form_fields = [
            'icon' => ['label' => 'Icon Class', 'type' => 'text', 'required' => true, 'placeholder' => 'fas fa-wifi'],
            'content' => ['label' => 'Content', 'type' => 'text', 'required' => true],
            'display_order' => ['label' => 'Display Order', 'type' => 'number', 'required' => true],
            'is_active' => ['label' => 'Active', 'type' => 'checkbox']
        ];
        break;
    default:
        // Invalid type
        header("Location: index.php?error=invalid_type");
        exit();
}

// Get item data if editing
if ($is_edit) {
    $item = getSingleRow("SELECT * FROM $table WHERE id = ?", "i", [$id]);
    if (!$item) {
        header("Location: index.php?error=item_not_found");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $data = [];
    $types = '';
    $params = [];
    
    foreach ($form_fields as $field => $config) {
        if ($field === 'is_active') {
            $data[$field] = isset($_POST[$field]) ? 1 : 0;
            $types .= 'i';
            $params[] = $data[$field];
        } else {
            $data[$field] = $_POST[$field] ?? '';
            if ($config['type'] === 'number') {
                $types .= 'i';
                $params[] = (int)$data[$field];
            } else {
                $types .= 's';
                $params[] = $data[$field];
            }
        }
    }
    
    // Prepare query based on mode (add or edit)
    if ($is_edit) {
        // Update existing item
        $query_parts = [];
        foreach (array_keys($data) as $field) {
            $query_parts[] = "$field = ?";
        }
        $query = "UPDATE $table SET " . implode(", ", $query_parts) . " WHERE id = ?";
        $types .= 'i';
        $params[] = $id;
        
        $result = updateData($query, $types, $params);
        $success_message = "Item updated successfully";
    } else {
        // Insert new item
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $query = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        
        $result = insertData($query, $types, $params);
        $success_message = "Item added successfully";
    }
    
    if ($result !== false) {
        header("Location: index.php?success=" . urlencode($success_message));
        exit();
    } else {
        $error_message = "Operation failed. Please try again.";
    }
}

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'House Rules', 'url' => 'index.php'],
    ['title' => $page_title, 'url' => '#']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> | Virunga Homestay Admin</title>
    <!-- Include CSS and JS files -->
    <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Include TinyMCE for rich text editing -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea.rich-editor',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                     alignleft aligncenter alignright alignjustify | \
                     bullist numlist outdent indent | removeformat | help'
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include '../includes/sidebar.php'; ?>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $page_title; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                    </div>
                </div>
                
                <!-- Breadcrumbs -->
                <?php echo generateBreadcrumb($breadcrumbs); ?>
                
                <!-- Error message -->
                <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="">
                            <?php foreach ($form_fields as $field => $config): ?>
                            <div class="mb-3">
                                <label for="<?php echo $field; ?>" class="form-label">
                                    <?php echo $config['label']; ?>
                                    <?php if (isset($config['required']) && $config['required']): ?>
                                    <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                
                                <?php if ($config['type'] === 'text'): ?>
                                <input type="text" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" 
                                       value="<?php echo $is_edit ? htmlspecialchars($item[$field]) : ''; ?>" 
                                       <?php echo isset($config['placeholder']) ? 'placeholder="' . $config['placeholder'] . '"' : ''; ?>
                                       <?php echo isset($config['required']) && $config['required'] ? 'required' : ''; ?>>
                                
                                <?php elseif ($config['type'] === 'textarea'): ?>
                                <textarea class="form-control rich-editor" id="<?php echo $field; ?>" name="<?php echo $field; ?>" 
                                          rows="5" <?php echo isset($config['required']) && $config['required'] ? 'required' : ''; ?>><?php echo $is_edit ? $item[$field] : ''; ?></textarea>
                                
                                <?php elseif ($config['type'] === 'number'): ?>
                                <input type="number" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" 
                                       value="<?php echo $is_edit ? (int)$item[$field] : 0; ?>" min="0" 
                                       <?php echo isset($config['required']) && $config['required'] ? 'required' : ''; ?>>
                                
                                <?php elseif ($config['type'] === 'select'): ?>
                                <select class="form-select" id="<?php echo $field; ?>" name="<?php echo $field; ?>" 
                                        <?php echo isset($config['required']) && $config['required'] ? 'required' : ''; ?>>
                                    <?php foreach ($config['options'] as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $is_edit && $item[$field] === $value ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <?php elseif ($config['type'] === 'checkbox'): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="<?php echo $field; ?>" name="<?php echo $field; ?>" 
                                           <?php echo $is_edit && $item[$field] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?php echo $field; ?>">
                                        Active
                                    </label>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($field === 'icon'): ?>
                                <div class="form-text mt-2">
                                    <p>Preview: <i class="<?php echo $is_edit ? $item[$field] : 'fas fa-info-circle'; ?>"></i></p>
                                    <p>For icon options, visit <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank">Font Awesome</a>.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Include JS files -->
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Icon preview
        $(document).ready(function() {
            $('#icon').on('input', function() {
                const iconClass = $(this).val();
                $(this).next('.form-text').find('i').attr('class', iconClass);
            });
        });
    </script>
</body>
</html>
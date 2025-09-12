<?php
/**
 * Blogs Management - Edit Blog Post
 * Professional admin interface for editing blog posts
 */

// Define admin access and start session
define('ADMIN_ACCESS', true);
session_start();

// Include authentication middleware
require_once '../../backend/api/utils/auth_middleware.php';

// Require authentication
requireAuth();

// Include image helpers
require_once '../../../include/image_helpers.php';

// Include database connection and helpers
require_once '../../backend/database/connection.php';
require_once '../../backend/api/utils/helpers.php';
require_once '../../backend/api/utils/validation.php';
require_once '../../backend/api/utils/image-handler.php';

// Get current user
$current_user = getCurrentUser();

// Get blog ID
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$blog_id) {
    redirectWithMessage('index.php', 'Invalid blog post ID.', 'danger');
}

// Get blog data
$blog = getSingleRow("SELECT * FROM blogs WHERE id = ?", 'i', [$blog_id]);

if (!$blog) {
    redirectWithMessage('index.php', 'Blog post not found.', 'danger');
}

// Initialize variables
$errors = [];
$form_data = [
    'title' => $blog['title'],
    'slug' => $blog['slug'],
    'content' => $blog['content'],
    'is_published' => $blog['is_published']
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $form_data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'is_published' => isset($_POST['is_published']) ? 1 : 0
    ];
    
    // Auto-generate slug if empty
    if (empty($form_data['slug']) && !empty($form_data['title'])) {
        $form_data['slug'] = generateSlug($form_data['title']);
    }
    
    // Validation rules
    $validation_rules = [
        'title' => ['required' => true, 'length' => [3, 255]],
        'slug' => ['required' => true, 'length' => [3, 255]],
        'content' => ['required' => true, 'length' => [10, 50000]]
    ];
    
    // Validate form data
    $validation_result = validateFormData($form_data, $validation_rules);
    
    if (!$validation_result['valid']) {
        $errors = $validation_result['errors'];
    }
    
    // Validate slug format
    if (!empty($form_data['slug']) && !validateSlug($form_data['slug'])['valid']) {
        $errors['slug'] = 'Slug must contain only lowercase letters, numbers, and hyphens.';
    }
    
    // Check if slug already exists (excluding current blog)
    if (!empty($form_data['slug']) && empty($errors['slug'])) {
        $existing_slug = getSingleRow("SELECT id FROM blogs WHERE slug = ? AND id != ?", 'si', [$form_data['slug'], $blog_id]);
        if ($existing_slug) {
            $errors['slug'] = 'This slug is already in use. Please choose a different one.';
        }
    }
    
    // Handle image upload
    $image_path = $blog['image'] ?? ''; // Keep existing image by default, ensure it's a string
    $delete_existing_image = isset($_POST['delete_existing_image']);

    // Debug logging
    error_log('Blog edit - Original blog image: ' . var_export($blog['image'], true));
    error_log('Blog edit - Initial image_path: ' . var_export($image_path, true));
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['image'], 'uploads/blogs/', [
            'max_width' => 1200,
            'max_height' => 800,
            'quality' => 85
        ]);
        
        if ($upload_result['success']) {
            // Delete old image if exists
            if (!empty($blog['image'])) {
                // Construct full path for deletion - handle both old and new formats
                $old_image_path = (strpos($blog['image'], 'uploads/') === 0)
                    ? $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $blog['image']
                    : $_SERVER['DOCUMENT_ROOT'] . '/homestay/uploads/blogs/' . $blog['image'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image_path = $upload_result['filename'];
        } else {
            $errors['image'] = $upload_result['message'];
        }
    } elseif ($delete_existing_image) {
        // Delete existing image
        if (!empty($blog['image'])) {
            $old_image_path = $_SERVER['DOCUMENT_ROOT'] . '/homestay/' . $blog['image'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_path = '';
    }
    

    
    // If no validation errors, update database
    if (empty($errors)) {
        try {
            $query = "UPDATE blogs
                      SET title = ?, slug = ?, content = ?, image = ?, is_published = ?, published_at = ?, updated_at = NOW()
                      WHERE id = ?";

            // Ensure image_path is always a string and not empty if we want to keep existing
            if (empty($image_path) && !$delete_existing_image) {
                // If image_path is empty but we're not deleting, keep the original
                $image_path = $blog['image'] ?? '';
            }
            $image_path = (string)$image_path;

            // Debug logging
            error_log('Blog edit - Final image path value: ' . var_export($image_path, true));
            error_log('Blog edit - All parameters: ' . print_r([
                'title' => $form_data['title'],
                'slug' => $form_data['slug'],
                'content' => substr($form_data['content'], 0, 50) . '...',
                'image' => $image_path,
                'is_published' => $form_data['is_published'],
                'blog_id' => $blog_id
            ], true));

            // Prepare published_at parameter
            $published_at_param = null;
            if ($form_data['is_published']) {
                if (!$blog['is_published']) {
                    // Publishing for the first time
                    $published_at_param = date('Y-m-d H:i:s');
                } else {
                    // Keep existing published_at
                    $published_at_param = $blog['published_at'];
                }
            } else {
                // Unpublishing - set to null
                $published_at_param = null;
            }

            $result = updateData($query, 'ssssisi', [
                $form_data['title'],
                $form_data['slug'],
                $form_data['content'],
                $image_path,
                $form_data['is_published'],
                $published_at_param,
                $blog_id
            ]);

            if ($result !== false) {
                // Log activity (optional - don't fail if logging fails)
                try {
                    logActivity('update_blog', "Updated blog post: {$form_data['title']}");
                } catch (Exception $log_error) {
                    error_log('Activity logging failed: ' . $log_error->getMessage());
                }

                // Redirect with success message
                redirectWithMessage('index.php', 'Blog post updated successfully!', 'success');
            } else {
                $errors['general'] = 'Failed to update blog post. Please try again.';
            }
            
        } catch (Exception $e) {
            error_log('Error updating blog post: ' . $e->getMessage());
            $errors['general'] = 'An error occurred while updating the blog post.';
        }
    }
}

// Breadcrumb data
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => '../dashboard.php'],
    ['title' => 'Blogs', 'url' => 'index.php'],
    ['title' => 'Edit Blog Post', 'url' => '']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post - Virunga Homestay Admin</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/forms.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom Rich Text Editor CSS -->
    <style>
        .custom-editor-container {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .custom-editor-toolbar {
            background: #f5f5f5;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .custom-editor-toolbar button {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .custom-editor-toolbar button:hover {
            background: #f0f0f0;
        }
        .custom-editor-toolbar button.active {
            background: #e6f2ff;
            border-color: #99c2ff;
        }
        .custom-editor-toolbar .toolbar-group {
            display: inline-flex;
            margin-right: 8px;
            border-right: 1px solid #ddd;
            padding-right: 8px;
        }
        .custom-editor-toolbar .toolbar-group:last-child {
            border-right: none;
        }
        .custom-editor-content {
            padding: 15px;
            min-height: 350px;
            max-height: 600px;
            overflow-y: auto;
            background: #fff;
        }
        .custom-editor-content:focus {
            outline: none;
        }
        .custom-editor-content img {
            max-width: 100%;
            height: auto;
        }
        .custom-editor-content table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }
        .custom-editor-content table, .custom-editor-content th, .custom-editor-content td {
            border: 1px solid #ddd;
        }
        .custom-editor-content th, .custom-editor-content td {
            padding: 8px;
            text-align: left;
        }
        .color-picker {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            padding: 5px;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .color-option {
            width: 20px;
            height: 20px;
            display: inline-block;
            margin: 2px;
            cursor: pointer;
            border: 1px solid #ddd;
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
                    <a href="index.php" class="nav-link active">
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
                    <h1 class="page-title">Edit Blog Post</h1>
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
                <!-- Breadcrumb -->
                <?= generateBreadcrumb($breadcrumbs) ?>

                <!-- Form Container -->
                <div class="form-container">
                    <div class="form-header">
                        <h2 class="form-title">Edit Blog Post</h2>
                        <p class="form-subtitle">Update blog post information</p>
                    </div>

                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data" data-validate="true">
                        <div class="form-row">
                            <div class="form-col-8">
                                <div class="form-group">
                                    <label for="title" class="form-label required">Blog Title</label>
                                    <input 
                                        type="text" 
                                        id="title" 
                                        name="title" 
                                        class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['title']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="Enter blog post title"
                                    >
                                    <?php if (isset($errors['title'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-col-4">
                                <div class="form-group">
                                    <label for="slug" class="form-label required">URL Slug</label>
                                    <input 
                                        type="text" 
                                        id="slug" 
                                        name="slug" 
                                        class="form-control <?= isset($errors['slug']) ? 'is-invalid' : '' ?>" 
                                        value="<?= htmlspecialchars($form_data['slug']) ?>"
                                        required
                                        data-min-length="3"
                                        data-max-length="255"
                                        placeholder="url-friendly-slug"
                                        pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
                                        data-auto-generate="false"
                                    >
                                    <small class="form-text text-muted">URL-friendly version of the title</small>
                                    <?php if (isset($errors['slug'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['slug']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label required">Blog Content</label>
                            <textarea
                                id="content"
                                name="content"
                                class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>"
                                data-min-length="10"
                                data-max-length="50000"
                                style="height: 400px;"
                            ><?= htmlspecialchars($form_data['content']) ?></textarea>
                            <?php if (isset($errors['content'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['content']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">Featured Image</label>
                            
                            <?php if (!empty($blog['image'])): ?>
                                <div class="existing-image-preview" style="margin-bottom: 15px;">
                                    <img src="<?= buildAdminImageUrl($blog['image'], 'blogs') ?>"
                                         alt="Current image"
                                         style="max-width: 200px; max-height: 150px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="margin-top: 10px;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                                            <input type="checkbox" name="delete_existing_image" value="1">
                                            Delete current image
                                        </label>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="file-upload-container">
                                <input 
                                    type="file" 
                                    id="image" 
                                    name="image" 
                                    class="file-upload-input" 
                                    accept="image/*"
                                >
                                <label for="image" class="file-upload-label">
                                    <div class="file-upload-content">
                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                        <div class="file-upload-text">
                                            <strong>Click to upload new image</strong> or drag and drop<br>
                                            <small>PNG, JPG, GIF up to 20MB</small>
                                        </div>
                                    </div>
                                </label>
                                <div class="image-preview"></div>
                            </div>
                            <?php if (isset($errors['image'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    id="is_published" 
                                    name="is_published" 
                                    class="form-check-input" 
                                    <?= $form_data['is_published'] ? 'checked' : '' ?>
                                >
                                <label for="is_published" class="form-check-label">
                                    Published (visible on website)
                                </label>
                            </div>
                            <?php if ($blog['published_at']): ?>
                                <small class="form-text text-muted">
                                    Originally published: <?= formatDateTime($blog['published_at']) ?>
                                </small>
                            <?php endif; ?>
                        </div>

                        <div class="form-actions">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Blog Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- JavaScript Files -->
    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/forms.js"></script>
    <script src="../../assets/js/image-upload.js"></script>
    
    <script>
        // Create custom rich text editor
        document.addEventListener('DOMContentLoaded', function() {
            // Get the textarea element
            const textarea = document.getElementById('content');
            if (!textarea) return;
            
            // Create editor container
            const editorContainer = document.createElement('div');
            editorContainer.className = 'custom-editor-container';
            
            // Create toolbar
            const toolbar = document.createElement('div');
            toolbar.className = 'custom-editor-toolbar';
            
            // Create editable content area
            const editorContent = document.createElement('div');
            editorContent.className = 'custom-editor-content';
            editorContent.contentEditable = true;
            editorContent.innerHTML = textarea.value;
            
            // Hide the original textarea
            textarea.style.display = 'none';
            
            // Add toolbar buttons
            const toolbarHTML = `
                <div class="toolbar-group">
                    <button type="button" data-command="undo" title="Undo"><i class="fas fa-undo"></i></button>
                    <button type="button" data-command="redo" title="Redo"><i class="fas fa-redo"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                    <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                    <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                    <button type="button" data-command="strikeThrough" title="Strike through"><i class="fas fa-strikethrough"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="foreColor" title="Text color" class="color-btn"><i class="fas fa-palette"></i></button>
                    <div class="color-picker">
                        <div class="color-option" style="background-color: #000000" data-color="#000000"></div>
                        <div class="color-option" style="background-color: #e60000" data-color="#e60000"></div>
                        <div class="color-option" style="background-color: #ff9900" data-color="#ff9900"></div>
                        <div class="color-option" style="background-color: #ffff00" data-color="#ffff00"></div>
                        <div class="color-option" style="background-color: #008a00" data-color="#008a00"></div>
                        <div class="color-option" style="background-color: #0066cc" data-color="#0066cc"></div>
                        <div class="color-option" style="background-color: #9933ff" data-color="#9933ff"></div>
                        <div class="color-option" style="background-color: #ffffff" data-color="#ffffff"></div>
                        <div class="color-option" style="background-color: #facccc" data-color="#facccc"></div>
                        <div class="color-option" style="background-color: #ffebcc" data-color="#ffebcc"></div>
                        <div class="color-option" style="background-color: #ffffcc" data-color="#ffffcc"></div>
                        <div class="color-option" style="background-color: #cce8cc" data-color="#cce8cc"></div>
                        <div class="color-option" style="background-color: #cce0f5" data-color="#cce0f5"></div>
                        <div class="color-option" style="background-color: #ebd6ff" data-color="#ebd6ff"></div>
                    </div>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="justifyLeft" title="Align left"><i class="fas fa-align-left"></i></button>
                    <button type="button" data-command="justifyCenter" title="Align center"><i class="fas fa-align-center"></i></button>
                    <button type="button" data-command="justifyRight" title="Align right"><i class="fas fa-align-right"></i></button>
                    <button type="button" data-command="justifyFull" title="Justify"><i class="fas fa-align-justify"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="insertUnorderedList" title="Bullet list"><i class="fas fa-list-ul"></i></button>
                    <button type="button" data-command="insertOrderedList" title="Numbered list"><i class="fas fa-list-ol"></i></button>
                    <button type="button" data-command="outdent" title="Decrease indent"><i class="fas fa-outdent"></i></button>
                    <button type="button" data-command="indent" title="Increase indent"><i class="fas fa-indent"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="createLink" title="Insert link"><i class="fas fa-link"></i></button>
                    <button type="button" data-command="unlink" title="Remove link"><i class="fas fa-unlink"></i></button>
                    <button type="button" data-command="insertImage" title="Insert image"><i class="fas fa-image"></i></button>
                </div>
                <div class="toolbar-group">
                    <button type="button" data-command="removeFormat" title="Clear formatting"><i class="fas fa-eraser"></i></button>
                    <button type="button" data-command="insertTable" title="Insert table"><i class="fas fa-table"></i></button>
                </div>
            `;
            
            toolbar.innerHTML = toolbarHTML;
            
            // Add elements to the DOM
            editorContainer.appendChild(toolbar);
            editorContainer.appendChild(editorContent);
            textarea.parentNode.insertBefore(editorContainer, textarea);
            
            // Handle toolbar button clicks
            toolbar.querySelectorAll('button[data-command]').forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.getAttribute('data-command');
                    
                    if (command === 'createLink') {
                        const url = prompt('Enter the link URL:', 'https://');
                        if (url) {
                            document.execCommand('createLink', false, url);
                        }
                    } else if (command === 'insertImage') {
                        // Create a file input
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.accept = 'image/*';
                        input.click();
                        
                        input.onchange = function() {
                            const file = this.files[0];
                            if (file) {
                                // Upload the image
                                const formData = new FormData();
                                formData.append('file', file);
                                
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', '../../backend/api/utils/custom-image-upload.php', true);
                                xhr.onload = function() {
                                    if (xhr.status === 200) {
                                        try {
                                            const response = JSON.parse(xhr.responseText);
                                            if (response.location) {
                                                document.execCommand('insertImage', false, response.location);
                                            }
                                        } catch (e) {
                                            console.error('Error parsing JSON response:', e);
                                        }
                                    }
                                };
                                xhr.send(formData);
                            }
                        };
                    } else if (command === 'insertTable') {
                        const rows = prompt('Enter number of rows:', '3');
                        const cols = prompt('Enter number of columns:', '3');
                        
                        if (rows && cols) {
                            let tableHTML = '<table>';
                            for (let i = 0; i < parseInt(rows); i++) {
                                tableHTML += '<tr>';
                                for (let j = 0; j < parseInt(cols); j++) {
                                    tableHTML += '<td>Cell</td>';
                                }
                                tableHTML += '</tr>';
                            }
                            tableHTML += '</table>';
                            
                            document.execCommand('insertHTML', false, tableHTML);
                        }
                    } else if (command === 'foreColor') {
                        // Toggle color picker
                        const colorPicker = document.querySelector('.color-picker');
                        colorPicker.style.display = colorPicker.style.display === 'none' ? 'block' : 'none';
                        
                        // Position the color picker below the button
                        const buttonRect = this.getBoundingClientRect();
                        colorPicker.style.top = (buttonRect.bottom + window.scrollY) + 'px';
                        colorPicker.style.left = (buttonRect.left + window.scrollX) + 'px';
                    } else {
                        document.execCommand(command, false, null);
                    }
                    
                    // Update textarea value
                    textarea.value = editorContent.innerHTML;
                });
            });
            
            // Handle color picker clicks
            document.querySelectorAll('.color-option').forEach(option => {
                option.addEventListener('click', function() {
                    const color = this.getAttribute('data-color');
                    document.execCommand('foreColor', false, color);
                    document.querySelector('.color-picker').style.display = 'none';
                    textarea.value = editorContent.innerHTML;
                });
            });
            
            // Update textarea when content changes
            editorContent.addEventListener('input', function() {
                textarea.value = this.innerHTML;
            });
            
            // Close color picker when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.color-btn') && !e.target.closest('.color-picker')) {
                    document.querySelector('.color-picker').style.display = 'none';
                }
            });
        });

        // Auto-generate slug from title (only if not manually edited)
        document.getElementById('title').addEventListener('input', function() {
            const slugField = document.getElementById('slug');
            if (slugField.dataset.autoGenerate !== 'false') {
                const slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugField.value = slug;
            }
        });

        // Mark slug as manually edited
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.autoGenerate = 'false';
        });

        // Handle form submission with custom editor
        document.querySelector('form[data-validate="true"]').addEventListener('submit', function(e) {
            // Content is already synced with textarea through event listeners
            
            // Basic validation
            const title = document.getElementById('title').value.trim();
            const content = document.getElementById('content').value;
            
            let hasErrors = false;
            
            // Validate title
            if (!title) {
                hasErrors = true;
                document.getElementById('title').classList.add('is-invalid');
                document.getElementById('title').focus();
            } else {
                document.getElementById('title').classList.remove('is-invalid');
            }
            
            // Validate content
            if (!content || content.replace(/<[^>]*>/g, '').trim().length < 10) {
                hasErrors = true;
                document.getElementById('content').classList.add('is-invalid');
                if (!hasErrors) {
                    // Focus the editor content div
                    document.querySelector('.custom-editor-content').focus();
                }
            } else {
                document.getElementById('content').classList.remove('is-invalid');
            }
            
            if (hasErrors) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            }
        });
    </script>
</body>
</html>

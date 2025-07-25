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
    
    <!-- TinyMCE Rich Text Editor -->
    <script src="https://cdn.tiny.cloud/1/9zeungj09xa5xpcvfrp4jhl82awjs7se0w9p11lk9bfim0fx/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
        // Initialize TinyMCE Rich Text Editor
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'image link | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
            branding: false,
            promotion: false,
            // Image upload configuration
            images_upload_url: '../../backend/api/utils/tinymce-image-upload.php',
            images_upload_base_path: '../../',
            images_upload_credentials: true,
            automatic_uploads: true,
            file_picker_types: 'image',
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.onchange = function() {
                        var file = this.files[0];
                        if (file) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);
                                callback(blobInfo.blobUri(), { title: file.name });
                            };
                            reader.readAsDataURL(file);
                        }
                    };
                    input.click();
                }
            },
            // Image upload handler
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '../../backend/api/utils/tinymce-image-upload.php');

                    xhr.upload.onprogress = function (e) {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = function() {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        var json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }

                        resolve(json.location);
                    };

                    xhr.onerror = function () {
                        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                    };

                    var formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                });
            }
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

        // Handle form submission - ensure TinyMCE content is synced
        document.querySelector('form[data-validate="true"]').addEventListener('submit', function(e) {
            // Sync TinyMCE content to textarea
            if (tinymce.get('content')) {
                tinymce.get('content').save();
            }

            // Basic validation
            const title = document.getElementById('title').value.trim();
            const content = tinymce.get('content') ? tinymce.get('content').getContent() : '';

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
                if (!hasErrors) tinymce.get('content').focus();
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

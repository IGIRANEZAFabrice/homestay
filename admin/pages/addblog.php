<?php
require_once '../../include/connection.php';

// Initialize variables to avoid undefined warnings
$title = '';
$subtitle = '';
$image = '';
$success = '';
$error = '';

function save_uploaded_image($file) {
    $targetDir = '../../uploads/blogs/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return false;
    $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetFile = $targetDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return 'uploads/blogs/' . $filename;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $title = trim($_POST['blogTitle'] ?? '');
    $subtitle = trim($_POST['blogSubtitle'] ?? ''); // We'll use this as excerpt
    $excerpt = trim($_POST['blogExcerpt'] ?? $subtitle); // Use subtitle as excerpt if no excerpt provided
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    $image = '';
    
    // Debug logging
    error_log("POST data received: " . print_r($_POST, true));
    error_log("FILES data received: " . print_r($_FILES, true));
    
    if (isset($_FILES['featuredImage']) && $_FILES['featuredImage']['error'] === UPLOAD_ERR_OK) {
        $image = save_uploaded_image($_FILES['featuredImage']);
        error_log("Featured image saved: " . $image);
    }
    
    // Parse content blocks from form data
    $contentBlocks = [];
    $index = 0;
    while (isset($_POST["contentBlocks"][$index]["type"])) {
        $type = $_POST["contentBlocks"][$index]["type"];
        $content = '';
        
        if ($type === 'text') {
            $content = trim($_POST["contentBlocks"][$index]["content"] ?? '');
        } else if ($type === 'image') {
            // Handle image file upload
            if (isset($_FILES["contentBlocks"]["name"][$index]["file"]) && 
                $_FILES["contentBlocks"]["error"][$index]["file"] === UPLOAD_ERR_OK) {
                // Create a temporary file array for the upload function
                $fileArray = [
                    'name' => $_FILES["contentBlocks"]["name"][$index]["file"],
                    'tmp_name' => $_FILES["contentBlocks"]["tmp_name"][$index]["file"],
                    'error' => $_FILES["contentBlocks"]["error"][$index]["file"],
                    'size' => $_FILES["contentBlocks"]["size"][$index]["file"]
                ];
                $content = save_uploaded_image($fileArray);
            }
        }
        
        if ($content) {
            $contentBlocks[] = [
                'type' => $type,
                'content' => $content
            ];
        }
        $index++;
    }
    
    error_log("Content blocks: " . print_r($contentBlocks, true));
    
    if ($title && $image && count($contentBlocks) > 0) {
        try {
            // Combine content blocks into a single content field
            $combinedContent = '';
            foreach ($contentBlocks as $block) {
                if ($block['type'] === 'text') {
                    $combinedContent .= '<p>' . htmlspecialchars($block['content']) . '</p>';
                } else if ($block['type'] === 'image') {
                    $combinedContent .= '<img src="' . htmlspecialchars($block['content']) . '" alt="Blog image" style="max-width: 100%; height: auto; margin: 20px 0;">';
                }
            }
            
            // Insert blog post with content field
            $stmt = $conn->prepare("INSERT INTO blogs (title, image, content, slug, is_published, published_at) VALUES (?, ?, ?, ?, 1, NOW())");
            $stmt->bind_param('ssss', $title, $image, $combinedContent, $slug);
            
            if ($stmt->execute()) {
                $blog_id = $stmt->insert_id;
                error_log("Blog saved with ID: " . $blog_id);
                $stmt->close();
                
                // Also save content blocks to blog_content_blocks table for future flexibility
                $order = 1;
                foreach ($contentBlocks as $block) {
                    $type = $block['type'];
                    $content = $block['content'];
                    
                    if ($type === 'text') {
                        $stmt2 = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_text, display_order) VALUES (?, ?, ?, ?)");
                        $stmt2->bind_param('issi', $blog_id, $type, $content, $order);
                    } else if ($type === 'image') {
                        $stmt2 = $conn->prepare("INSERT INTO blog_content_blocks (blog_id, content_type, content_image, display_order) VALUES (?, ?, ?, ?)");
                        $stmt2->bind_param('issi', $blog_id, $type, $content, $order);
                    }
                    
                    if (isset($stmt2)) {
                        $stmt2->execute();
                        error_log("Content block saved: " . $type . " - " . $content);
                        $stmt2->close();
                    }
                    $order++;
                }
                
                $success = 'Blog post saved successfully!';
                // Redirect after a short delay to show success message
                header("refresh:2;url=blogs.php");
            } else {
                $error = 'Failed to save blog post: ' . $stmt->error;
                error_log("Database error: " . $error);
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
            error_log("Exception: " . $error);
        }
    } else {
        $error = 'Please fill in all required fields and add at least one content section.';
        error_log("Validation error: " . $error);
        error_log("Title: '$title', Image: '$image', Content blocks: " . count($contentBlocks));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Blog - Admin</title>
    
    <link rel="stylesheet" href="../css/blog-admin-modern.css">
    <link rel="stylesheet" href="../css/blog-form.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    <div class="admin-container" style="margin-left: 290px; width: calc(100% - 280px);">
        <h1>Create New Blog Post</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form id="blogForm" class="blog-form" method="post" enctype="multipart/form-data" action="">
            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                    <span class="section-hint">Start with the essentials</span>
                </div>
                
                <div class="form-group">
                    <label for="blogTitle">Blog Title</label>
                    <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter an engaging title" required>
                </div>
                
                <div class="form-group">
                    <label for="blogSubtitle">Subtitle</label>
                    <input type="text" id="blogSubtitle" name="blogSubtitle" value="<?php echo htmlspecialchars($subtitle); ?>" placeholder="Add a descriptive subtitle" required>
                </div>
                
                <div class="form-group">
                    <label for="featuredImage">Featured Image</label>
                    <div class="file-input-container">
                        <label for="featuredImage" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i> 
                            <span id="fileInputText">Choose a featured image</span>
                        </label>
                        <input type="file" id="featuredImage" name="featuredImage" accept="image/*" class="file-input" required>
                    </div>
                    <div class="image-preview" id="featuredImagePreview"></div>
                    <div class="form-hint">Recommended size: 1200 x 630 pixels (16:9 ratio)</div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-edit"></i> Blog Content</h2>
                    <span class="section-hint">Build your story with text and images</span>
                </div>
                
                <div id="contentSections" class="content-sections">
                    <!-- Content sections will be added here -->
                </div>
                
                <div class="content-tools">
                    <button type="button" class="btn-tool" onclick="addContentSection('text')">
                        <i class="fas fa-paragraph"></i> Add Text Block
                    </button>
                    <button type="button" class="btn-tool" onclick="addContentSection('image')">
                        <i class="fas fa-image"></i> Add Image Block
                    </button>
                </div>
                
                <div class="empty-state" id="emptyContentState">
                    <div class="empty-state-icon">
                        <i class="fas fa-feather-alt"></i>
                    </div>
                    <h3>Start Creating Your Blog</h3>
                    <p>Use the buttons above to add text and image blocks to your blog post.</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Publish Blog
                </button>
                <button type="button" class="btn-secondary" onclick="previewBlog()">
                    <i class="fas fa-eye"></i> Preview
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="modal">
        <div class="modal-content preview-content">
            <div class="modal-header">
                <h2><i class="fas fa-eye"></i> Blog Preview</h2>
                <button class="close-modal" onclick="closePreviewModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <article class="blog-preview">
                    <h1 id="previewTitle"></h1>
                    <h2 id="previewSubtitle"></h2>
                    <div id="previewContent"></div>
                </article>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closePreviewModal()">
                    <i class="fas fa-arrow-left"></i> Back to Editing
                </button>
            </div>
        </div>
    </div>

    <script>
        let contentSections = [];

        // Function to add a new content section
        function addContentSection(type) {
            const section = {
                id: Date.now() + Math.random(), // Ensure unique ID
                type: type
            };
            contentSections.push(section);
            renderContentSections();
            
            // Hide empty state if it's visible
            const emptyState = document.getElementById('emptyContentState');
            if (emptyState) {
                emptyState.style.display = 'none';
            }
        }

        // Function to remove a content section
        function removeContentSection(id) {
            contentSections = contentSections.filter(section => section.id !== id);
            renderContentSections();
            
            // Show empty state if no sections left
            if (contentSections.length === 0) {
                const emptyState = document.getElementById('emptyContentState');
                if (emptyState) {
                    emptyState.style.display = 'block';
                }
            }
        }

        // Function to move a section up
        function moveSectionUp(id) {
            const index = contentSections.findIndex(section => section.id === id);
            if (index > 0) {
                const temp = contentSections[index];
                contentSections[index] = contentSections[index - 1];
                contentSections[index - 1] = temp;
                renderContentSections();
            }
        }

        // Function to move a section down
        function moveSectionDown(id) {
            const index = contentSections.findIndex(section => section.id === id);
            if (index < contentSections.length - 1) {
                const temp = contentSections[index];
                contentSections[index] = contentSections[index + 1];
                contentSections[index - 1] = temp;
                renderContentSections();
            }
        }

        // Function to render all content sections
        function renderContentSections() {
            const container = document.getElementById('contentSections');
            container.innerHTML = '';
            
            // Show/hide empty state based on content sections
            const emptyState = document.getElementById('emptyContentState');
            if (emptyState) {
                emptyState.style.display = contentSections.length === 0 ? 'block' : 'none';
            }
            
            contentSections.forEach((section, index) => {
                const sectionElement = document.createElement('div');
                sectionElement.className = 'content-section';
                sectionElement.innerHTML = `
                    <div class="section-header">
                        <span class="section-type">
                            <i class="fas fa-${section.type === 'text' ? 'paragraph' : 'image'}"></i>
                            ${section.type === 'text' ? 'Text' : 'Image'} Block
                        </span>
                        <div class="section-actions">
                            <button type="button" class="btn-move" title="Move Up" onclick="moveSectionUp(${section.id})" ${index === 0 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="btn-move" title="Move Down" onclick="moveSectionDown(${section.id})" ${index === contentSections.length - 1 ? 'disabled' : ''}>
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="btn-delete" title="Remove" onclick="removeContentSection(${section.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="section-content">
                        ${section.type === 'text' 
                            ? `<textarea name="content_${section.id}" rows="4" placeholder="Enter your text here...">${section.content || ''}</textarea>`
                            : `<div class="image-input">
                                <div class="file-input-container">
                                    <label for="image_${section.id}" class="file-input-label">
                                        <i class="fas fa-cloud-upload-alt"></i> 
                                        <span>${section.preview ? 'Change image' : 'Choose an image'}</span>
                                    </label>
                                    <input type="file" id="image_${section.id}" name="image_${section.id}" accept="image/*" class="file-input">
                                </div>
                                <div class="image-preview" id="preview_${section.id}">${section.preview || ''}</div>
                                <input type="text" name="imageCaption_${section.id}" placeholder="Image caption (optional)" value="${section.caption || ''}" class="image-caption-input">
                                <div class="form-hint">Add a descriptive caption to explain the image</div>
                               </div>`
                        }
                    </div>
                `;
                container.appendChild(sectionElement);

                // Add image preview functionality for image sections
                if (section.type === 'image') {
                    const imageInput = sectionElement.querySelector(`input[name="image_${section.id}"]`);
                    const preview = sectionElement.querySelector(`#preview_${section.id}`);
                    
                    imageInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                                // Store the preview for later rendering
                                section.preview = preview.innerHTML;
                                
                                // Update file input label text
                                const label = imageInput.previousElementSibling;
                                const span = label.querySelector('span');
                                span.textContent = 'Change image';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.innerHTML = '';
                            section.preview = '';
                            
                            // Reset file input label text
                            const label = imageInput.previousElementSibling;
                            const span = label.querySelector('span');
                            span.textContent = 'Choose an image';
                        }
                    });
                } else if (section.type === 'text') {
                    // Add event listener to preserve text content
                    const textarea = sectionElement.querySelector(`textarea[name="content_${section.id}"]`);
                    textarea.addEventListener('input', function(e) {
                        section.content = e.target.value;
                    });
                }
            });
        }

        // Function to preview image
        function previewImage(input, previewElement) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewElement.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    previewElement.classList.add('has-image');
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewElement.innerHTML = '';
                previewElement.classList.remove('has-image');
            }
        }
        
        // Add event listener for featured image preview
        document.getElementById('featuredImage').addEventListener('change', function() {
            const preview = document.getElementById('featuredImagePreview');
            previewImage(this, preview);
            
            // Update file input label text
            const fileInputText = document.getElementById('fileInputText');
            if (this.files.length > 0) {
                fileInputText.textContent = this.files[0].name;
            } else {
                fileInputText.textContent = 'Choose a featured image';
            }
        });

        // Preview functionality
        function previewBlog() {
            const title = document.getElementById('blogTitle').value;
            const subtitle = document.getElementById('blogSubtitle').value;
            const featuredImageFile = document.getElementById('featuredImage').files[0];

            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewSubtitle').textContent = subtitle;
            
            const previewContent = document.getElementById('previewContent');
            let featuredImageHtml = '';
            
            if (featuredImageFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    featuredImageHtml = `<img src="${e.target.result}" alt="${title}" class="preview-featured-image">`;
                    updatePreviewContent();
                };
                reader.readAsDataURL(featuredImageFile);
            } else {
                updatePreviewContent();
            }
            
            function updatePreviewContent() {
                const contentHtml = contentSections.map(section => {
                    const element = document.querySelector(`[name="${section.type}_${section.id}"]`);
                    if (!element) return ''; // Skip if element doesn't exist
                    
                    if (section.type === 'text') {
                        return `<p>${element.value}</p>`;
                    } else {
                        return `<img src="${element.value}" alt="Blog image" class="preview-content-image">`;
                    }
                }).join('');
                
                previewContent.innerHTML = featuredImageHtml + contentHtml;
            }

            document.getElementById('previewModal').style.display = 'flex';
        }

        function closePreviewModal() {
            document.getElementById('previewModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('previewModal');
            if (event.target === modal) {
                closePreviewModal();
            }
        }

        // Form submission validation
        document.getElementById('blogForm').addEventListener('submit', function(e) {
            // Validate required fields
            const title = document.getElementById('blogTitle').value.trim();
            const subtitle = document.getElementById('blogSubtitle').value.trim();
            const featuredImageFile = document.getElementById('featuredImage').files[0];
            
            if (!title || !subtitle || !featuredImageFile) {
                e.preventDefault();
                alert('Please fill in all required fields: Title, Subtitle, and Featured Image');
                return false;
            }
            
            if (contentSections.length === 0) {
                e.preventDefault();
                alert('Please add at least one content section (text or image)');
                return false;
            }
            
            // Create hidden fields for content blocks
            let hasContent = false;
            contentSections.forEach((section, index) => {
                if (section.type === 'text') {
                    const textarea = document.querySelector(`textarea[name="content_${section.id}"]`);
                    if (textarea && textarea.value.trim()) {
                        // Create hidden input for text content
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `contentBlocks[${index}][type]`;
                        hiddenInput.value = 'text';
                        this.appendChild(hiddenInput);
                        
                        const contentInput = document.createElement('input');
                        contentInput.type = 'hidden';
                        contentInput.name = `contentBlocks[${index}][content]`;
                        contentInput.value = textarea.value.trim();
                        this.appendChild(contentInput);
                        
                        hasContent = true;
                    }
                } else if (section.type === 'image') {
                    const fileInput = document.querySelector(`input[name="image_${section.id}"]`);
                    if (fileInput && fileInput.files[0]) {
                        // Create hidden input for image type
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `contentBlocks[${index}][type]`;
                        hiddenInput.value = 'image';
                        this.appendChild(hiddenInput);
                        
                        // Rename the file input to match expected structure
                        fileInput.name = `contentBlocks[${index}][file]`;
                        hasContent = true;
                    }
                }
            });
            
            if (!hasContent) {
                e.preventDefault();
                alert('Please add content to at least one section');
                return false;
            }
            
            // Form is valid, allow submission
            return true;
        });
        
        // Ensure sidebar is properly initialized
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('adminSidebar');
            if (sidebar) {
                // Make sure sidebar is visible and not small by default
                sidebar.classList.remove('hide');
                sidebar.classList.remove('small');
                console.log('Addblog.php: Ensuring sidebar is fully visible');
                
                // Ensure admin container has correct margin and z-index
                const adminContainer = document.querySelector('.admin-container');
                if (adminContainer) {
                    adminContainer.style.marginLeft = '260px';
                    adminContainer.style.width = 'calc(100% - 280px)';
                    adminContainer.style.zIndex = '1001';
                    adminContainer.style.position = 'relative';
                    console.log('Addblog.php: Adjusting admin container margin and z-index');
                }
            }
        });
        
        // Force the admin container to have the correct margin, width, and z-index
        // This ensures it doesn't appear behind the sidebar
        window.addEventListener('load', function() {
            const adminContainer = document.querySelector('.admin-container');
            if (adminContainer) {
                adminContainer.style.marginLeft = '260px';
                adminContainer.style.width = 'calc(100% - 280px)';
                adminContainer.style.zIndex = '1001';
                adminContainer.style.position = 'relative';
                console.log('Addblog.php load event: Setting admin container z-index and position');
            }
        });
    </script>
</body>
</html>

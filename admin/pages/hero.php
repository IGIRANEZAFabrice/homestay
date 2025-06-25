<?php
require_once '../../include/connection.php';

// Function to handle file upload
function uploadImage($file) {
    $target_dir = "../../uploads/hero/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["error" => "File is not an image."];
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "path" => "uploads/hero/" . $fileName];
    } else {
        return ["error" => "Sorry, there was an error uploading your file."];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'update') {
            $id = $_POST['id'];
            $title = $_POST['title'];
            $paragraph = $_POST['subtitle'];
            $display_order = $_POST['display_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if (!empty($_FILES['image']['name'])) {
                $upload = uploadImage($_FILES['image']);
                if (isset($upload['success'])) {
                    $image = $upload['path'];
                    $sql = "UPDATE hero_images SET title=?, paragraph=?, image=?, display_order=?, is_active=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssiii", $title, $paragraph, $image, $display_order, $is_active, $id);
                } else {
                    echo json_encode(['error' => $upload['error']]);
                    exit;
                }
            } else {
                $sql = "UPDATE hero_images SET title=?, paragraph=?, display_order=?, is_active=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiii", $title, $paragraph, $display_order, $is_active, $id);
            }
            
        } else if ($_POST['action'] == 'create') {
            if (empty($_FILES['image']['name'])) {
                echo json_encode(['error' => 'Image is required for new slides']);
                exit;
            }
            
            $upload = uploadImage($_FILES['image']);
            if (isset($upload['success'])) {
                $image = $upload['path'];
                $title = $_POST['title'];
                $paragraph = $_POST['subtitle'];
                $display_order = $_POST['display_order'];
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $sql = "INSERT INTO hero_images (title, paragraph, image, display_order, is_active) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssii", $title, $paragraph, $image, $display_order, $is_active);
            } else {
                echo json_encode(['error' => $upload['error']]);
                exit;
            }
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
            exit;
        }
    }
}

// Fetch or create 3 hero slides
$slides = [];
$sql = "SELECT * FROM hero_images ORDER BY display_order ASC LIMIT 3";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $slides[] = $row;
}
// If less than 3, fill with empty placeholders
for ($i = count($slides); $i < 3; $i++) {
    $slides[] = [
        'id' => '',
        'title' => '',
        'paragraph' => '',
        'image' => '',
        'display_order' => $i+1,
        'is_active' => 1
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Section - Admin</title>
    <link rel="stylesheet" href="../../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1>Manage Hero Section</h1>
        </div>
        <div class="hero-slides-container">
            <?php foreach ($slides as $i => $slide): ?>
            <div class="hero-slide-card" data-id="<?php echo $slide['id']; ?>">
                <h2>Hero Slide #<?php echo $i+1; ?></h2>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" class="hero-image-input" accept="image/*" <?php echo $slide['image'] ? '' : 'required'; ?>>
                    <div class="image-preview" style="background-image: url('../../<?php echo $slide['image']; ?>');"></div>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" class="hero-title" value="<?php echo htmlspecialchars($slide['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" class="hero-subtitle" value="<?php echo htmlspecialchars($slide['paragraph']); ?>" required>
                </div>
                <input type="hidden" class="hero-order" value="<?php echo $i+1; ?>">
                <input type="hidden" class="hero-active" value="1">
            </div>
            <?php endforeach; ?>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" onclick="saveAllChanges()">
                <i class="fas fa-save"></i> Save All Changes
            </button>
            <button class="btn btn-secondary" onclick="previewHeroSection()">
                <i class="fas fa-eye"></i> Preview
            </button>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal" id="previewModal">
        <div class="modal-content preview-content">
            <div class="modal-header">
                <h2>Hero Section Preview</h2>
                <button class="close-btn" onclick="closePreviewModal()">&times;</button>
            </div>
            <div class="hero-preview">
                <div class="hero-slider">
                    <?php foreach ($slides as $index => $slide): ?>
                    <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="../../<?php echo $slide['image']; ?>" alt="Hero Preview">
                        <div class="hero-content">
                            <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                            <p><?php echo htmlspecialchars($slide['paragraph']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="hero-slider-controls">
                    <button class="slider-control prev" onclick="prevSlide()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="slider-dots">
                        <?php for($i = 0; $i < count($slides); $i++): ?>
                        <button class="slider-dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></button>
                        <?php endfor; ?>
                    </div>
                    <button class="slider-control next" onclick="nextSlide()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function saveAllChanges() {
        const slides = document.querySelectorAll('.hero-slide-card');
        const promises = [];

        slides.forEach(slide => {
            const formData = new FormData();
            const id = slide.dataset.id;
            const imageInput = slide.querySelector('.hero-image-input');
            const isNew = !id;

            formData.append('action', isNew ? 'create' : 'update');
            if (!isNew) formData.append('id', id);
            formData.append('title', slide.querySelector('.hero-title').value);
            formData.append('subtitle', slide.querySelector('.hero-subtitle').value);
            formData.append('display_order', slide.querySelector('.hero-order').value);
            formData.append('is_active', slide.querySelector('.hero-active').checked ? '1' : '0');
            
            if (imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }

            promises.push(
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                }).then(response => response.json())
            );
        });

        Promise.all(promises)
            .then(results => {
                const errors = results.filter(r => r.error);
                if (errors.length) {
                    alert('Some errors occurred: ' + errors.map(e => e.error).join('\n'));
                } else {
                    alert('All changes saved successfully!');
                    location.reload();
                }
            })
            .catch(error => {
                alert('Error saving changes: ' + error);
            });
    }

    // Preview image when file is selected
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('hero-image-input')) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = e.target.closest('.form-group').querySelector('.image-preview');
                    preview.style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Existing slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');

    function updateSlider() {
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlider();
    }

    function prevSlide() {
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        updateSlider();
    }

    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
    }

    // Auto-advance slides in preview
    let slideInterval = setInterval(nextSlide, 5000);

    // Pause auto-advance when hovering over slider
    document.querySelector('.hero-slider')?.addEventListener('mouseenter', () => {
        clearInterval(slideInterval);
    });

    document.querySelector('.hero-slider')?.addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 5000);
    });
    </script>
</body>
</html>

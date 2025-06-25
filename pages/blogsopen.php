<?php
require_once '../include/connection.php';

// Get the blog slug from URL parameter
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    // Redirect to blog list if no slug provided
    header('Location: blog.php');
    exit;
}

// Fetch the specific blog post
$stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ? AND is_published = 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();
$stmt->close();

if (!$blog) {
    // Blog not found, redirect to blog list
    header('Location: blog.php');
    exit;
}

// Fetch all content blocks for this blog in display order
$stmt = $conn->prepare("SELECT * FROM blog_content_blocks WHERE blog_id = ? ORDER BY display_order ASC");
$stmt->bind_param('i', $blog['id']);
$stmt->execute();
$contentBlocksResult = $stmt->get_result();
$contentBlocks = [];
while ($block = $contentBlocksResult->fetch_assoc()) {
    $contentBlocks[] = $block;
}
$stmt->close();

// Debug: Log what we found
error_log("Blog ID: " . $blog['id']);
error_log("Content blocks found: " . count($contentBlocks));
foreach ($contentBlocks as $index => $block) {
    error_log("Block $index: " . json_encode($block));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($blog['title']); ?> | Virunga Homestay</title>
  <link rel="stylesheet" href="../css/blogs.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    .blogpost-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 20px;
      background: #fff;
    }
    .blog-post {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .blog-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 40px 30px;
      text-align: center;
    }
    .blog-title {
      font-size: 2.8rem;
      margin: 0 0 15px 0;
      font-weight: 600;
      line-height: 1.2;
    }
    .blog-meta {
      font-size: 1rem;
      opacity: 0.9;
    }
    .blog-featured-image {
      width: 100%;
      height: 400px;
      overflow: hidden;
      position: relative;
    }
    .blog-featured-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .blog-content-area {
      padding: 40px 30px;
    }
    .content-block {
      margin-bottom: 30px;
    }
    .text-block {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #333;
    }
    .text-block p {
      margin-bottom: 20px;
      text-align: justify;
    }
    .image-block {
      text-align: center;
      margin: 40px 0;
    }
    .image-block img {
      max-width: 100%;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      transition: transform 0.3s ease;
    }
    .image-block img:hover {
      transform: scale(1.02);
    }
    .image-caption {
      margin-top: 15px;
      font-style: italic;
      color: #666;
      font-size: 0.9rem;
    }
    .heading-block h2 {
      font-size: 2rem;
      color: #333;
      margin: 40px 0 20px 0;
      padding-bottom: 10px;
      border-bottom: 3px solid #667eea;
      font-weight: 600;
    }
    .heading-block h3 {
      font-size: 1.5rem;
      color: #444;
      margin: 30px 0 15px 0;
      font-weight: 600;
    }
    .quote-block {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      padding: 30px;
      margin: 40px 0;
      border-radius: 12px;
      font-style: italic;
      font-size: 1.2rem;
      line-height: 1.6;
      position: relative;
    }
    .quote-block::before {
      content: '"';
      font-size: 4rem;
      position: absolute;
      top: -10px;
      left: 20px;
      opacity: 0.3;
    }
    .back-btn {
      display: inline-block;
      margin-bottom: 20px;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      padding: 10px 20px;
      border: 2px solid #667eea;
      border-radius: 25px;
      transition: all 0.3s ease;
    }
    .back-btn:hover {
      background: #667eea;
      color: white;
      text-decoration: none;
    }
    .no-content {
      text-align: center;
      padding: 60px 20px;
      color: #666;
      font-size: 1.1rem;
    }
    .no-content h3 {
      color: #333;
      margin-bottom: 15px;
    }
    .debug-info {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 5px;
      padding: 15px;
      margin: 20px 0;
      font-family: monospace;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <?php include '../include/header.php'; ?>
  <main class="blogpost-container">
    <a href="blog.php" class="back-btn">&larr; Back to Blog</a>
    
    <!-- Debug information (remove in production) -->
    
    
    <article id="blog-post" class="blog-post">
      <header class="blog-header">
        <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
        <div class="blog-meta">
          <span class="blog-date">
            <?php 
            $date = $blog['published_at'] ? $blog['published_at'] : $blog['created_at'];
            echo date('F j, Y', strtotime($date));
            ?>
          </span>
        </div>
      </header>
      
      <?php if ($blog['image']): ?>
        <div class="blog-featured-image">
          <img src="../<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
        </div>
      <?php endif; ?>
      
      <div class="blog-content-area">
        <!-- Display content blocks in order -->
        <?php if (!empty($contentBlocks)): ?>
          <?php foreach ($contentBlocks as $block): ?>
            <?php if ($block['content_type'] === 'text' && !empty($block['content_text'])): ?>
              <div class="content-block text-block">
                <p><?php echo nl2br(htmlspecialchars($block['content_text'])); ?></p>
              </div>
            <?php elseif ($block['content_type'] === 'image' && !empty($block['content_image'])): ?>
              <div class="content-block image-block">
                <img src="../<?php echo htmlspecialchars($block['content_image']); ?>" 
                     alt="<?php echo htmlspecialchars($block['image_alt_text'] ?? 'Blog image'); ?>">
                <?php if (!empty($block['image_alt_text'])): ?>
                  <p class="image-caption"><?php echo htmlspecialchars($block['image_alt_text']); ?></p>
                <?php endif; ?>
              </div>
            <?php elseif ($block['content_type'] === 'heading' && !empty($block['content_text'])): ?>
              <div class="content-block heading-block">
                <h2><?php echo htmlspecialchars($block['content_text']); ?></h2>
              </div>
            <?php elseif ($block['content_type'] === 'quote' && !empty($block['content_text'])): ?>
              <div class="content-block quote-block">
                <blockquote><?php echo htmlspecialchars($block['content_text']); ?></blockquote>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback: Display the main content field if no content blocks -->
          <div class="content-block text-block">
            <?php if (!empty($blog['content'])): ?>
              <?php echo $blog['content']; ?>
            <?php else: ?>
              <div class="no-content">
                <h3>No content available</h3>
                <p>This blog post doesn't have any content yet.</p>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </article>
  </main>
  <?php include '../include/footer.php'; ?>

    <!-- Floating WhatsApp Button -->
    <a
      href="https://wa.me/+250788123456?text=Hello! I'd like to know more about Virunga Homestay"
      class="floating-whatsapp"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.688"
        />
      </svg>
    </a>
</body>
</html>

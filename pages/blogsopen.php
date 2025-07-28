<?php
require_once '../include/connection.php';
require_once '../include/image_helpers.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$blog = null;
if ($slug) {
    $stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ? AND is_published = 1 LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $blog = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $blog ? htmlspecialchars($blog['title']) . ' | Virunga Homestay' : 'Blog Not Found | Virunga Homestay'; ?></title>
  <link rel="stylesheet" href="../css/blogs.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/logo.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    .blog-hero-full {
      width: 100vw;
      min-height: 90vh;
      height: 90dvh;
      max-height: 100vh;
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: flex-end;
      justify-content: flex-start;
      background: #222;
      margin-left: calc(-1 * ((100vw - 100%) / 2)); /* Remove container padding */
    }
    .blog-hero-full img {
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;
      filter: brightness(0.7) saturate(1.1);
      transition: filter 0.3s;
    }
    .blog-hero-overlay {
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      background: linear-gradient(180deg,rgba(0,0,0,0.25) 0%,rgba(0,0,0,0.55) 100%);
      z-index: 2;
    }
    .blog-hero-content {
      position: relative;
      z-index: 3;
      color: #fff;
      padding: 3rem 2rem 2.5rem 2rem;
      max-width: 900px;
    }
    .blog-hero-title {
      font-size: clamp(2.2rem, 5vw, 3.5rem);
      font-weight: 700;
      margin-bottom: 1rem;
      line-height: 1.1;
      text-shadow: 0 4px 32px rgba(0,0,0,0.25);
      font-family: 'Playfair Display', serif;
    }
    .blog-hero-date {
      font-size: 1.1rem;
      color: #f2c572;
      font-weight: 500;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 8px rgba(0,0,0,0.18);
    }
    .blog-content-full {
      max-width: 900px;
      margin: 0 auto;
      background: #fff;
      border-radius: 0 0 24px 24px;
      box-shadow: 0 8px 32px rgba(44,90,160,0.07);
      padding: 2.5rem 1.5rem 3rem 1.5rem;
      margin-top: -3rem;
      position: relative;
      z-index: 10;
      font-size: 1.18rem;
      color: #2d3a3a;
      line-height: 1.8;
      min-height: 200px;
    }
    .blog-content-full img {
      max-width: 100%;
      height: auto;
      border-radius: 12px;
      margin: 2rem 0;
      display: block;
      box-shadow: 0 2px 16px rgba(44,90,160,0.08);
    }
    .blog-back-btn {
      display: inline-block;
      margin: 2rem 0 0 0;
      color: #b85c19;
      background: none;
      border: none;
      font-size: 1.1rem;
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s;
      cursor: pointer;
    }
    .blog-back-btn:hover {
      color: #f2c572;
      text-decoration: underline;
    }
    @media (max-width: 900px) {
      .blog-hero-content, .blog-content-full { padding-left: 1rem; padding-right: 1rem; }
      .blog-content-full { padding-top: 1.5rem; padding-bottom: 2rem; }
    }
    @media (max-width: 600px) {
      .blog-hero-content { padding: 2rem 0.7rem 1.5rem 0.7rem; }
      .blog-hero-title { font-size: 2rem; }
      .blog-content-full { border-radius: 0 0 16px 16px; }
    }
    body { background: #f8f6f3; }
  </style>
</head>
<body>
  <?php include './include/header.php'; ?>
  <?php if (!$blog): ?>
    <main style="max-width:700px;margin:7rem auto 3rem auto;padding:2rem 1rem 3rem 1rem;text-align:center;background:#fff;border-radius:18px;box-shadow:0 4px 24px rgba(44,90,160,0.08);">
      <h2 style="color:#b85c19;font-size:2rem;margin-bottom:1rem;">Blog Not Found</h2>
      <p style="color:#888;font-size:1.1rem;">The blog post you are looking for does not exist or is not published.</p>
      <a href="blog.php" class="blog-back-btn">&larr; Back to Blogs</a>
    </main>
  <?php else: ?>
    <section class="blog-hero-full">
      <img src="../<?php echo buildImageUrl($blog['image'], 'blogs'); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
      <div class="blog-hero-overlay"></div>
      <div class="blog-hero-content">
        <div class="blog-hero-date">
          <?php 
            $date = $blog['published_at'] ? $blog['published_at'] : $blog['created_at'];
            echo date('F j, Y', strtotime($date));
          ?>
        </div>
        <div class="blog-hero-title"><?php echo htmlspecialchars($blog['title']); ?></div>
      </div>
    </section>
    <main class="blog-content-full">
      <?php echo $blog['content']; // content is already HTML ?>
      <a href="blog.php" class="blog-back-btn">&larr; Back to Blogs</a>
    </main>
  <?php endif; ?>
  <?php include '../include/footer.php'; ?>
</body>
</html>

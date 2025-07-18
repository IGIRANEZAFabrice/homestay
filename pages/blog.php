<?php
require_once '../include/connection.php';

// Fetch published blogs from database
$sql = "SELECT * FROM blogs WHERE is_published = 1 ORDER BY published_at DESC, created_at DESC";
$result = $conn->query($sql);
$blogs = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $blogs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog | Virunga Homestay</title>
  <link rel="stylesheet" href="../css/blogs.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/rooms.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <?php include './include/header.php'; ?>
   <section class="rooms-hero">
    <h1>Our Rooms</h1>
    <p>Experience comfort and culture in our thoughtfully designed rooms</p>
  </section>
  <main class="blogs-list-container">
    <div class="blogs-grid">
      <?php if (empty($blogs)): ?>
        <div class="no-blogs">
          <h2>No blog posts yet</h2>
          <p>Check back soon for exciting stories and updates from Virunga Homestay!</p>
        </div>
      <?php else: ?>
        <?php foreach ($blogs as $blog): ?>
          <article class="blog-card">
            <div class="blog-image">
              <img src="../<?php echo buildImageUrl($blog['image'], 'blogs'); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
            </div>
            <div class="blog-content">
              <h2 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h2>
              <div class="blog-excerpt">
                <?php 
                // Display content or excerpt
                $content = $blog['content'];
                if (strlen($content) > 200) {
                    echo htmlspecialchars(substr($content, 0, 200)) . '...';
                } else {
                    echo htmlspecialchars($content);
                }
                ?>
              </div>
              <div class="blog-meta">
                <span class="blog-date">
                  <?php 
                  $date = $blog['published_at'] ? $blog['published_at'] : $blog['created_at'];
                  echo date('F j, Y', strtotime($date));
                  ?>
                </span>
              </div>
              <a href="blogsopen.php?slug=<?php echo htmlspecialchars($blog['slug']); ?>" class="read-more">
                Read More
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
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

<?php
require_once __DIR__ . '/../config/db.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$stmt = $conn->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'published'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $blog = $res->fetch_assoc();
} else {
    header("Location: " . $baseLink('blog'));
    exit;
}

  $pageTitle = 'Virunga Homestay - ' . htmlspecialchars($blog['title']);
  $pageCss = ['blogdetails.css'];
  $pageScripts = [];
  include 'includes/header.php';

// Format vars
$thumbnail = !empty($blog['thumbnail']) ? htmlspecialchars($blog['thumbnail']) : './img/hero/2.jpg';
if (strpos($thumbnail, '/') === false && strpos($thumbnail, '\\') === false && strpos($thumbnail, 'http') !== 0) {
    $thumbnail = './img/blogs/' . $thumbnail;
} elseif (strpos($thumbnail, './') !== 0 && strpos($thumbnail, 'http') !== 0) {
    $thumbnail = './' . ltrim($thumbnail, '/');
}
$kickerLine = htmlspecialchars($blog['date_published']) . ' &middot; ' . htmlspecialchars($blog['kicker']);
$chipsArr = !empty($blog['chips']) ? explode(',', $blog['chips']) : [];
?>

<section class="detail-hero detail-hero--blog">
  <img src="<?php echo $thumbnail; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" />
  <div class="detail-hero__overlay"></div>
  <div class="detail-hero__content">
    <p class="detail-hero__kicker"><?php echo $kickerLine; ?></p>
    <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
    <p class="detail-hero__sub"><?php echo htmlspecialchars($blog['sub_title']); ?></p>
    <div class="detail-hero__chips">
      <?php foreach($chipsArr as $chip): ?>
        <span><?php echo htmlspecialchars(trim($chip)); ?></span>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<main id="blog-detail-page">
  <section class="article-shell">
    <div class="article-wrap article-grid">
      <aside class="article-rail">
        <div class="rail-card">
          <h3>In This Story</h3>
          <nav class="rail-toc" aria-label="Article sections">
            <a href="#experience">Highland Experience</a>
            <a href="#culture">Local Culture</a>
            <a href="#wildlife">Wildlife and Eco Adventures</a>
            <a href="#activities">Activities You Cannot Miss</a>
            <a href="#why-base">Why Travelers Choose This Base</a>
            <a href="#plan">Plan Your Journey</a>
          </nav>
        </div>

        <div class="rail-card">
          <h3>Article Snapshot</h3>
          <ul class="rail-facts">
            <li><span>Category</span><strong><?php echo ucfirst(htmlspecialchars($blog['category'])); ?></strong></li>
            <li><span>Location</span><strong>Musanze, Rwanda</strong></li>
            <li><span>Read time</span><strong><?php echo htmlspecialchars($blog['read_time']); ?></strong></li>
          </ul>
        </div>
      </aside>

      <article class="article-main">
        <?php echo $blog['content']; ?>

        <section class="author-card" aria-label="Author">
          <img src="./img/logo/logo-small.png" alt="Virunga editorial team" />
          <div>
            <h3>Virunga Editorial Team</h3>
            <p>
              We write practical travel stories and local insight guides to help guests get the most meaningful
              experiences in Northern Rwanda.
            </p>
          </div>
        </section>

        <?php include 'includes/book_form.php'; ?>
      </article>
    </div>
  </section>

  <section class="related-posts">
    <div class="article-wrap">
      <div class="related-head">
        <span>Continue Reading</span>
        <h2>Related stories from the journal</h2>
      </div>

      <div class="related-grid">
        <?php
        $relatedStmt = $conn->prepare("SELECT slug, title, sub_title, thumbnail FROM blogs WHERE status = 'published' AND id != ? ORDER BY id DESC LIMIT 3");
        $relatedStmt->bind_param("i", $blog['id']);
        $relatedStmt->execute();
        $relatedRes = $relatedStmt->get_result();

        if ($relatedRes && $relatedRes->num_rows > 0) {
            while ($relBlog = $relatedRes->fetch_assoc()) {
                $relThumb = !empty($relBlog['thumbnail']) ? htmlspecialchars($relBlog['thumbnail']) : './img/hero/1.jpg';
                if (strpos($relThumb, '/') === false && strpos($relThumb, '\\') === false && strpos($relThumb, 'http') !== 0) {
                    $relThumb = './img/blogs/' . $relThumb;
                } elseif (strpos($relThumb, './') !== 0 && strpos($relThumb, 'http') !== 0) {
                    $relThumb = './' . ltrim($relThumb, '/');
                }
                $relUrl = $baseLink('blogdetails') . '?slug=' . urlencode($relBlog['slug']);
                $relTitle = htmlspecialchars($relBlog['title']);
                // Generate simple subtitle if none provided
                $relSub = !empty($relBlog['sub_title']) ? htmlspecialchars($relBlog['sub_title']) : '';
                ?>
                <a class="related-card" href="<?php echo $relUrl; ?>">
                  <img src="<?php echo $relThumb; ?>" alt="<?php echo $relTitle; ?>" />
                  <div>
                    <h3><?php echo $relTitle; ?></h3>
                    <p><?php echo $relSub; ?></p>
                  </div>
                </a>
                <?php
            }
        }
        ?>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

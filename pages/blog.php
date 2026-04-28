<?php
  $pageTitle = 'Virunga Homestay - Blog';
  $pageCss = ['page-hero.css', 'blog.css'];
  $pageHeroKey = 'blog';
  $pageScripts = ['blog.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<main id="blog-page">
  <section class="blog-intro">
    <div class="section-container blog-intro__grid">
      <div>
        <div class="section-label">Latest Stories</div>
        <h2 class="blog-title">Fresh stories from Musanze, crafted for travelers</h2>
        <p class="blog-lead">
          Explore local insights, travel tips, and behind-the-scenes stories from Virunga Homestay.
          Use filters to discover what matches your trip style.
        </p>
      </div>
      <div class="blog-kpis">
        <div class="blog-kpi"><strong>24</strong><span>Published Posts</span></div>
        <div class="blog-kpi"><strong>6</strong><span>Topics</span></div>
        <div class="blog-kpi"><strong>4 min</strong><span>Avg Read Time</span></div>
      </div>
    </div>
  </section>

  <section class="blog-controls">
    <div class="section-container blog-controls__row">
      <div class="blog-filters" role="tablist" aria-label="Blog categories">
        <button class="blog-filter is-active" data-filter="all" type="button">All</button>
        <button class="blog-filter" data-filter="guides" type="button">Guides</button>
        <button class="blog-filter" data-filter="culture" type="button">Culture</button>
        <button class="blog-filter" data-filter="food" type="button">Food</button>
        <button class="blog-filter" data-filter="nature" type="button">Nature</button>
      </div>
      <label class="blog-search-wrap" for="blogSearch">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input id="blogSearch" class="blog-search" type="text" placeholder="Search stories" autocomplete="off" />
      </label>
    </div>
  </section>

  <section class="blog-listing">
    <div class="section-container">
      <div class="blog-grid" id="blogGrid">
        <?php
        require_once __DIR__ . '/../config/db.php';
        $sqlBlogs = "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC";
        $resBlogs = $conn->query($sqlBlogs);
        if ($resBlogs && $resBlogs->num_rows > 0) {
            while ($blog = $resBlogs->fetch_assoc()) {
                $category = htmlspecialchars($blog['category']);
                $title = htmlspecialchars($blog['title']);
                // Use a short summary generated from the start of the content if subtitle is empty
                $cleanContent = strip_tags($blog['content']);
                $subTitle = !empty($blog['sub_title']) ? htmlspecialchars($blog['sub_title']) : (strlen($cleanContent) > 100 ? substr($cleanContent, 0, 100) . '...' : $cleanContent);
                $kicker = !empty($blog['kicker']) ? htmlspecialchars($blog['kicker']) : ucfirst($category);
                $thumbnail = !empty($blog['thumbnail']) ? htmlspecialchars($blog['thumbnail']) : './img/hero/1.jpg';
                if (strpos($thumbnail, '/') === false && strpos($thumbnail, '\\') === false && strpos($thumbnail, 'http') !== 0) {
                    $thumbnail = './img/blogs/' . $thumbnail;
                } elseif (strpos($thumbnail, './') !== 0 && strpos($thumbnail, 'http') !== 0) {
                    $thumbnail = './' . ltrim($thumbnail, '/');
                }
                $datePub = htmlspecialchars($blog['date_published']);
                $readTime = htmlspecialchars($blog['read_time']);
                $slug = htmlspecialchars($blog['slug']);
                
                $detailUrl = $baseLink('blogdetails') . '?slug=' . urlencode($slug);
                ?>
                <a class="blog-card" href="<?php echo $detailUrl; ?>" data-category="<?php echo strtolower(str_replace(' ', '-', $category)); ?>" data-title="<?php echo strtolower($title); ?>">
                  <div class="blog-card__media">
                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo $title; ?>" />
                  </div>
                  <div class="blog-card__body">
                    <span class="blog-card__tag"><?php echo $kicker; ?></span>
                    <h3><?php echo $title; ?></h3>
                    <p><?php echo $subTitle; ?></p>
                    <div class="blog-card__meta">
                      <span><?php echo $datePub; ?></span>
                      <span><?php echo $readTime; ?></span>
                    </div>
                  </div>
                </a>
                <?php
            }
        }
        ?>
      </div>

      <div class="blog-more-wrap">
        <button id="blogShowMore" class="blog-more-btn" type="button">View more</button>
      </div>

      <p class="blog-empty" id="blogEmpty" hidden>No stories match your search yet.</p>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>

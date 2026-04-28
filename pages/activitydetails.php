<?php
  require_once __DIR__ . '/../config/db.php';

  $act_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($act_id <= 0) {
      header("Location: " . $baseLink('activity'));
      exit;
  }

  $stmt = $conn->prepare("SELECT * FROM activities WHERE id = ? AND status = 'active'");
  $stmt->bind_param("i", $act_id);
  $stmt->execute();
  $activityData = $stmt->get_result()->fetch_assoc();

  if (!$activityData) {
      header("Location: " . $baseLink('activity'));
      exit;
  }

  $title        = htmlspecialchars($activityData['title']);
  $tag          = htmlspecialchars($activityData['tag']);
  $short_desc   = htmlspecialchars($activityData['short_description']);
  $long_desc    = !empty($activityData['long_description']) ? $activityData['long_description'] : '';
  $duration     = htmlspecialchars($activityData['duration'] ?? '');
  $age_group    = htmlspecialchars($activityData['age_group'] ?? '');
  $group_size   = htmlspecialchars($activityData['group_size'] ?? '');
  $characteristics = htmlspecialchars($activityData['characteristics'] ?? '');
  $price        = htmlspecialchars($activityData['price'] ?? '');

  $imgVal = !empty($activityData['image']) ? $activityData['image'] : 'services/2.jpg';
  if (strpos($imgVal, 'http') === 0) {
      $image = $imgVal;
  } else {
      $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
      if (strpos($imgVal, '/') === false) $imgVal = 'activities/' . $imgVal;
      $image = './img/' . $imgVal;
  }
  $image = htmlspecialchars($image);

  $pageTitle       = 'Virunga Homestay — ' . $activityData['title'];
  $pageDescription = $activityData['short_description'];
  $pageCss         = ['activitydetails.css'];
  $pageScripts     = [];
  include 'includes/header.php';
?>

<!-- ── HERO ─────────────────────────────────────────────── -->
<section class="detail-hero detail-hero--activity">
  <img src="<?= $image ?>" alt="<?= $title ?>" />
  <div class="detail-hero__overlay"></div>
  <div class="detail-hero__content">
    <p class="detail-hero__kicker"><?= $tag ?> &nbsp;·&nbsp; Community Experience</p>
    <h1><?= $title ?></h1>
    <?php if ($price): ?>
      <p class="detail-hero__sub"><i class="fa-solid fa-tag" style="margin-right:6px;"></i><?= $price ?></p>
    <?php endif; ?>
  </div>
</section>

<!-- ── MAIN ──────────────────────────────────────────────── -->
<main id="activity-detail-page">
  <section class="act-shell">
    <div class="act-wrap act-grid">

      <!-- Sidebar rail -->
      <aside class="act-rail">

        <!-- Quick facts -->
        <div class="rail-card">
          <h3>Activity Details</h3>
          <ul class="rail-facts">
            <?php if ($price): ?>
            <li><span>Price</span><strong><?= $price ?></strong></li>
            <?php endif; ?>
            <?php if ($duration): ?>
            <li><span>Duration</span><strong><?= $duration ?></strong></li>
            <?php endif; ?>
            <?php if ($group_size): ?>
            <li><span>Group Size</span><strong><?= $group_size ?></strong></li>
            <?php endif; ?>
            <?php if ($age_group): ?>
            <li><span>Age Group</span><strong><?= $age_group ?></strong></li>
            <?php endif; ?>
            <?php if ($characteristics): ?>
            <li><span>Type</span><strong><?= $characteristics ?></strong></li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Booking CTA -->
        <div class="rail-card rail-card--cta">
          <h3>Ready to Join?</h3>
          <p>Book this experience directly through WhatsApp for instant confirmation.</p>
          <div class="rail-cta-actions">
            <a class="rail-btn-primary" href="https://wa.me/250784513435?text=Hello!%20I%20am%20interested%20in%20booking%20the%20<?= rawurlencode($activityData['title']) ?>" target="_blank" rel="noopener">
              <i class="fa-brands fa-whatsapp"></i> Book on WhatsApp
            </a>
            <a class="rail-btn-ghost" href="tel:+250784513435">
              <i class="fa-solid fa-phone"></i> Call Now
            </a>
          </div>
        </div>

      </aside>

      <!-- Article body -->
      <article class="act-main">

        <!-- Short intro card -->
        <div class="prose-block prose-block--intro">
          <?php if ($price): ?>
          <div class="act-price-badge">
            <i class="fa-solid fa-tag"></i>
            <span><?= $price ?></span>
          </div>
          <?php endif; ?>
          <p class="lead-dropcap"><?= $short_desc ?></p>
        </div>

        <!-- Rich long description blocks -->
        <?php if (!empty(trim(strip_tags($long_desc)))): ?>
          <?= $long_desc ?>
        <?php endif; ?>

        <!-- Booking form -->
        <?php include 'includes/book_form.php'; ?>

      </article>
    </div>
  </section>

  <!-- ── RECOMMENDATIONS ──────────────────────────────── -->
  <section class="activity-recommendations">
    <div class="act-wrap">
      <div class="related-head">
        <span>More Experiences</span>
        <h2>Activities you may also enjoy</h2>
      </div>

      <div class="related-grid">
        <?php
        $sqlRec = "SELECT * FROM activities WHERE status = 'active' AND id != ? LIMIT 3";
        $stmtRec = $conn->prepare($sqlRec);
        $stmtRec->bind_param("i", $act_id);
        $stmtRec->execute();
        $resRec = $stmtRec->get_result();
        if ($resRec && $resRec->num_rows > 0) {
            while ($rowRec = $resRec->fetch_assoc()) {
                $imgRec = !empty($rowRec['image']) ? $rowRec['image'] : 'services/4.jpg';
                if (strpos($imgRec, 'http') !== 0) {
                    $imgRec = preg_replace('/^(\.\/)?img\//', '', ltrim($imgRec, '/'));
                    if (strpos($imgRec, '/') === false) $imgRec = 'activities/' . $imgRec;
                    $imgRec = './img/' . $imgRec;
                }
                $shortText = trim(strip_tags($rowRec['short_description']));
                if (mb_strlen($shortText) > 100) $shortText = mb_substr($shortText, 0, 100) . '...';
                $recUrl = $baseLink('activitydetails') . (strpos($baseLink('activitydetails'), '?') !== false ? '&' : '?') . 'id=' . $rowRec['id'];
                ?>
                <a class="related-card" href="<?= $recUrl ?>">
                  <img src="<?= htmlspecialchars($imgRec) ?>" alt="<?= htmlspecialchars($rowRec['title']) ?>" />
                  <div>
                    <h3><?= htmlspecialchars($rowRec['title']) ?></h3>
                    <p><?= htmlspecialchars($shortText) ?></p>
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

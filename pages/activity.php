<?php
  $pageTitle = 'Virunga Homestay - Community Activities';
  $pageCss = ['page-hero.css', 'room-cards.css', 'activity.css'];
  $pageHeroKey = 'activity';
  $pageScripts = ['activity.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<main id="activity-page">
  <section class="activity-intro">
    <div class="section-container activity-intro__grid">
      <div class="activity-intro__copy">
        <div class="section-label">Community Activities</div>
        <h2 class="section-heading activity-intro__title">Build your perfect day in Musanze</h2>
        <p class="activity-intro__body">
          Pick the mood of your trip and we will tailor your experience with local guides,
          artisan hosts, and flexible timing.
        </p>

        <div class="activity-choices" role="tablist" aria-label="Activity planner presets">
          <button
            class="activity-choice is-active"
            data-title="Culture and craft immersion"
            data-body="Visit local makers, learn imigongo techniques, and join a storytelling tea circle in the evening."
            data-time="Half day"
            data-group="2-8 guests"
            data-image="./img/services/3.jpg"
            type="button"
          >Culture</button>
          <button
            class="activity-choice"
            data-title="Food and farm discovery"
            data-body="Harvest seasonal produce, cook with our host family, and taste fresh Rwandan coffee from nearby growers."
            data-time="4-6 hours"
            data-group="2-10 guests"
            data-image="./img/services/2.jpg"
            type="button"
          >Food</button>
          <button
            class="activity-choice"
            data-title="Nature and soft adventure"
            data-body="Enjoy scenic village walks, birdwatching, and optional sunrise viewpoints with trusted local companions."
            data-time="Flexible"
            data-group="1-6 guests"
            data-image="./img/services/4.jpg"
            type="button"
          >Nature</button>
        </div>
      </div>

      <aside class="activity-preview" aria-live="polite">
        <img id="activityPreviewImage" class="activity-preview__img" src="./img/services/3.jpg" alt="Activity preview" />
        <div class="activity-preview__overlay"></div>
        <div class="activity-preview__content">
          <span class="activity-preview__pill">Recommended Plan</span>
          <h3 id="activityPreviewTitle" class="activity-preview__title">Culture and craft immersion</h3>
          <p id="activityPreviewBody" class="activity-preview__text">
            Visit local makers, learn imigongo techniques, and join a storytelling tea circle in the evening.
          </p>
          <div class="activity-preview__meta">
            <span id="activityPreviewTime"><i class="fa-solid fa-clock"></i> Half day</span>
            <span id="activityPreviewGroup"><i class="fa-solid fa-users"></i> 2-8 guests</span>
          </div>
        </div>
      </aside>
    </div>
  </section>

  <section id="activities">
    <div class="section-container">
      <div class="section-label">Things to Do and See</div>
      <div class="activities-header">
        <h2 class="section-heading">Handpicked community experiences</h2>
        <p class="section-sub">
          The same warm moments you saw on Home, now expanded with more ways to explore.
        </p>
      </div>

      <div class="activities-grid">
        <?php
        require_once __DIR__ . '/../config/db.php';
        $sqlAct = "SELECT * FROM activities WHERE status = 'active' ORDER BY display_order ASC, id DESC";
        $resAct = $conn->query($sqlAct);

        if ($resAct && $resAct->num_rows > 0) {
            while ($rowAct = $resAct->fetch_assoc()) {
                $imgVal = !empty($rowAct['image']) ? $rowAct['image'] : 'services/2.jpg';
                if (strpos($imgVal, 'http') === 0) {
                    $image = $imgVal;
                } else {
                    $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                    if (strpos($imgVal, '/') === false) {
                        $imgVal = 'activities/' . $imgVal;
                    }
                    $image = './img/' . $imgVal;
                }
                $image = htmlspecialchars($image);
                
                $title = htmlspecialchars($rowAct['title']);
                $tag = htmlspecialchars($rowAct['tag']);
                $duration = htmlspecialchars($rowAct['duration'] ?? '');
                $char = htmlspecialchars($rowAct['characteristics'] ?? '');
                $price = !empty($rowAct['price']) ? htmlspecialchars($rowAct['price']) : '';
                $detailLink = $baseLink('activitydetails') . (strpos($baseLink('activitydetails'), '?') !== false ? '&' : '?') . 'id=' . $rowAct['id'];
                $priceHtml = $price ? '<div class="room-card__price"><span class="amount">' . $price . '</span></div>' : '';

                echo '
                <a class="room-card" href="' . $detailLink . '">
                  <img class="room-card__img" src="' . $image . '" alt="' . $title . '" />
                  <div class="room-card__overlay"></div>
                  <span class="room-card__tag">' . $tag . '</span>
                  <div class="room-card__content">
                    <h3 class="room-card__name">' . $title . '</h3>
                    <div class="room-card__meta">
                      <span><i class="fa-solid fa-seedling"></i> ' . $char . '</span>
                      <span><i class="fa-solid fa-clock"></i> ' . $duration . '</span>
                    </div>
                    ' . $priceHtml . '
                  </div>
                  <span class="room-card__cta">View Details <i class="fa-solid fa-arrow-right fa-xs"></i></span>
                </a>';
            }
        } else {
            echo '<p style="grid-column: 1 / -1; text-align: center;">No activities available at the moment.</p>';
        }
        ?>
      </div>

      <div class="activities-actions">
        <button class="btn-primary" id="activityShowMore" type="button">
          <i class="fa-solid fa-plus fa-xs"></i> Show More Activities
        </button>
      </div>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>


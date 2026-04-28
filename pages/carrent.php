<?php
  $pageTitle = 'Virunga Homestay - Car Rent';
  $pageCss = ['page-hero.css','carrent.css'];
  $pageHeroKey = 'carrent';
  $pageScripts = [];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<!-- ===== INTRO SECTION ===== -->
<section class="carrent-intro">
  <div class="carrent-intro__inner">
    <span class="carrent-intro__eyebrow">Private Transport</span>
    <h2 class="carrent-intro__heading">Explore Rwanda<br>at Your Own Pace</h2>
    <p class="carrent-intro__body">
      Journey through volcanic landscapes, misty rainforests, and golden savannah with 
      a private vehicle tailored to your adventure. Each car comes with an experienced 
      local driver who knows every hidden road.
    </p>
    <div class="carrent-intro__pills">
      <span class="pill">&#10003; Driver included</span>
      <span class="pill">&#10003; Fuel covered</span>
      <span class="pill">&#10003; Park transfers</span>
      <span class="pill">&#10003; 24/7 support</span>
    </div>
  </div>
</section>

<!-- ===== CARS GRID ===== -->
<section class="carrent-fleet">
  <div class="carrent-fleet__header">
    <h3 class="carrent-fleet__label">Our Fleet</h3>
  </div>

  <div class="carrent-grid">
    <?php
    require_once __DIR__ . '/../config/db.php';
    $sqlCars = "SELECT * FROM cars WHERE status = 'active' ORDER BY display_order ASC";
    $resCars = $conn->query($sqlCars);
    if ($resCars && $resCars->num_rows > 0) {
        while ($car = $resCars->fetch_assoc()) {
            
            // Image handling (auto-route to cars/)
            $cImgVal = !empty($car['image']) ? $car['image'] : 'placeholder.jpg';
            if (strpos($cImgVal, 'http') === 0) {
                $cImg = $cImgVal;
            } else {
                $cImgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($cImgVal, '/'));
                if (strpos($cImgVal, '/') === false) {
                    $cImgVal = 'cars/' . $cImgVal;
                }
                $cImg = './img/' . $cImgVal;
            }
            
            // Badge text & class logic
            $cBadge = !empty($car['badge']) ? htmlspecialchars($car['badge']) : '';
            $badgeClassModifier = '';
            if ($cBadge) {
                $badgeLower = strtolower($cBadge);
                if (strpos($badgeLower, 'group') !== false) {
                    $badgeClassModifier = ' car-card__badge--group';
                }
            }
            ?>
            <article class="car-card">
              <div class="car-card__image-wrap">
                <img src="<?php echo htmlspecialchars($cImg); ?>" alt="<?php echo htmlspecialchars($car['title']); ?>" class="car-card__image" onerror="this.style.display='none'">
                <div class="car-card__image-placeholder">
                  <span class="car-icon">&#9698;</span>
                </div>
                <?php if ($cBadge): ?>
                  <span class="car-card__badge<?php echo $badgeClassModifier; ?>"><?php echo $cBadge; ?></span>
                <?php endif; ?>
              </div>
              <div class="car-card__body">
                <div class="car-card__meta">
                  <span class="car-card__type"><?php echo htmlspecialchars($car['type']); ?></span>
                  <span class="car-card__seats">&#128100; <?php echo htmlspecialchars($car['seats']); ?></span>
                </div>
                <h4 class="car-card__name"><?php echo htmlspecialchars($car['title']); ?></h4>
                <p class="car-card__desc"><?php echo htmlspecialchars($car['description']); ?></p>
                <ul class="car-card__features">
                  <?php if (!empty($car['feature_1'])): ?><li><?php echo htmlspecialchars($car['feature_1']); ?></li><?php endif; ?>
                  <?php if (!empty($car['feature_2'])): ?><li><?php echo htmlspecialchars($car['feature_2']); ?></li><?php endif; ?>
                  <?php if (!empty($car['feature_3'])): ?><li><?php echo htmlspecialchars($car['feature_3']); ?></li><?php endif; ?>
                </ul>
                <div class="car-card__footer">
                  <div class="car-card__price">
                    <span class="price-amount">$<?php echo htmlspecialchars(intval($car['price'])); ?></span>
                    <span class="price-per">/ day</span>
                  </div>
                  <a href="#contact" class="car-card__cta">Book Now</a>
                </div>
              </div>
            </article>
            <?php
        }
    } else {
        echo "<p style='text-align:center;grid-column:1/-1;'>No vehicles available currently.</p>";
    }
    ?>
  </div><!-- /.carrent-grid -->
</section>

<!-- ===== CONTACT / BOOKING CTA ===== -->
<section class="carrent-contact" id="contact">
  <div class="carrent-contact__inner">
    <h3 class="carrent-contact__heading">Ready to Hit the Road?</h3>
    <p class="carrent-contact__sub">Tell us your dates and destination — we'll take care of everything else.</p>
    <a href="mailto:info@virungahomestay.com" class="carrent-contact__btn">Get in Touch</a>
    <a href="https://wa.me/250700000000" class="carrent-contact__btn carrent-contact__btn--wa" target="_blank" rel="noopener">
      WhatsApp Us
    </a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
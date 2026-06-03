<?php
  require_once __DIR__ . '/../config/db.php';
  $pageTitle   = 'Virunga Homestay – House Rules';
  $pageCss     = ['page-hero.css', 'house-rules.css'];
  $pageHeroKey = 'house-rules';
  $pageScripts = ['house-rules.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<!-- ═══════════════════════════════════════════════════════
  WELCOME BAND
═══════════════════════════════════════════════════════ -->
<div class="hr-welcome" data-reveal="fade-up">
  <div class="hr-welcome__inner">
    <i class="fa-solid fa-leaf hr-welcome__icon"></i>
    <p>
      Welcome to <strong>Virunga Homestay</strong> — enjoy a unique and immersive experience
      blending nature, culture, adventure, and conservation. Please review our house rules to
      ensure a comfortable stay for everyone.
    </p>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════
  QUICK FACTS STRIP  (check-in / check-out / quiet hours)
═══════════════════════════════════════════════════════ -->
<section class="hr-facts" id="hrFacts">
  <div class="hr-facts__orb hr-facts__orb--l"></div>
  <div class="hr-facts__orb hr-facts__orb--r"></div>

  <div class="hr-container">
    <div class="hr-facts__grid">
      <?php
      $sqlFacts = "SELECT * FROM hr_facts WHERE status = 'active' ORDER BY display_order ASC";
      $resFacts = $conn->query($sqlFacts);
      $delayF = 0;
      if ($resFacts && $resFacts->num_rows > 0) {
          while ($rowF = $resFacts->fetch_assoc()) {
              echo '
              <div class="hr-fact-card" data-reveal="fade-up" data-delay="' . $delayF . '">
                <div class="hr-fact-card__icon-wrap">
                  <i class="' . htmlspecialchars($rowF['icon']) . '"></i>
                </div>
                <div class="hr-fact-card__body">
                  <span class="hr-fact-card__label">' . htmlspecialchars($rowF['label']) . '</span>
                  <span class="hr-fact-card__value">' . htmlspecialchars($rowF['val']) . '</span>
                </div>
                <div class="hr-fact-card__glow"></div>
              </div>';
              $delayF += 100;
          }
      }
      ?>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
  RULE ICON GRID  (allowed / not-allowed 3-D flip cards)
═══════════════════════════════════════════════════════ -->
<section class="hr-rules-grid-section" id="hrRuleGrid">
  <div class="hr-container">

    <div class="hr-section-header" data-reveal="fade-up">
      <span class="hr-eyebrow"><i class="fa-solid fa-list-check"></i> At a Glance</span>
      <h2 class="hr-section-title">House <em>Rules</em></h2>
      <p class="hr-section-sub">Hover a card to learn more.</p>
    </div>

    <div class="hr-flip-grid">
      <?php
      $sqlRules = "SELECT * FROM hr_rules WHERE status = 'active' ORDER BY display_order ASC";
      $resRules = $conn->query($sqlRules);
      $delayR = 0;
      if ($resRules && $resRules->num_rows > 0) {
          while ($rowR = $resRules->fetch_assoc()) {
              echo '
              <div class="hr-flip-card hr-flip-card--' . htmlspecialchars($rowR['type']) . '" data-reveal="scale-in" data-delay="' . $delayR . '">
                <div class="hr-flip-card__inner">
                  <div class="hr-flip-card__front">
                    <i class="' . htmlspecialchars($rowR['icon']) . '"></i>
                    <span>' . $rowR['title'] . '</span>
                  </div>
                  <div class="hr-flip-card__back">
                    <p>' . $rowR['description'] . '</p>
                  </div>
                </div>
              </div>';
              $delayR += 80;
          }
      }
      ?>
    </div><!-- /hr-flip-grid -->
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
  DETAILED RULES ACCORDION
═══════════════════════════════════════════════════════ -->
<section class="hr-detail-section" id="hrDetail">
  <div class="hr-container hr-container--narrow">

    <div class="hr-section-header" data-reveal="fade-up">
      <span class="hr-eyebrow"><i class="fa-solid fa-scroll"></i> Full Details</span>
      <h2 class="hr-section-title">Detailed <em>House Rules</em></h2>
    </div>

    <div class="hr-accordion" id="hrAccordion">
      <?php
      $sqlAcc = "SELECT * FROM hr_accordion WHERE status = 'active' ORDER BY display_order ASC";
      $resAcc = $conn->query($sqlAcc);
      $delayA = 0;
      if ($resAcc && $resAcc->num_rows > 0) {
          while ($rowA = $resAcc->fetch_assoc()) {
              echo '
              <div class="hr-acc-item" data-reveal="fade-up" data-delay="' . $delayA . '">
                <button class="hr-acc-trigger">
                  <span class="hr-acc-trigger__icon-wrap"><i class="' . htmlspecialchars($rowA['icon']) . '"></i></span>
                  <span class="hr-acc-trigger__label">' . htmlspecialchars($rowA['label']) . '</span>
                  <span class="hr-acc-trigger__chevron"><i class="fa-solid fa-chevron-down"></i></span>
                </button>
                <div class="hr-acc-panel">
                  <p>' . $rowA['content'] . '</p>
                </div>
              </div>';
              $delayA += 60;
          }
      }
      ?>
    </div><!-- /hr-accordion -->
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
  CANCELLATION & REFUND POLICY — scroll timeline
═══════════════════════════════════════════════════════ -->
<section class="hr-cancel-section" id="hrCancel">
  <div class="hr-cancel__bg-canvas" id="cancelCanvas"></div>

  <div class="hr-container">
    <div class="hr-section-header hr-section-header--light" data-reveal="fade-up">
      <span class="hr-eyebrow hr-eyebrow--light"><i class="fa-solid fa-rotate-left"></i> Policies</span>
      <h2 class="hr-section-title hr-section-title--light">Cancellation &amp;<br><em>Refund Policy</em></h2>
    </div>

    <!-- Refund timeline -->
    <div class="hr-timeline" data-reveal="fade-up" data-delay="100">
      <?php
      $sqlCan = "SELECT * FROM hr_cancellation WHERE status = 'active' ORDER BY display_order ASC";
      $resCan = $conn->query($sqlCan);
      if ($resCan && $resCan->num_rows > 0) {
          while ($rowC = $resCan->fetch_assoc()) {
              echo '
              <div class="hr-tl-item hr-tl-item--' . htmlspecialchars($rowC['type']) . '">
                <div class="hr-tl-marker">
                  <div class="hr-tl-marker__ring"></div>
                  <i class="' . htmlspecialchars($rowC['icon']) . '"></i>
                </div>
                <div class="hr-tl-content">
                  <span class="hr-tl-badge hr-tl-badge--' . htmlspecialchars($rowC['type']) . '">' . htmlspecialchars($rowC['badge']) . '</span>
                  <h3 class="hr-tl-title">' . htmlspecialchars($rowC['title']) . '</h3>
                  <p class="hr-tl-desc">' . htmlspecialchars($rowC['description']) . '</p>
                </div>
              </div>';
          }
      }
      ?>
    </div><!-- /hr-timeline -->

    <!-- Important notes -->
    <div class="hr-notes-grid" data-reveal="fade-up" data-delay="200">
      <div class="hr-note-card">
        <p>Unused inclusions (meals, activities) are <strong>non-refundable</strong>.</p>
      </div>
      <div class="hr-note-card">
        <p>Refunds processed within <strong>7 working days</strong> to your original payment method.</p>
      </div>
      <div class="hr-note-card">
        <p>Discounted or promotional bookings are <strong>non-refundable</strong>.</p>
      </div>
      <div class="hr-note-card">
        <p>Refunds apply only to the <strong>basic room tariff</strong>. Convenience fees &amp; taxes are excluded.</p>
      </div>
    </div>

  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
  EXTENUATING CIRCUMSTANCES
═══════════════════════════════════════════════════════ -->
<section class="hr-extenuate-section" id="hrExtenuate">
  <div class="hr-container">

    <div class="hr-extenuate-inner" data-reveal="scale-in">
      <div class="hr-extenuate__badge">
        <i class="fa-solid fa-shield-heart"></i>
      </div>
      <h2 class="hr-extenuate__title">Extenuating <em>Circumstances</em> Policy</h2>
      <p class="hr-extenuate__lead">
        If you are unable to arrive due to unavoidable situations — natural disasters, security incidents, sudden government policy changes, or pandemics — the following special terms apply:
      </p>

      <div class="hr-ext-items">
        <?php
        $sqlExt = "SELECT * FROM hr_extenuating WHERE status = 'active' ORDER BY display_order ASC";
        $resExt = $conn->query($sqlExt);
        $delayE = 0;
        if ($resExt && $resExt->num_rows > 0) {
            while ($rowE = $resExt->fetch_assoc()) {
                echo '
                <div class="hr-ext-item" data-reveal="fade-up" data-delay="' . $delayE . '">
                  <div class="hr-ext-item__dot"></div>
                  <div class="hr-ext-item__body">
                    <i class="' . htmlspecialchars($rowE['icon']) . '"></i>
                    <div>
                      <strong>' . htmlspecialchars($rowE['title']) . '</strong>
                      <p>' . $rowE['description'] . '</p>
                    </div>
                  </div>
                </div>';
                $delayE += 80;
            }
        }
        ?>
      </div><!-- /hr-ext-items -->
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════
  CONTACT CTA
═══════════════════════════════════════════════════════ -->
<div class="hr-cta-strip" data-reveal="fade-up">
  <div class="hr-cta-strip__inner">
    <div class="hr-cta-strip__text">
      <h3>Have questions about our rules?</h3>
      <p>Our team is happy to help — reach out before your arrival.</p>
    </div>
    <div class="hr-cta-strip__actions">
      <a href="<?php echo $baseLink('contact'); ?>" class="hr-btn hr-btn--primary">
        <i class="fa-solid fa-envelope"></i> Contact Us
      </a>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
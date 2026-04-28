<?php
  $pageTitle = 'Virunga Homestay - The Perfect Place for Getaways';
  $pageDescription = 'Experience the best stay at Virunga Homestay. Your perfect sanctuary in the heart of nature near Virunga volcanoes.';
  $pageKeywords = 'homestay, Virunga, Rwanda, accommodation, travel, nature, volcanoes';
  $pageCss = ['room-cards.css','activity.css','home.css'];
  $pageHeroKey = null; // home has its own hero
  $pageScripts = ['home.js'];
  include 'includes/header.php';
?>

<!-- --- HERO -------------------------------------------------- -->
    <section class="hero" id="hero">
      <div class="slides" id="slides">
        <?php
        require_once __DIR__ . '/../config/db.php';
        $sqlHero = "SELECT * FROM hero_images WHERE status = 'active' AND is_active = 1 ORDER BY display_order ASC";
        $heroResult = $conn->query($sqlHero);

        if ($heroResult && $heroResult->num_rows > 0) {
            $isFirst = true;
            while ($hero = $heroResult->fetch_assoc()) {
                $activeClass = $isFirst ? ' active' : '';
                $isFirst = false;
                
                $title = htmlspecialchars($hero['title']);
                $paragraph = htmlspecialchars($hero['paragraph']);
                
                $imgVal = !empty($hero['image']) ? $hero['image'] : 'hero/1.jpg';
                if (strpos($imgVal, 'http') === 0) {
                    $imgAttr = $imgVal;
                } else {
                    $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                    if (strpos($imgVal, '/') === false) {
                        $imgVal = 'hero/' . $imgVal;
                    }
                    $imgAttr = './img/' . $imgVal;
                }
                
                echo '
                <div class="slide' . $activeClass . '">
                  <div class="slide-bg" style="background-image: url(\'' . htmlspecialchars($imgAttr) . '\');"></div>
                  <div class="slide-content">
                    <p class="slide-tag">Live the Virunga Experience</p>
                    <h1 class="slide-title">
                      ' . $title . '
                    </h1>
                    <p class="slide-desc">
                      ' . $paragraph . '
                    </p>
                    <div class="slide-actions">
                      <a href="#rooms" class="btn-primary">Explore Rooms</a>
                      <a href="#about" class="btn-outline">Our Story</a>
                    </div>
                  </div>
                </div>';
            }
        } else {
            // Fallback hero if DB is empty
            echo '
            <div class="slide active">
              <div class="slide-bg"></div>
              <div class="slide-content">
                <p class="slide-tag">Welcome to Virunga</p>
                <h1 class="slide-title">
                  Your <em>sanctuary</em> in the heart of nature
                </h1>
                <p class="slide-desc">
                  Nestled against the breathtaking Virunga volcanoes — wake up to
                  panoramic views and absolute tranquility.
                </p>
                <div class="slide-actions">
                  <a href="#rooms" class="btn-primary">Explore Rooms</a>
                  <a href="#about" class="btn-outline">Our Story</a>
                </div>
              </div>
            </div>';
        }
        ?>
      </div>
      <button
        class="arrow-btn arrow-prev"
        id="prevBtn"
        aria-label="Previous slide"
      >
        &#8592;
      </button>
      <button class="arrow-btn arrow-next" id="nextBtn" aria-label="Next slide">
        &#8594;
      </button>
      <div class="slider-controls" id="dotsWrap">
        <?php
        if (isset($heroResult) && $heroResult->num_rows > 0) {
            for ($d = 0; $d < $heroResult->num_rows; $d++) {
                $act = ($d === 0) ? ' active' : '';
                echo '<button class="slide-dot' . $act . '" aria-label="Slide ' . ($d + 1) . '"></button>';
            }
            $heroResult->data_seek(0);
        } else {
            echo '<button class="slide-dot active" aria-label="Slide 1"></button>';
        }
        ?>
      </div>
    </section>
    <!-- --- Stacked hero section (below slider) ----------------------- -->
    <!-- --- Home About (belt) --------------------------------------- -->
    <section id="home-about" class="reveal" data-reveal>
      <?php
      require_once __DIR__ . '/../config/db.php';
      $sqlAbout = "SELECT * FROM home_about LIMIT 1";
      $resAbout = $conn->query($sqlAbout);
      $about = $resAbout ? $resAbout->fetch_assoc() : null;

      $label = $about ? htmlspecialchars($about['label']) : 'Welcome to Virunga Homestay';
      $heading = $about ? htmlspecialchars($about['heading']) : 'Your Musanze basecamp for volcano sunrises, slow-evening fires, and effortless guided days.';
      $body = $about ? htmlspecialchars($about['body']) : 'Live inside a warm local home, wake to mountain air, and lean on accredited bilingual specialists for every trek, transfer, and taste of Rwanda, Uganda, or DRC. We blend heartfelt hosting with pro-level trip support so you can explore boldly and unwind completely.';
      
      $badge1 = ($about && !empty($about['badge_1'])) ? htmlspecialchars($about['badge_1']) : 'Family-run';
      $badge2 = ($about && !empty($about['badge_2'])) ? htmlspecialchars($about['badge_2']) : 'Tourist Info Centre';
      $badge3 = ($about && !empty($about['badge_3'])) ? htmlspecialchars($about['badge_3']) : 'Volcano & gorilla ready';

      $m1_num = $about ? (int)$about['metric_1_num'] : 12;
      $m1_suf = $about ? htmlspecialchars($about['metric_1_suffix']) : '+';
      $m1_lbl = $about ? htmlspecialchars($about['metric_1_label']) : 'Years hosting';

      $m2_num = $about ? (int)$about['metric_2_num'] : 840;
      $m2_suf = $about ? htmlspecialchars($about['metric_2_suffix']) : '';
      $m2_lbl = $about ? htmlspecialchars($about['metric_2_label']) : 'Stays curated';

      $m3_num = $about ? (int)$about['metric_3_num'] : 98;
      $m3_suf = $about ? htmlspecialchars($about['metric_3_suffix']) : '%';
      $m3_lbl = $about ? htmlspecialchars($about['metric_3_label']) : 'Guests recommend';
      ?>
      <div class="section-container about-grid">
        <div class="about-copy" data-reveal>
          <p class="section-label"><?php echo $label; ?></p>
          <h2 class="section-heading"><?php echo $heading; ?></h2>
          <p class="about-body"><?php echo $body; ?></p>
          <div class="about-badges">
            <?php if ($badge1) echo '<span>' . $badge1 . '</span>'; ?>
            <?php if ($badge2) echo '<span>' . $badge2 . '</span>'; ?>
            <?php if ($badge3) echo '<span>' . $badge3 . '</span>'; ?>
          </div>
        </div>
        <div class="about-card" data-reveal>
          <div class="about-card__metric">
            <span class="metric-num" data-count data-target="<?php echo $m1_num; ?>" <?php if($m1_suf) echo 'data-suffix="'.$m1_suf.'"'; ?>>0</span>
            <span class="metric-label"><?php echo $m1_lbl; ?></span>
          </div>
          <div class="about-card__metric">
            <span class="metric-num" data-count data-target="<?php echo $m2_num; ?>" <?php if($m2_suf) echo 'data-suffix="'.$m2_suf.'"'; ?>>0</span>
            <span class="metric-label"><?php echo $m2_lbl; ?></span>
          </div>
          <div class="about-card__metric">
            <span class="metric-num" data-count data-target="<?php echo $m3_num; ?>" <?php if($m3_suf) echo 'data-suffix="'.$m3_suf.'"'; ?>>0</span>
            <span class="metric-label"><?php echo $m3_lbl; ?></span>
          </div>
        </div>
      </div>
    </section>
    <section id="hero-pin">
      <div id="hero-sticky">
        <div class="stack-wrapper" id="stackWrapper">
          <div class="center-img" id="centerImg">
            <img src="./img/services/1.JPG" alt="Main interior" />
          </div>

          <div class="stack-img" id="sImg0">
            <img src="./img/services/2.jpg" alt="Living space" />
          </div>
          <div class="stack-img" id="sImg1">
            <img src="./img/services/3.jpg" alt="Bedroom" />
          </div>
          <div class="stack-img" id="sImg2">
            <img src="./img/services/4.jpg" alt="Kitchen" />
          </div>
          <div class="stack-img" id="sImg3">
            <img src="./img/services/1.JPG" alt="Reading nook" />
          </div>
          <div class="stack-img" id="sImg4">
            <img src="./img/hero/1.jpg" alt="Garden view" />
          </div>
          <div class="stack-img" id="sImg5">
            <img src="./img/hero/2.jpg" alt="Poolside" />
          </div>
          <div class="stack-img" id="sImg6">
            <img src="./img/hero/3.jpg" alt="Terrace" />
          </div>

          <div class="hero-title" id="heroTitle">
            <h1 style="color: #e08a2f; font-weight:700;">Your ComfortPlace</h1>
            <span style="color: #e08a2f; font-weight:700;" class="sub" id="heroSub">
              where warmth lives
            </span>
          </div>
        </div>
      </div>
    </section>

    <section id="experience" class="experience-section" data-reveal>
      <div class="section-container">
        <?php
        require_once __DIR__ . '/../config/db.php';
        $expItems = [];
        $resExp = $conn->query("SELECT * FROM home_experience WHERE status = 'active' ORDER BY display_order ASC, id ASC");
        if ($resExp && $resExp->num_rows > 0) {
            while ($rowExp = $resExp->fetch_assoc()) {
                $expItems[] = $rowExp;
            }
        }
        ?>
        <div class="experience-head" data-reveal>
          <p class="section-label">Curated Adventures</p>
          <h2 class="section-heading">Explore Our Signature Experiences</h2>
          <p class="experience-subtitle">Choose an experience to preview details, then continue to all activities.</p>
        </div>
        <div class="experience-list">
          <?php if (!empty($expItems)): ?>
            <?php foreach ($expItems as $idx => $exp): ?>
              <?php
              $expEyebrow = !empty($exp['eyebrow']) ? htmlspecialchars($exp['eyebrow']) : 'Explore Musanze through guided local insights and hidden cultural gems.';
              $expTitle = !empty($exp['title']) ? htmlspecialchars($exp['title']) : 'Virunga Gateway Experience';
              $expDescription = !empty($exp['description']) ? htmlspecialchars($exp['description']) : 'Easy access to adventures around the Virunga Massif region.';
              $expButtonText = !empty($exp['button_text']) ? htmlspecialchars($exp['button_text']) : 'Explore Experiences';
              $expButtonLink = !empty($exp['button_link']) ? htmlspecialchars($exp['button_link']) : (isset($baseLink) ? $baseLink('activity') : 'activity.php');
              $expImgVal = !empty($exp['image']) ? $exp['image'] : 'hero/2.jpg';
              if (strpos($expImgVal, 'http') === 0) {
                  $expImage = $expImgVal;
              } else {
                  $expImgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($expImgVal, '/'));
                  if (strpos($expImgVal, '/') === false) {
                      $expImgVal = 'activities/' . $expImgVal;
                  }
                  $expImage = './img/' . $expImgVal;
              }
              $expImage = htmlspecialchars($expImage);
              ?>
              <article class="experience-card<?php echo $idx === 0 ? ' is-active' : ''; ?>" data-reveal data-exp-card tabindex="0">
                <div class="experience-media">
                  <img src="<?php echo $expImage; ?>" alt="<?php echo $expTitle; ?>" />
                </div>
                <div class="experience-copy">
                  <p class="experience-eyebrow"><?php echo $expEyebrow; ?></p>
                  <h2 class="experience-title">
                    <?php echo $expTitle; ?>
                  </h2>
                  <p class="experience-body"><?php echo $expDescription; ?></p>
                  <a class="btn-primary experience-cta" href="<?php echo $expButtonLink; ?>">
                    <i class="fa-solid fa-arrow-right"></i> <?php echo $expButtonText; ?>
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          <?php else: ?>
            <article class="experience-card is-active" data-reveal data-exp-card tabindex="0">
              <div class="experience-media">
                <img src="./img/hero/2.jpg" alt="Virunga Gateway Experience" />
              </div>
              <div class="experience-copy">
                <p class="experience-eyebrow">Explore Musanze through guided local insights and hidden cultural gems.</p>
                <h2 class="experience-title">Virunga Gateway Experience</h2>
                <p class="experience-body">Easy access to adventures around the Virunga Massif region.</p>
                <a class="btn-primary experience-cta" href="<?php echo isset($baseLink) ? $baseLink('activity') : 'activity.php'; ?>">
                  <i class="fa-solid fa-arrow-right"></i> Explore Experiences
                </a>
              </div>
            </article>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- ══ ROOMS ══ -->
    <section id="rooms">
      <div class="section-container">
        <h2 class="section-heading" id="roomsHeading">Our Stay Experience</h2>
        <p class="section-sub rooms-intro" data-reveal>
          At Virunga Homestay, we welcome you to a space of comfort, warmth, and authentic local living. Our private rooms offer a peaceful retreat after your Virunga adventures, within a genuine home environment where every guest feels at home.
        </p>
        <div class="rooms-grid" id="roomsGrid">
            <?php
            require_once __DIR__ . '/../config/db.php';
            $sql = "SELECT * FROM rooms WHERE status = 'active' LIMIT 3";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $i = 0;
                $tags = ['Most Popular', 'New', 'Suite'];
                while ($row = $result->fetch_assoc()) {
                    $imgVal = !empty($row['image']) ? $row['image'] : 'services/1.JPG';
                    if (strpos($imgVal, 'http') === 0) {
                        $image = $imgVal;
                    } else {
                        $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                        if (strpos($imgVal, '/') === false) {
                            $imgVal = 'rooms/' . $imgVal;
                        }
                        $image = './img/' . $imgVal;
                    }
                    $image = htmlspecialchars($image);
                    $title = htmlspecialchars($row['title']);
                    $meters = isset($row['meters']) ? (int)$row['meters'] : 0;
                    $guests = isset($row['guest_number']) ? (int)$row['guest_number'] : 2;
                    $bed = isset($row['bed_type']) ? htmlspecialchars($row['bed_type']) : 'King Bed';
                    $price = isset($row['price']) ? (int)$row['price'] : 0;
                    $tag = isset($tags[$i]) ? $tags[$i] : 'Premium';
                    $i++;
                    
                    $whatsappMsg = rawurlencode("Hello Francis! I'm interested in the {$title}. Could you share availability and rates?");
                    
                    echo '
                    <div class="room-card">
                      <img class="room-card__img" src="' . $image . '" alt="' . $title . '" />
                      <div class="room-card__overlay"></div>
                      <span class="room-card__tag">' . $tag . '</span>
                      <div class="room-card__content">
                        <h3 class="room-card__name">' . $title . '</h3>
                        <div class="room-card__meta">
                          <span><i class="fa-solid fa-maximize"></i> ' . $meters . ' m²</span>
                          <span><i class="fa-solid fa-user-group"></i> ' . $guests . ' Guests</span>
                          <span><i class="fa-solid fa-bed"></i> ' . $bed . '</span>
                        </div>
                        <div class="room-card__price">
                          <span class="amount">$' . $price . '</span>
                          <span class="per">/ night</span>
                        </div>
                      </div>
                      <a class="room-card__cta" href="' . (isset($baseLink) ? $baseLink('bookinginfo') : 'bookinginfo.php') . '">Book Now <i class="fa-solid fa-arrow-right fa-xs"></i></a>
                    </div>';
                }
            } else {
                echo '<p style="grid-column: 1 / -1; text-align: center;">No rooms available at the moment.</p>';
            }
            ?>
          </div>
        </div>
        <div class="rooms-actions" id="roomsActions">
          <a class="btn-primary" href="<?php echo isset($baseLink) ? $baseLink('rooms') : 'rooms.php'; ?>" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
           <i class="fa-solid fa-grid-2 fa-xs"></i> View All Rooms
          </a>
          <a class="btn-ghost" href="<?php echo isset($baseLink) ? $baseLink('bookinginfo') : 'bookinginfo.php'; ?>" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
            Booking Information
          </a>
        </div>
      </div>
    </section>

    <!-- ══ ACTIVITIES ══ -->
    <section id="activities">
      <div class="section-container">
        <div class="section-label">Our Community Experiences</div>
        <div class="activities-header">
          <h2 class="section-heading">Experience Life at Virunga Homestay</h2>
          <p class="section-sub">
            Guided walks, local cuisine, culture, and nature unforgettable moments await.
          </p>
        </div>
        <div class="activities-grid">
          <?php
          require_once __DIR__ . '/../config/db.php';
          $sqlAct = "SELECT * FROM activities WHERE status = 'active' LIMIT 3";
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
          <a class="btn-primary" href="<?php echo isset($baseLink) ? $baseLink('activity') : 'activity.php'; ?>" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
           <i class="fa-solid fa-grid-2 fa-xs"></i> Show More Activities
          </a>
        </div>
      </div>
    </section>

    <!-- ══ WHY CHOOSE US ══ -->
    <section id="why">
      <div class="section-container">
        <h2 class="section-heading" id="whyHeading">
          Why choose Virunga Homestay as your experience
        </h2>
        <div class="experience-intro" data-reveal>
          <p class="experience-intro__kicker">Virunga Homestay - Live the Virunga Experience</p>
          <p class="experience-intro__point is-active" data-intro-point tabindex="0">
            Choose Virunga Homestay because it is more than a place to stay - it is a real home experience in Musanze where you are welcomed like family and immersed in daily local life.
          </p>
          <p class="experience-intro__point" data-intro-point tabindex="0">
            You don't just visit the Virunga region; you live it. From shared meals and authentic conversations to cultural moments with your host, every stay is designed to feel personal, warm, and meaningful.
          </p>
          <p class="experience-intro__point" data-intro-point tabindex="0">
            This is the difference: instead of a standard accommodation, you get a guided way of experiencing the Virunga through people, stories, and connection.
          </p>
        </div>
        <div class="why-grid" id="whyGrid">
          <?php
          require_once __DIR__ . '/../config/db.php';
          $sqlWhy = "SELECT * FROM home_why WHERE status = 'active' ORDER BY display_order ASC";
          $resWhy = $conn->query($sqlWhy);
          
          if ($resWhy && $resWhy->num_rows > 0) {
              while ($rowWhy = $resWhy->fetch_assoc()) {
                  $icon = htmlspecialchars($rowWhy['icon']);
                  $title = htmlspecialchars($rowWhy['title']);
                  $bodyText = htmlspecialchars($rowWhy['body']);
                  
                  echo '
                  <div class="why-card">
                    <div class="why-icon"><i class="' . $icon . '"></i></div>
                    <h3 class="why-title">' . $title . '</h3>
                    <p class="why-body">' . $bodyText . '</p>
                  </div>';
              }
          }
          ?>
        </div>
      </div>
    </section>
     <!-- Parallax Image Section -->
    <section class="parallax-section parallax-food">
      <div class="parallax-overlay">
        <div class="parallax-content">
          <div>
            <h2 class="parallax-title">Experience Local Cuisine</h2>
            <p class="parallax-text">
              Savor authentic Rwandan dishes prepared with locally-sourced ingredients
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- ══ TESTIMONIAL TEASER ══ -->
    <section id="testimonials">
      <div class="section-container">
        <div class="testi-card">
          <div class="testi-copy">
            <p class="section-label">What Our Guests Say</p>
            <h2 class="section-heading">Stories from the Virunga Experience</h2>
          </div>
          <a
            class="btn-primary testi-cta"
            href="https://www.tripadvisor.com/Hotel_Review-g317075-d20326735-Reviews-Virunga_Homestay-Ruhengeri_Musanze_District_Northern_Province.html"
            target="_blank"
            rel="noopener"
          >
            <i class="fa-brands fa-tripadvisor"></i> Review Us on TripAdvisor
          </a>
        </div>
      </div>
    </section>

    <!-- REAL GUEST STORIES -->
    <section id="guest-reviews">
      <div class="section-container">
        <p class="section-label">Real Stories from Travelers</p>
        <h2 class="section-heading">Virunga Homestay Experiences</h2>
        <p class="guest-reviews__lead">
          Every stay at Virunga Homestay becomes a story worth sharing. Here is what our guests have experienced in their own words:
        </p>

        <div class="guest-reviews__grid">
          <article class="guest-review-card" data-reveal>
            <h3>A Dream Stay in Musanze</h3>
            <p class="guest-review-card__source">Tripadvisor Review</p>
            <p>
              John, Emmy, and the whole team made our stay unforgettable. Amazing volcano views, thoughtful service, and help with park trips Virunga Homestay comes highly recommended!
            </p>
          </article>

          <article class="guest-review-card" data-reveal>
            <h3>Our Honeymoon Stay at Virunga Homestay</h3>
            <p class="guest-review-card__source">Tripadvisor Review</p>
            <p>
              Our honeymoon stay at Virunga Homestay last week was a truly remarkable experience. The warm welcome, the peaceful setting, and the personal touches made us feel right at home from the very first day. As part of our celebration, we were gifted a once-in-a-lifetime gorilla visit....
            </p>
          </article>

          <article class="guest-review-card" data-reveal>
            <h3>Unforgettable Moments at Virunga Homestay!</h3>
            <p class="guest-review-card__source">Tripadvisor Review</p>
            <p>
              Welcomed like family, we cooked traditional dishes, explored local farms, and made banana beer. Evenings were full of laughter, music, and delicious meals. A true Rwandan experience with lasting memories highly recommended in Musanze!
            </p>
          </article>

          <article class="guest-review-card" data-reveal>
            <h3>Feel Rwanda, Not Just Visit</h3>
            <p class="guest-review-card__source">Tripadvisor Review</p>
            <p>
              An unforgettable 10-day, 11-night journey through Rwanda was made exceptional by Emmy’s professionalism and warm hospitality from Virunga Homestay. His deep knowledge and flexibility enriched every moment, turning the trip into an inspiring exploration of Rwanda’s culture and landscapes. With four nights in a clean, safe, and welcoming homestay that felt like home, the experience was deeply rewarding highly recommended for those seeking authentic connection and lasting memories.
            </p>
          </article>
        </div>

        <div class="guest-reviews__summary" data-reveal>
          <h3>More Than a Stay - A Real Connection</h3>
          <p>
            Virunga Homestay offers more than accommodation. It is a living experience in the heart of Musanze, where guests are welcomed into a real home and local way of life.
          </p>
          <div class="guest-reviews__points">
            <span>Authentic Rwandan hospitality in a family home</span>
            <span>Personal guidance from knowledgeable local hosts</span>
            <span>Cultural immersion through food, stories, and daily life</span>
            <span>Easy access to the Virunga Massif region and surrounding attractions</span>
          </div>
          <p>
            Every visit is supported by a warm, locally rooted hosting experience designed to make your journey meaningful, comfortable, and unforgettable.
          </p>
          <h4>Why Travelers Choose Virunga Homestay</h4>
          <p class="guest-reviews__belonging">
            Because they are not just looking for a place to stay - they are looking for a place to belong.
          </p>
        </div>
      </div>
    </section>

    <!-- WHATSAPP FLOATING BUTTON -->
    <a
      href="https://wa.me/250781234567"
      target="_blank"
      rel="noopener noreferrer"
      class="wa-float"
      aria-label="Chat on WhatsApp"
    >
      <div class="wa-float__pulse"></div>
      <div class="wa-float__icon"><i class="fa-brands fa-whatsapp"></i></div>
      <span class="wa-float__label">Chat with us</span>
    </a>
    <?php include 'includes/footer.php'; ?>




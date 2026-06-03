<?php
  $pageTitle = 'About Virunga Homestay - Our Story and Philosophy';
  $pageDescription = 'Discover the story behind Virunga Homestay. Learn about our philosophy of making every guest feel at home in the heart of Rwanda.';
  $pageKeywords = 'about Virunga Homestay, Rwanda homestay story, hospitality philosophy, Amara Nkosi';
  $pageCss = ['page-hero.css','about.css'];
  $pageHeroKey = 'about';
  $pageScripts = ['about.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>
<div id="about-page">
      <!-- ── SECTION 1: MANIFESTO ─────────────────────────────────── -->
      <section class="s-manifesto">
        <div class="container">
          <div class="manifesto-inner">
            <div class="manifesto-text">
              <blockquote
                class="philosophy-quote reveal"
                style="
                  font-size: clamp(1.1rem, 2.2vw, 1.6rem);
                  margin-bottom: var(--space-5);
                  max-width: 52ch;
                "
              >
                The best memories aren't made in hotels they're made in homes
                where someone was genuinely glad you came.
              </blockquote>
              <div
                class="philosophy-author reveal"
                style="margin-bottom: var(--space-6)"
              >
                Bodain Nshizirungu, Founder
              </div>
              <div class="section-label reveal">
                <i class="fa-solid fa-house-chimney"></i> Our Story
              </div>
              <h1 class="manifesto-headline">
                <span class="line-stagger"
                  ><span>Every guest feels <em>at</em> home.</span></span
                >
              </h1>
              <p class="manifesto-body reveal">
                Virunga Homestay offers an authentic, organized, and safe Rwanda home experience in Musanze, combining warm local hospitality, cultural connection, and guided nature-based activities in the Virunga region. Guests stay in a real home, engage with local life, and explore northern Rwanda through meaningful, well-structured experiences.
              </p>
              <a href="#story" class="manifesto-cta reveal">
                Discover our journey <i class="fa-solid fa-arrow-right"></i>
              </a>
            </div>

            <div class="manifesto-visual reveal-right">
              <div class="card-stack">
                <div class="card card-1">
                  <div class="card-inner">
                    <div class="card-icon-badge">
                      <i class="fa-solid fa-star"></i>
                    </div>
                  </div>
                </div>
                <div class="card card-2">
                  <div class="card-inner">
                    <div class="card-icon-badge">
                      <i class="fa-solid fa-heart"></i>
                    </div>
                    <div class="card-stat-big">600+</div>
                    <div class="card-stat-label">Families Hosted</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── SECTION 2: SCROLL FRAMES ──────────────────────────────── -->
      <!-- s-video-scroll is 400vh tall — the sticky child sticks inside it -->
      <section class="s-video-scroll" id="videoSection">
        <div class="video-sticky-wrap" id="videoSticky">
          <canvas id="scrollCanvas" class="video-bg"></canvas>
          <div class="video-fallback"></div>
          <div class="video-overlay"></div>

          <!-- Words that appear at different scroll positions -->
          <div class="video-words">
            <div class="video-word" data-word="0">
              <em>Feel</em> the warmth<br />of a real home
            </div>
            <div class="video-word" data-word="1">
              Every corner<br />holds a <em>memory</em>
            </div>
            <div class="video-word" data-word="2">
              <em>Stories</em> begin<br />at our table
            </div>
            <div class="video-word" data-word="3">
              This is<br />where you <em>belong</em>
            </div>
          </div>

          <!-- Progress dots -->
          <div class="video-progress">
            <div class="vp-dot active"></div>
            <div class="vp-dot"></div>
            <div class="vp-dot"></div>
            <div class="vp-dot"></div>
          </div>
        </div>
      </section>

      <!-- ── SECTION 3: TIMELINE ──────────────────────────────────── -->
      <!-- REFINED ABOUT CONTENT -->
      <section class="s-about-refined">
        <div class="container">
          <div class="about-refined-head reveal">
            <div class="section-label">
              About Virunga Homestay
            </div>
            <h2>Live the Virunga Experience</h2>
          </div>

          <div class="about-refined-grid">
            <article class="about-refined-card reveal">
              <h3>Who We Are</h3>
              <p>
                Virunga Homestay is an authentic home experience located in Musanze, at the gateway to the Virunga volcanoes in northern Rwanda. We open our doors to travelers who wish to experience Rwanda not as visitors, but through real daily life in a welcoming local home.
              </p>
              <p>
                We are a home experience - rooted in culture, connection, and genuine hospitality.
              </p>
            </article>

            <article class="about-refined-card about-refined-card--primary reveal">
              <h3>Why We Exist</h3>
              <p>
                We exist to transform the way people experience Rwanda by moving beyond traditional accommodation. Our purpose is to create meaningful connections between travelers and local life through immersive, human-centered home experiences.
              </p>
            </article>

            <article class="about-refined-card about-refined-card--primary reveal">
              <h3>What We Offer</h3>
              <p>
                We provide carefully designed home experiences that include:
              </p>
              <ul>
                <li>A comfortable stay in a real local home</li>
                <li>Shared home-cooked meals with your host</li>
                <li>Cultural exchange and storytelling moments</li>
                <li>Guided local experiences in Musanze and the Virunga region</li>
              </ul>
              <p>Each stay is simple, authentic, and personally hosted.</p>
            </article>

            <article class="about-refined-card reveal">
              <h3>Why We Are Different</h3>
              <p>
                Virunga Homestay is defined by experience, not accommodation.
              </p>
              <p>
                We do not simply offer rooms - we offer connection. Every guest becomes part of a living home environment where interaction, culture, and everyday life create a truly immersive stay.
              </p>
            </article>

            <article class="about-refined-card reveal">
              <h3>Trust & Hospitality</h3>
              <p>
                We are committed to providing a safe, well-organized, and welcoming environment for every guest. Our hospitality is personal, attentive, and rooted in respect, ensuring comfort and peace of mind throughout your stay.
              </p>
            </article>

            <article class="about-refined-card about-refined-card--primary reveal">
              <h3>Community & Sustainability</h3>
              <p>
                We are deeply committed to the well-being of our local community in Musanze. By choosing to stay with us, you directly support local livelihoods and contribute to community-led initiatives.
              </p>
              <p>
                We prioritize sustainable practices and promote cultural preservation, ensuring that your journey leaves a positive footprint on both the environment and the people of the Virunga.
              </p>
            </article>

            <article class="about-refined-card about-refined-card--closing reveal">
              <p>
                At Virunga Homestay, you don't just visit Rwanda - you live it through real people, real stories, and real home experiences.
              </p>
              <p><strong>Welcome to your home in the Virunga.</strong></p>
            </article>
          </div>
        </div>
      </section>

            <!-- ── SECTION: WHY CHOOSE US ────────────────────────────────── -->
      <section id="why" class="s-why">
        <div class="container">
          <h2 class="section-heading reveal" style="text-align: center; margin-bottom: var(--space-8);">
            Why choose Virunga Homestay as your experience
          </h2>
          <div class="experience-intro reveal">
            <p class="experience-intro__kicker">Virunga Homestay - Live the Virunga Experience</p>
            <p class="experience-intro__point is-active">
              Choose Virunga Homestay because it is more than a place to stay - it is a real home experience in Musanze where you are welcomed like family and immersed in daily local life.
            </p>
            <p class="experience-intro__point">
              You don't just visit the Virunga region; you live it. From shared meals and authentic conversations to cultural moments with your host, every stay is designed to feel personal, warm, and meaningful.
            </p>
            <p class="experience-intro__point">
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
                    <div class="why-card reveal">
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

      <!-- ── SECTION 4: VALUES BENTO ──────────────────────────────── -->
      <section class="s-values">
        <div class="container">
          <div class="values-header">
            <div class="section-label reveal" style="justify-content: center">
              <i class="fa-solid fa-compass"></i> What we stand for
            </div>
            <h2 class="values-title reveal">
              Principles that guide <em>every single stay</em>
            </h2>
          </div>

          <div class="bento-grid">
            <div class="bento-card bc-1 accent reveal">
              <div class="bento-inner">
                <div>
                  <div class="bento-icon">
                    <i class="fa-solid fa-heart"></i>
                  </div>
                  <div class="bento-label">Core Value</div>
                  <div class="bento-title">
                    Hospitality is not a service it's a feeling we create together
                  </div>
                </div>
                <div>
                  <p class="bento-text">
                    We train every host not just in comfort, but in empathy. The
                    warmth you feel when you walk through our doors isn't an
                    accident. It's a deeply intentional choice made every single
                    day by people who genuinely care about you.
                  </p>
                </div>
              </div>
            </div>

            <div class="bento-card bc-2 dark reveal">
              <div class="bento-icon"><i class="fa-solid fa-leaf"></i></div>
              <div class="bento-label">Sustainability</div>
              <div class="bento-title">Rooted in responsibility</div>
              <p class="bento-text">
                Local ingredients. Minimal waste. Spaces that give back to the
                communities they belong to.
              </p>
              <div class="bento-number">01</div>
            </div>

            <div class="bento-card bc-3 reveal">
              <div class="bento-icon">
                <i class="fa-solid fa-lock-open"></i>
              </div>
              <div class="bento-label">Authenticity</div>
              <div class="bento-title">Real homes, real people</div>
              <p class="bento-text">
                No staged decor, no rehearsed welcome speeches just genuine
                homes and genuine hosts.
              </p>
              <div class="bento-number">02</div>
            </div>

            <div class="bento-card bc-4 reveal">
              <div class="bento-icon">
                <i class="fa-solid fa-shield-halved"></i>
              </div>
              <div class="bento-label">Safety</div>
              <div class="bento-title">Peace of mind, always</div>
              <p class="bento-text">
                Every host is vetted. Every stay is insured. Your comfort and
                security are never negotiable.
              </p>
              <div class="bento-number">03</div>
            </div>

            <div class="bento-card bc-5 reveal">
              <div class="bento-icon">
                <i class="fa-solid fa-earth-africa"></i>
              </div>
              <div class="bento-label">Culture</div>
              <div class="bento-title">Travel that transforms</div>
              <p class="bento-text">
                We believe the deepest travel experiences happen around a family
                table, not a hotel lobby.
              </p>
              <div class="bento-number">04</div>
            </div>
          </div>
        </div>
      </section>


      <!-- ── SECTION 5: STATS MARQUEE ─────────────────────────────── -->
      <section class="s-stats">
        <div class="stats-marquee-wrap">
          <div class="stats-marquee" id="marquee1">
            <div class="stat-item">
              <div class="stat-number">600+</div>
              <div class="stat-info">
                <span class="stat-label">Families</span>
                <span class="stat-desc">hosted with love</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">4.9★</div>
              <div class="stat-info">
                <span class="stat-label">Rating</span>
                <span class="stat-desc">average guest score</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">48</div>
              <div class="stat-info">
                <span class="stat-label">Hosts</span>
                <span class="stat-desc">across the country</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">6+</div>
              <div class="stat-info">
                <span class="stat-label">Years</span>
                <span class="stat-desc">of real hospitality</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">98%</div>
              <div class="stat-info">
                <span class="stat-label">Return Rate</span>
                <span class="stat-desc">guests come back</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <!-- Duplicate for seamless loop -->
            <div class="stat-item">
              <div class="stat-number">600+</div>
              <div class="stat-info">
                <span class="stat-label">Families</span>
                <span class="stat-desc">hosted with love</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">4.9★</div>
              <div class="stat-info">
                <span class="stat-label">Rating</span>
                <span class="stat-desc">average guest score</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">48</div>
              <div class="stat-info">
                <span class="stat-label">Hosts</span>
                <span class="stat-desc">across the country</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">6+</div>
              <div class="stat-info">
                <span class="stat-label">Years</span>
                <span class="stat-desc">of real hospitality</span>
              </div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">98%</div>
              <div class="stat-info">
                <span class="stat-label">Return Rate</span>
                <span class="stat-desc">guests come back</span>
              </div>
            </div>
            <div class="stat-divider"></div>
          </div>
        </div>
      </section>

      <!-- ── SECTION: PARALLAX CUISINE ─────────────────────────────── -->
      <section class="parallax-section parallax-food">
        <div class="parallax-overlay">
          <div class="parallax-content">
            <div>
              <h2 class="parallax-title reveal">A Taste of Rwanda, Shared at Home</h2>
              <p class="parallax-text reveal">
                Enjoy traditional Rwandan dishes prepared with fresh local ingredients and served in the warmth of our family home.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ── SECTION 6: CTA ────────────────────────────────────────── -->
      <section class="s-cta">
        <div class="container">
          <h2 class="cta-title reveal">Ready to feel<br /><em>at home?</em></h2>
          <p class="cta-subtitle reveal">
            Whether you're a traveller looking for a real local experience, or a
            host ready to open your door we'd love to welcome you.
          </p>
          <div class="cta-buttons reveal">
            <a href="#" class="btn-primary"> Book A Stay </a>
          </div>
        </div>
      </section>
    
<?php include 'includes/footer.php'; ?>


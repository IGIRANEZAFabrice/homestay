    <section class="imigongo-strip" aria-hidden="true"></section>

<footer class="footer">
      <!-- Imigongo: top-right -->
      <svg
        class="imigongo imigongo--tl"
        viewBox="0 0 280 280"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <defs>
          <style>
            .ig {
              fill: var(--color-primary);
            }
            .ig2 {
              fill: var(--color-primary-dark);
            }
            .ig3 {
              fill: var(--color-primary-light);
            }
          </style>
        </defs>
        <!-- Imigongo: bold angular spiral/geometric blocks -->
        <!-- Outer triangles -->
        <polygon class="ig" points="0,0 280,0 280,280" />
        <polygon class="ig2" points="0,0 260,0 0,260" />
        <!-- Diagonal checker bands -->
        <polygon class="ig3" points="280,0 280,60 220,0" />
        <polygon class="ig" points="280,60 280,120 160,0 220,0" />
        <polygon class="ig2" points="280,120 280,180 100,0 160,0" />
        <polygon class="ig3" points="280,180 280,240 40,0 100,0" />
        <polygon class="ig" points="280,240 280,280 0,0 40,0" />
        <!-- inner spiral squares -->
        <rect
          class="ig2"
          x="180"
          y="0"
          width="30"
          height="30"
          transform="rotate(45 195 15)"
        />
        <rect
          class="ig3"
          x="220"
          y="40"
          width="20"
          height="20"
          transform="rotate(45 230 50)"
        />
        <rect
          class="ig"
          x="150"
          y="150"
          width="40"
          height="40"
          transform="rotate(45 170 170)"
        />
        <rect
          class="ig2"
          x="195"
          y="195"
          width="25"
          height="25"
          transform="rotate(45 207 207)"
        />
        <!-- row of triangles -->
        <polygon class="ig3" points="0,280 40,240 80,280" />
        <polygon class="ig2" points="80,280 120,240 160,280" />
        <polygon class="ig3" points="160,280 200,240 240,280" />
        <polygon class="ig" points="240,280 280,240 280,280" />
        <!-- interlocking L-shapes -->
        <polyline
          class="ig"
          fill="none"
          stroke="var(--color-primary-light)"
          stroke-width="4"
          points="20,260 20,230 50,230 50,200 80,200 80,170 110,170"
        />
        <polyline
          class="ig"
          fill="none"
          stroke="var(--color-primary-dark)"
          stroke-width="3"
          points="260,20 230,20 230,50 200,50 200,80 170,80 170,110"
        />
      </svg>

      <!-- Imigongo: bottom-left -->
      <svg
        class="imigongo imigongo--br"
        viewBox="0 0 240 240"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <polygon class="ig" points="0,0 240,0 240,240" />
        <polygon class="ig2" points="0,0 220,0 0,220" />
        <polygon class="ig3" points="240,0 240,50 190,0" />
        <polygon class="ig" points="240,50 240,100 140,0 190,0" />
        <polygon class="ig2" points="240,100 240,150 90,0 140,0" />
        <polygon class="ig3" points="240,150 240,200 40,0 90,0" />
        <polygon class="ig" points="240,200 240,240 0,0 40,0" />
        <rect
          class="ig3"
          x="150"
          y="130"
          width="36"
          height="36"
          transform="rotate(45 168 148)"
        />
        <rect
          class="ig2"
          x="188"
          y="168"
          width="22"
          height="22"
          transform="rotate(45 199 179)"
        />
        <polygon class="ig3" points="0,240 35,205 70,240" />
        <polygon class="ig2" points="70,240 105,205 140,240" />
        <polygon class="ig3" points="140,240 175,205 210,240" />
        <polygon class="ig" points="210,240 240,210 240,240" />
        <polyline
          fill="none"
          stroke="var(--color-primary-light)"
          stroke-width="3.5"
          points="15,225 15,198 42,198 42,171 69,171 69,144 96,144"
        />
        <polyline
          fill="none"
          stroke="var(--color-primary-dark)"
          stroke-width="3"
          points="225,15 198,15 198,42 171,42 171,69 144,69 144,96"
        />
      </svg>

      <!-- ── GRID ── -->
      <div class="footer__inner">
        <!-- BRAND -->
        <div class="footer__col footer__brand">
          <img
            src="./img/logo/logo.png"
            alt="Virunga Homestay"
            class="footer__logo"
          />
          <p class="footer__tagline">Where Virunga Becomes Personal.</p>
          <p class="footer__desc">
           Live the Virunga in a real home through people, stories, and shared moments that stay with you.
          </p>

          <div class="footer__badges">
            <span class="badge"
              ><i class="fa-solid fa-clock"></i> Est. 2020</span
            >
          </div>
        </div>

        <!-- EXPLORE -->
        <div class="footer__col">
          <h3 class="footer__col-title">Explore</h3>
          <ul class="footer__links">
            <li><a href="<?php echo $baseLink('home'); ?>">Home</a></li>
            <li><a href="<?php echo $baseLink('about'); ?>">Our Story</a></li>
            <li><a href="<?php echo $baseLink('rooms'); ?>">Our Stays</a></li>
            <li><a href="<?php echo $baseLink('shop'); ?>">Shop</a></li>
            <li><a href="<?php echo $baseLink('cars'); ?>">Car Rent</a></li>
            <li><a href="<?php echo $baseLink('activities'); ?>">Experiences</a></li>
             <li><a href="<?php echo $baseLink('blog'); ?>">Blogs</a></li>
          </ul>
        </div>

        <!-- GUEST INFO -->
        <div class="footer__col">
          <h3 class="footer__col-title">Guest Info</h3>
          <ul class="footer__links">
            <li><a href="<?php echo $baseLink('rooms'); ?>">Book a Stay</a></li>
             <li><a href="<?php echo $baseLink('contact#location'); ?>">Location</a></li>
            <li><a href="<?php echo $baseLink('houserules'); ?>">Check-in / Check-out</a></li>
            <li><a href="<?php echo $baseLink('houserules'); ?>">Our Homestay Rules</a></li>
            <li><a href="<?php echo $baseLink('houserules'); ?>">FAQ</a></li>
            <li><a href="https://www.tripadvisor.com/Hotel_Review-g317075-d20326735-Reviews-Virunga_Homestay_Live_the_Virunga_Experience-Ruhengeri_Musanze_District_Northern_Prov.html">Testimonials</a></li>
          </ul>
        </div>

        <!-- CONTACT -->
        <div class="footer__col">
          <h3 class="footer__col-title">Contact</h3>
          <div class="footer__contact">
            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa-solid fa-location-dot"></i>
              </div>
              <div class="contact-text">
                <span class="contact-label">Address</span>
                <span class="contact-value"
                  >Musanze, Rwanda</span
                >
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
              <div class="contact-text">
                <span class="contact-label">Phone / WhatsApp</span>
                <span class="contact-value"
                  ><a href="tel:+250784513435">+250 784 513 435</a></span
                >
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="fa-solid fa-envelope"></i>
              </div>
              <div class="contact-text">
                <span class="contact-label">Email</span>
                <span class="contact-value"
                  ><a href="mailto:info@virungahomestay.com"
                    >info@virungahomestay.com</a
                  ><br/>
                  <a href="mailto:virungahomestay@gmail.com"
                    >virungahomestay@gmail.com</a
                  ></span
                >
              </div>
            </div>
          </div>

          <!-- Social -->
          <div class="footer__social">
            <a href="" class="social-link" aria-label="Facebook"
              ><i class="fa-brands fa-facebook-f"></i
            ></a>
            <a href="" class="social-link" aria-label="Instagram"
              ><i class="fa-brands fa-instagram"></i
            ></a>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="footer__divider"><hr /></div>

      <!-- Bottom bar -->
      <div class="footer__bottom">
        <p class="footer__copy">
          © 2025 <span>Virunga Homestay</span>. All rights reserved.</p>
        <ul class="footer__bottom-links">
          <li><a href="<?php echo $baseLink('privacy'); ?>">Privacy Policy</a></li>
          <li><a href="<?php echo $baseLink('privacy'); ?>">Terms of Service</a></li>
          <li><a href="<?php echo $baseLink('privacy'); ?>">Cookie Policy</a></li>
        </ul>
      </div>
    </footer>

    <!-- ── WHATSAPP FLOATING BUTTON ─────────────────────────── -->
    <a
      href="https://wa.me/250784513435"
      target="_blank"
      rel="noopener noreferrer"
      class="wa-float"
      aria-label="Chat on WhatsApp"
    >
      <div class="wa-float__pulse"></div>
      <div class="wa-float__icon"><i class="fa-brands fa-whatsapp"></i></div>
      <span class="wa-float__label">Chat with us</span>
    </a>
    <!-- SCRIPTS -->
    <script src="<?php echo $basePath; ?>/js/main.js"></script>
    <script src="<?php echo $basePath; ?>/js/count.js"></script>
    <?php if (!empty($pageScripts) && is_array($pageScripts)) foreach ($pageScripts as $js): ?>
      <script src="<?php echo $basePath; ?>/js/<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
  </body>
</html>


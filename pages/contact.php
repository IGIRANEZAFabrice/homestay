<?php
  require_once __DIR__ . '/../config/db.php';
  $pageTitle = 'Virunga Homestay - Contact Us';
  $pageCss = ['page-hero.css','contact.css'];
  $pageHeroKey = 'contact';
  $pageScripts = ['contact.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<section class="contact-section">
      <div class="contact-left">
        <p class="section-label reveal">Reach Out</p>

        <h2 class="contact-heading reveal reveal-delay-1">
          We'd love to<br />hear from <em>you</em>
        </h2>

        <p class="contact-intro reveal reveal-delay-2">
          Whether you're planning a stay, have a question about our services, or
          simply want to know more about the Virunga region - our team is here
          and happy to help.
        </p>

        <div class="info-cards">
          <div class="info-card reveal reveal-delay-1">
            <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="info-text">
              <p class="info-label">Location</p>
              <p class="info-value">
                Musanze District, Northern Province<br />Rwanda, near Volcanoes
                National Park
              </p>
            </div>
          </div>

          <div class="info-card reveal reveal-delay-2">
            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="info-text">
              <p class="info-label">Phone &amp; WhatsApp</p>
              <p class="info-value">
                <a href="tel:+250784513435">+250 784 513 435</a><br />
                <a href="https://wa.me/250784513435">Chat on WhatsApp &#8599;</a>
              </p>
            </div>
          </div>

          <div class="info-card reveal reveal-delay-3">
            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="info-text">
              <p class="info-label">Email</p>
              <p class="info-value">
                <a href="mailto:virungahomestay@gmail.com">virungahomestay@gmail.com</a>
              </p>
            </div>
          </div>

          <div class="info-card reveal reveal-delay-4">
            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
            <div class="info-text">
              <p class="info-label">Office Hours</p>
              <p class="info-value">
                Mon - Fri: 8 am - 6 pm<br />Sat - Sun: 9 am - 4 pm (EAT)
              </p>
            </div>
          </div>
        </div>

        <div class="social-row reveal reveal-delay-5">
          <a href="#" class="social-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="social-btn" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="social-btn" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>

      <div class="form-card reveal scale-up reveal-delay-2">
        <h3 class="form-title">Send us a <em>message</em></h3>
        <p class="form-sub">We reply within 24 hours.</p>

        <form id="contactForm" novalidate>
          <div class="form-grid">
            <div class="form-group">
              <input
                type="text"
                id="fname"
                name="fname"
                placeholder=" "
                autocomplete="given-name"
              />
              <label for="fname">First Name</label>
              <span class="error-msg">Please enter your first name</span>
            </div>

            <div class="form-group">
              <input
                type="text"
                id="lname"
                name="lname"
                placeholder=" "
                autocomplete="family-name"
              />
              <label for="lname">Last Name</label>
              <span class="error-msg">Please enter your last name</span>
            </div>

            <div class="form-group">
              <input
                type="email"
                id="email"
                name="email"
                placeholder=" "
                autocomplete="email"
              />
              <label for="email">Email Address</label>
              <span class="error-msg">Enter a valid email</span>
            </div>

            <div class="form-group">
              <input
                type="tel"
                id="phone"
                name="phone"
                placeholder=" "
                autocomplete="tel"
              />
              <label for="phone">Phone (optional)</label>
            </div>

            <div class="form-group full select-wrap">
              <select id="subject" name="subject">
                <option value="" disabled selected hidden></option>
                <option>Room Booking Enquiry</option>
                <option>Car Rental</option>
                <option>Events &amp; Community Activities</option>
                <option>Shop / Products</option>
                <option>House Rules</option>
                <option>General Question</option>
                <option>Other</option>
              </select>
              <label for="subject">Subject</label>
              <span class="error-msg">Please select a subject</span>
            </div>

            <div class="form-group full textarea-group">
              <textarea
                id="message"
                name="message"
                placeholder=" "
                rows="5"
              ></textarea>
              <label for="message">Your Message</label>
              <span class="error-msg">Please write a message</span>
            </div>

            <label class="checkbox-group">
              <input type="checkbox" id="consent" name="consent" />
              <span class="custom-checkbox"></span>
              <span>
                I agree to the <a href="<?php echo $baseLink('privacy'); ?>">Privacy Policy</a>
                and consent to being contacted about my enquiry.
              </span>
            </label>

            <button type="submit" class="btn-submit">
              Send Message
              <span class="arrow">&#8594;</span>
            </button>
          </div>
        </form>

        <div class="form-success" id="formSuccess">
          <div class="success-icon">&#10003;</div>
          <p class="success-title">
            Message <em>sent!</em>
          </p>
          <p class="success-msg">
            Thank you for reaching out. Our team will get back to you within 24
            hours.
          </p>
          <a
            href="/"
            class="btn-primary-sm"
            style="margin-top: var(--space-2)"
          >
            &#8592; Back to Home
          </a>
        </div>
      </div>
    </section>

    <div class="map-section">
      <div class="map-header reveal">
        <div>
          <p class="section-label" style="margin-bottom: var(--space-2)">
            Find Us
          </p>
          <h2>Our <em>location</em></h2>
        </div>
        <a
          href="https://maps.google.com/?q=Musanze,Rwanda"
          target="_blank"
          class="directions-link"
          rel="noreferrer"
        >
          Get Directions &#8599;
        </a>
      </div>

      <div class="map-wrapper reveal reveal-delay-2">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31910.87113110543!2d29.61587883908226!3d-1.5037494498305096!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dc50b446101f65%3A0x23a3b04944d6738c!2sMusanze!5e0!3m2!1sen!2srw!4v1712580000000!5m2!1sen!2srw"
          style="border:0;"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Virunga Homestay Location - Musanze, Rwanda"
        ></iframe>
      </div>
    </div>

    <section class="faq-section">
      <div class="faq-inner">
        <div class="faq-header reveal">
          <p class="section-label" style="justify-content: center">
            Quick Answers
          </p>
          <h2>Frequently asked <em>questions</em></h2>
          <p>Can't find your answer? Just send us a message above.</p>
        </div>

        <div class="faq-grid">
          <?php
          $sqlFaqs = "SELECT * FROM faqs WHERE status = 'active' ORDER BY display_order ASC";
          $resFaqs = $conn->query($sqlFaqs);
          if ($resFaqs && $resFaqs->num_rows > 0):
              $i = 1;
              while($faq = $resFaqs->fetch_assoc()):
          ?>
          <div class="faq-item reveal reveal-delay-<?= ($i % 4) ?: 4 ?>">
            <button class="faq-q">
              <?= htmlspecialchars($faq['question']) ?>
              <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
            </button>
            <div class="faq-a">
              <p>
                <?= nl2br(htmlspecialchars($faq['answer'])) ?>
              </p>
            </div>
          </div>
          <?php 
              $i++;
              endwhile; 
          else:
          ?>
          <!-- Fallback if no FAQs in database -->
          <div class="faq-item reveal reveal-delay-1">
            <button class="faq-q">
              What's the best way to book a room?
              <span class="faq-icon"><i class="fa-solid fa-plus"></i></span>
            </button>
            <div class="faq-a">
              <p>
                The fastest way is to use our online booking form or send us a
                message via WhatsApp. You can also email us directly.
              </p>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <div class="cta-strip">
      <div class="cta-strip-text reveal">
        <h3>Ready for your <em>next getaway?</em></h3>
        <p>Explore our rooms and packages - the perfect escape awaits.</p>
      </div>
      <div class="cta-strip-actions reveal reveal-delay-2">
        <a href="<?php echo $baseLink('rooms'); ?>" class="btn-primary-sm">View Rooms &#8594;</a>
        <a href="<?php echo $baseLink('about'); ?>" class="btn-ghost-sm">Our Story</a>
      </div>
    </div>
<?php include 'includes/footer.php'; ?>


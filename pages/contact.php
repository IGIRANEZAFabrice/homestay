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
        <div class="contact-help">
          <p class="curated-eyebrow">WE HELP WITH</p>
          <ul class="exp-row__list">
            <li>Accommodation</li>
            <li>Cultural experiences</li>
            <li>Trekking guidance support</li>
            <li>Local transfers if needed</li>
          </ul>
        </div>

        <div class="contact-reach" style="margin-top: 60px;">
          <p class="curated-eyebrow">REACH US DIRECTLY</p>
          <p class="contact-reach__info">
            <a href="mailto:virungahomestay@gmail.com">virungahomestay@gmail.com</a><br />
            <a href="tel:+250784513435">+250 784 513 435</a>
          </p>
        </div>
      </div>

      <div class="contact-right">
        <div id="formSuccess" class="form-success-refined" style="display: none;">
          <div class="success-icon-wrap">
            <i class="fa-solid fa-paper-plane"></i>
          </div>
          <h3 class="form-title">Message <em>Sent!</em></h3>
          <p class="form-sub">Your message was sent. We will get back to you as soon as possible.</p>
          
          <div class="success-actions">
            <a href="<?php echo $baseLink('home'); ?>" class="btn-journey btn-success-action">
              <i class="fa-solid fa-house"></i> BACK TO HOME
            </a>
            <a href="<?php echo $baseLink('rooms'); ?>" class="btn-journey btn-success-action btn-accent">
              <i class="fa-solid fa-calendar-check"></i> BOOK OUR STAY
            </a>
            <a href="<?php echo $baseLink('experiences'); ?>" class="btn-journey btn-success-action">
              <i class="fa-solid fa-mountain-sun"></i> VIEW EXPERIENCES
            </a>
          </div>
        </div>

        <div id="contactFormContainer">
          <h3 class="form-title">Send us a <em>message</em></h3>
          <p class="form-sub">We reply within 24 hours.</p>

          <form id="contactForm" novalidate>
            <div class="form-minimal-grid">
              <div class="form-minimal-row">
                <div class="form-minimal-group">
                  <label for="fname">First Name</label>
                  <input type="text" id="fname" name="fname" placeholder=" " required />
                  <span class="error-msg">Please enter your first name</span>
                </div>

                <div class="form-minimal-group">
                  <label for="lname">Last Name</label>
                  <input type="text" id="lname" name="lname" placeholder=" " required />
                  <span class="error-msg">Please enter your last name</span>
                </div>
              </div>

              <div class="form-minimal-row">
                <div class="form-minimal-group">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" name="email" placeholder=" " required />
                  <span class="error-msg">Enter a valid email</span>
                </div>

                <div class="form-minimal-group">
                  <label for="phone">Phone (optional)</label>
                  <input type="tel" id="phone" name="phone" placeholder=" " />
                </div>
              </div>

              <div class="form-minimal-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                  <option value="" disabled selected hidden></option>
                  <option>Room Booking Enquiry</option>
                  <option>Car Rental</option>
                  <option>Events &amp; Community Activities</option>
                  <option>Shop / Products</option>
                  <option>House Rules</option>
                  <option>General Question</option>
                  <option>Other</option>
                </select>
                <span class="error-msg">Please select a subject</span>
              </div>

              <div class="form-minimal-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" rows="4" placeholder=" " required></textarea>
                <span class="error-msg">Please write a message</span>
              </div>

              <div class="form-minimal-group" style="border:none;">
                <label class="checkbox-group">
                  <input type="checkbox" id="consent" name="consent" required />
                  <span>
                    I agree to the <a href="<?php echo $baseLink('privacy'); ?>">Privacy Policy</a> and consent to being contacted.
                  </span>
                </label>
              </div>

              <div class="form-minimal-actions">
                <button type="submit" class="btn-journey btn-submit-contact">
                  SEND MESSAGE
                </button>
              </div>
            </div>
          </form>
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

      <div class="map-wrapper reveal reveal-delay-2" id="location">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.4582870349604!2d29.63036257356573!3d-1.4960571358884254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca74680751f5d%3A0x8181ab39eecaf265!2sVirunga%20Homestay%20Experience%20-%20Where%20Virunga%20Becomes%20Personal.!5e0!3m2!1sen!2srw!4v1778424750665!5m2!1sen!2srw" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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


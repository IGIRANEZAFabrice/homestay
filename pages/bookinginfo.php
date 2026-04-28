<?php
  $pageTitle = 'Booking & Payment Information - Virunga Homestay';
  $pageDescription = 'Learn how to book your stay at Virunga Homestay. Flexible booking options, direct booking discounts, and secure payment methods.';
  $pageKeywords = 'booking, payment, Virunga Homestay, reservation, Musanze, Rwanda';
  $pageCss = ['page-hero.css', 'bookinginfo.css'];
  $pageHeroKey = 'booking';
  $pageScripts = ['bookinginfo.js'];
  include 'includes/header.php';
?>

<!-- ── Page Hero ────────────────────────────────────────────────── -->
<div class="page-hero" style="min-height: 38vh;">
  <div class="page-hero-bg" style="background-image:url('./img/hero/2.jpg');"></div>
  <div class="page-hero-content">
    <span class="page-tag">Reservations</span>
    <h1>Booking &amp; <em>Payments</em></h1>
    <span class="hero-rule"></span>
  </div>
</div>

<!-- ── Breadcrumb ────────────────────────────────────────────────── -->
<nav class="breadcrumb" aria-label="Breadcrumb">
  <a href="<?php echo $baseLink('home'); ?>">Home</a>
  <span class="sep">/</span>
  <span style="color:var(--color-text-on-dark-2);">Booking Information</span>
</nav>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- HOW TO BOOK                                                    -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="bi-section">
  <div class="bi-container">

    <!-- Section intro -->
    <div class="bi-intro" data-reveal>
      <span class="bi-label"><i class="fa-solid fa-calendar-check"></i> How to Book</span>
      <h2 class="bi-title">Your Stay Starts Here</h2>
      <p class="bi-subtitle">
        Reserving your stay at Virunga Homestay is simple, secure, and rewarding. We provide flexible booking options and ensure you enjoy exclusive savings when you book directly with us.
      </p>
    </div>

    <!-- Steps timeline -->
    <div class="bi-timeline">
      <div class="bi-timeline__line" aria-hidden="true"></div>

      <div class="bi-step" data-reveal>
        <div class="bi-step__marker">
          <span class="bi-step__num">01</span>
          <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="bi-step__card">
          <h3 class="bi-step__title">Book via Email</h3>
          <p class="bi-step__text">
            Send your request to <a href="mailto:virungahomestay@gmail.com">virungahomestay@gmail.com</a>, and our reservations team will quickly assist you with availability, room preferences, and booking details.
          </p>
          <a href="mailto:virungahomestay@gmail.com" class="bi-step__action">
            <i class="fa-solid fa-paper-plane"></i> Send Email
          </a>
        </div>
      </div>

      <div class="bi-step" data-reveal>
        <div class="bi-step__marker">
          <span class="bi-step__num">02</span>
          <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div class="bi-step__card">
          <h3 class="bi-step__title">Book via WhatsApp or Call</h3>
          <p class="bi-step__text">
            Contact us directly on WhatsApp or by phone at <strong>+250 784 513 435</strong>. Whether by message or call, you'll receive fast responses, real-time support, and immediate booking confirmation.
          </p>
          <a href="https://wa.me/250784513435" target="_blank" rel="noopener" class="bi-step__action bi-step__action--wa">
            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
          </a>
        </div>
      </div>

      <div class="bi-step" data-reveal>
        <div class="bi-step__marker">
          <span class="bi-step__num">03</span>
          <i class="fa-solid fa-percent"></i>
        </div>
        <div class="bi-step__card">
          <h3 class="bi-step__title">Unlock Direct Booking Discounts</h3>
          <p class="bi-step__text">
            Guests who book directly through our email or WhatsApp/phone enjoy <strong>exclusive discounts</strong> and special rates not offered on external booking platforms. This guarantees you the best price for your stay.
          </p>
        </div>
      </div>

      <div class="bi-step" data-reveal>
        <div class="bi-step__marker">
          <span class="bi-step__num">04</span>
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="bi-step__card">
          <h3 class="bi-step__title">Secure Your Reservation</h3>
          <p class="bi-step__text">
            After confirming your booking, you'll receive an official confirmation with details of our deposit and payment methods, ensuring your stay is reserved with confidence.
          </p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- WHY DIRECT BOOKING                                            -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="bi-why-section">
  <div class="bi-container">
    <div class="bi-why" data-reveal>

      <div class="bi-why__header">
        <i class="fa-solid fa-star"></i>
        <h3 class="bi-why__title">Why Choose Direct Booking?</h3>
      </div>

      <div class="bi-why__grid">
        <div class="bi-why__item">
          <div class="bi-why__icon"><i class="fa-solid fa-tag"></i></div>
          <div>
            <strong>Best Price Guarantee</strong>
            <p>Save more with huge direct-booking discounts.</p>
          </div>
        </div>
        <div class="bi-why__item">
          <div class="bi-why__icon"><i class="fa-solid fa-bolt"></i></div>
          <div>
            <strong>Faster Service</strong>
            <p>Instant replies and seamless confirmations.</p>
          </div>
        </div>
        <div class="bi-why__item">
          <div class="bi-why__icon"><i class="fa-solid fa-heart"></i></div>
          <div>
            <strong>Personalized Care</strong>
            <p>Direct contact with our team for customized arrangements.</p>
          </div>
        </div>
        <div class="bi-why__item">
          <div class="bi-why__icon"><i class="fa-solid fa-gem"></i></div>
          <div>
            <strong>Exclusive Benefits</strong>
            <p>Priority room allocation, dining flexibility, and local activity support.</p>
          </div>
        </div>
      </div>

      <p class="bi-why__footer">
        Booking directly with Virunga Homestay means enjoying authentic hospitality, unbeatable value, and a smooth reservation process from start to finish.
      </p>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- PAYMENT METHODS                                               -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="bi-section bi-section--dark">
  <div class="bi-container">

    <div class="bi-intro" data-reveal>
      <span class="bi-label"><i class="fa-solid fa-credit-card"></i> Payment Methods</span>
      <h2 class="bi-title">Client Payment Methods &amp; Currency</h2>
      <p class="bi-subtitle">
        We provide a secure, transparent, and internationally compatible payment system designed to accommodate clients from different regions.
      </p>
    </div>

    <!-- Currency banner -->
    <div class="bi-currency" data-reveal>
      <h4 class="bi-currency__title">Accepted Currencies</h4>
      <div class="bi-currency__grid">
        <div class="bi-currency__item">
          <span class="bi-currency__symbol">$</span>
          <div>
            <strong>US Dollars (USD)</strong>
            <p>Standard international currency for safari and ecotour payments.</p>
          </div>
        </div>
        <div class="bi-currency__item">
          <span class="bi-currency__symbol">€</span>
          <div>
            <strong>Euros (EUR)</strong>
            <p>Widely accepted for European clients.</p>
          </div>
        </div>
        <div class="bi-currency__item">
          <span class="bi-currency__symbol">£</span>
          <div>
            <strong>Pounds Sterling (GBP)</strong>
            <p>Accepted for UK-based clients and agencies.</p>
          </div>
        </div>
        <div class="bi-currency__item">
          <span class="bi-currency__symbol">Fr</span>
          <div>
            <strong>Rwandan Francs (RWF)</strong>
            <p>Local currency for in-country transactions and small balances.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment method cards -->
    <div class="bi-pay-grid" data-reveal>

      <div class="bi-pay-card">
        <div class="bi-pay-card__icon"><i class="fa-solid fa-building-columns"></i></div>
        <h4 class="bi-pay-card__title">Bank Transfers <span>SWIFT / IBAN</span></h4>
        <ul class="bi-pay-card__list">
          <li>Recommended for deposits and large payments.</li>
          <li>Clients responsible for covering their own bank charges.</li>
          <li>Funds confirmed once received in full in our account.</li>
        </ul>
      </div>

      <div class="bi-pay-card">
        <div class="bi-pay-card__icon"><i class="fa-solid fa-credit-card"></i></div>
        <h4 class="bi-pay-card__title">Credit &amp; Debit Cards</h4>
        <ul class="bi-pay-card__list">
          <li>Visa, Mastercard, and American Express accepted.</li>
          <li>Encrypted with 3D-Secure authentication.</li>
          <li>Fast confirmation and global accessibility.</li>
        </ul>
      </div>

      <div class="bi-pay-card">
        <div class="bi-pay-card__icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
        <h4 class="bi-pay-card__title">Mobile Money</h4>
        <ul class="bi-pay-card__list">
          <li>MTN Mobile Money and Airtel Money available.</li>
          <li>Convenient for East African residents and last-minute payments.</li>
        </ul>
      </div>

      <div class="bi-pay-card">
        <div class="bi-pay-card__icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <h4 class="bi-pay-card__title">Cash Transactions</h4>
        <ul class="bi-pay-card__list">
          <li>Accepted in USD, EUR, GBP, and RWF for onsite payments.</li>
          <li>USD notes must be clean, undamaged, and issued from 2009 onwards.</li>
        </ul>
      </div>

    </div>

    <!-- Conditions -->
    <div class="bi-conditions" data-reveal>
      <div class="bi-conditions__header">
        <i class="fa-solid fa-file-contract"></i>
        <h4>Payment Conditions &amp; Confirmation</h4>
      </div>
      <div class="bi-conditions__grid">
        <div class="bi-conditions__item">
          <div class="bi-conditions__badge">30–50%</div>
          <strong>Deposit Requirement</strong>
          <p>A non-refundable deposit is mandatory upon booking to secure permits, accommodations, and services.</p>
        </div>
        <div class="bi-conditions__item">
          <div class="bi-conditions__badge">30 days</div>
          <strong>Balance Settlement</strong>
          <p>Full balance is due no later than 30 days before tour commencement.</p>
        </div>
        <div class="bi-conditions__item">
          <div class="bi-conditions__badge">100%</div>
          <strong>Short-Notice Bookings</strong>
          <p>Reservations within 30 days of departure require full upfront payment.</p>
        </div>
        <div class="bi-conditions__item">
          <div class="bi-conditions__badge"><i class="fa-solid fa-receipt"></i></div>
          <strong>Confirmation</strong>
          <p>Every transaction is followed by an official electronic receipt. Reservations are confirmed after the deposit is received.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ── CTA Banner ─────────────────────────────────────────────── -->
<section class="bi-cta">
  <div class="bi-container">
    <div class="bi-cta__inner" data-reveal>
      <div class="bi-cta__text">
        <h3>Ready to Reserve Your Stay?</h3>
        <p>Contact us today and enjoy exclusive direct booking discounts.</p>
      </div>
      <div class="bi-cta__actions">
        <a href="https://wa.me/250784513435" target="_blank" rel="noopener" class="bi-cta__btn bi-cta__btn--primary">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp Us
        </a>
        <a href="mailto:virungahomestay@gmail.com" class="bi-cta__btn bi-cta__btn--outline">
          <i class="fa-solid fa-envelope"></i> Send Email
        </a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

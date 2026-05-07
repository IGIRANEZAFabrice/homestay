<?php
  $pageTitle = 'Virunga Homestay - Privacy Policy';
  $pageCss = ['page-hero.css','privacy.css'];
  $pageHeroKey = 'privacy';
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<main class="privacy-container">
    <section class="privacy-highlights">
        <div class="highlight-card">
            <div class="card-icon"><i class="fas fa-fingerprint-slash"></i></div>
            <h3>No Tracking</h3>
            <p>We do not collect IP addresses or use invasive tracking pixels. Your digital footprint stays yours.</p>
        </div>
        
        <div class="highlight-card">
            <div class="card-icon"><i class="fas fa-wallet"></i></div>
            <h3>Offline Payments</h3>
            <p>Your financial security is guaranteed because we don't process or store any payment data on this site.</p>
        </div>

        <div class="highlight-card">
            <div class="card-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <h3>Data Minimalism</h3>
            <p>We only ask for the name and contact details strictly required to prepare your room.</p>
        </div>
    </section>

    <section class="policy-details">
        <article class="policy-section">
            <div class="section-heading">
                <span class="step-number">01</span>
                <h2>What we collect</h2>
            </div>
            <div class="section-body">
                <p>When you book or inquire through our forms, we collect basic identifiers to manage your stay:</p>
                <ul class="custom-list">
                    <li>Full name and contact information (Email/Phone).</li>
                    <li>Check-in and Check-out dates.</li>
                    <li>Specific preferences (e.g., dietary needs or arrival times).</li>
                </ul>
            </div>
        </article>

        <article class="policy-section">
            <div class="section-heading">
                <span class="step-number">02</span>
                <h2>Data Usage & Security</h2>
            </div>
            <div class="section-body">
                <p>Your data isn't a commodity; it’s a courtesy. We use it solely to facilitate your reservation. Our website utilizes **SSL encryption** to ensure that any information you send via our contact forms is transmitted securely.</p>
                <div class="quote-box">
                    "We believe privacy is the ultimate luxury. We treat your data with the same respect we show our guests."
                </div>
            </div>
        </article>

        <div class="privacy-cta">
            <h3>Need to update your info?</h3>
            <p>If you'd like to review or delete any details you've shared with us, our inbox is always open.</p>
            <div class="cta-buttons">
                <a href="mailto:hello@yourhomestay.com" class="btn-primary">
                    <i class="fas fa-paper-plane"></i> Get in Touch
                </a>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>


<?php
// Include header
require_once '../include/connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Booking Information | Virunga Homestay</title>
  
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/logo.css">
  <link rel="stylesheet" href="../css/rooms.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <?php include 'include/header.php'; ?>
  <section class="booking-hero">
    <h1>Booking Information</h1>
    <p>Everything you need to know about reserving your stay at Virunga Homestay</p>
  </section>
  
  <main>
    <!-- How to Book Section -->
    <section class="booking-info" id="how-to-book">
      <div class="booking-container">
        <h2 class="booking-title">How to Book at Virunga Homestay</h2>
        <p class="booking-description">Reserving your stay at Virunga Homestay is simple, secure, and rewarding. We provide flexible booking options and ensure you enjoy exclusive savings when you book directly with us.</p>
        
        <div class="booking-steps">
          <div class="booking-step">
            <h3>1. Book via Email</h3>
            <p>Send your request to <a href="mailto:virungahomestay@gmail.com">virungahomestay@gmail.com</a>, and our reservations team will quickly assist you with availability, room preferences, and booking details.</p>
          </div>
          
          <div class="booking-step">
            <h3>2. Book via WhatsApp or Call</h3>
            <p>Contact us directly on WhatsApp or by phone at <a href="tel:+250784513435">+250 784 513 435</a>. Whether by message or call, you will receive fast responses, real-time support, and immediate booking confirmation.</p>
          </div>
          
          <div class="booking-step">
            <h3>3. Unlock Direct Booking Discounts</h3>
            <p>Guests who book directly through our email or WhatsApp/phone enjoy exclusive discounts and special rates not offered on external booking platforms. This guarantees you the best price for your stay.</p>
          </div>
          
          <div class="booking-step">
            <h3>4. Secure Your Reservation</h3>
            <p>After confirming your booking, you will receive an official confirmation with details of our deposit and payment methods, ensuring your stay is reserved with confidence.</p>
          </div>
        </div>
        
        <div class="booking-benefits">
          <h3>Why Choose Direct Booking at Virunga Homestay?</h3>
          <ul>
            <li><strong>Best Price Guarantee</strong> – Save more with huge direct-booking discounts.</li>
            <li><strong>Faster Service</strong> – Instant replies and seamless confirmations.</li>
            <li><strong>Personalized Care</strong> – Direct contact with our team for customized arrangements.</li>
            <li><strong>Exclusive Benefits</strong> – Priority room allocation, dining flexibility, and support for local activities.</li>
          </ul>
          <p class="booking-footer">Booking directly with Virunga Homestay means enjoying authentic hospitality, unbeatable value, and a smooth reservation process from start to finish.</p>
        </div>
      </div>
    </section>
    
    <!-- Payment Methods Section -->
    <section class="payment-info" id="payment-methods">
      <div class="payment-container">
        <h2 class="payment-title">Virunga Ecotours – Client Payment Methods & Currency Acceptance</h2>
        <p class="payment-description">At Virunga Ecotours, we provide a secure, transparent, and internationally compatible payment system designed to accommodate clients from different regions. Our structure ensures flexibility while maintaining professional financial standards for tourism operations across the Virunga Massif.</p>
        
        <div class="payment-section">
          <h3>Accepted Currencies</h3>
          <ul class="payment-list">
            <li><strong>US Dollars (USD):</strong> Standard international currency for safari and ecotour payments.</li>
            <li><strong>Euros (EUR):</strong> Widely accepted for European clients.</li>
            <li><strong>Pounds Sterling (GBP):</strong> Payments in British Pounds are accepted for UK-based clients and agencies.</li>
            <li><strong>Rwandan Francs (RWF):</strong> Local currency accepted for in-country transactions, additional services, and small balances.</li>
          </ul>
        </div>
        
        <div class="payment-section">
          <h3>Authorized Payment Methods</h3>
          
          <div class="payment-method">
            <h4>1. International Bank Transfers (SWIFT/IBAN):</h4>
            <ul>
              <li>Recommended for deposits and large payments.</li>
              <li>Clients are responsible for covering their own bank charges.</li>
              <li>Funds are confirmed only once received in full in our account.</li>
            </ul>
          </div>
          
          <div class="payment-method">
            <h4>2. Credit & Debit Cards:</h4>
            <ul>
              <li>Major cards accepted: Visa, Mastercard, and American Express.</li>
              <li>Online payments processed with encryption and 3D-Secure authentication.</li>
              <li>Fast confirmation and global accessibility.</li>
            </ul>
          </div>
          
          <div class="payment-method">
            <h4>3. Mobile Money Services:</h4>
            <ul>
              <li>MTN Mobile Money and Airtel Money available for regional clients.</li>
              <li>Convenient option for East African residents and last-minute payments.</li>
            </ul>
          </div>
          
          <div class="payment-method">
            <h4>4. Cash Transactions:</h4>
            <ul>
              <li>Accepted in USD, EUR, GBP, and RWF for onsite payments.</li>
              <li>USD notes must be clean, undamaged, and issued from 2009 onwards to meet bank regulations.</li>
            </ul>
          </div>
        </div>
        
        <div class="payment-section">
          <h3>Payment Conditions</h3>
          <ul class="payment-list">
            <li><strong>Deposit Requirement:</strong> A non-refundable deposit of 30–50% is mandatory upon booking to secure permits, accommodations, and services.</li>
            <li><strong>Balance Settlement:</strong> Full balance is due no later than 30 days before tour commencement.</li>
            <li><strong>Short-Notice Bookings:</strong> Reservations made within 30 days of departure require 100% upfront payment.</li>
          </ul>
        </div>
        
        <div class="payment-section">
          <h3>Confirmation & Documentation</h3>
          <ul class="payment-list">
            <li>Every transaction is followed by an official electronic receipt.</li>
            <li>Reservations are considered valid and confirmed only after the deposit is successfully received and acknowledged by the finance department.</li>
          </ul>
        </div>
      </div>
    </section>
  </main>
  
  <?php include 'include/footer.php'; ?>

  <!-- Floating WhatsApp Button -->
  <a
    href="https://wa.me/+250788123456?text=Hello! I'd like to know more about booking at Virunga Homestay"
    class="floating-whatsapp"
  >
    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
      <path
        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.688"
      />
    </svg>
  </a>
  
  <script src="../js/header.js"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</body>
</html>
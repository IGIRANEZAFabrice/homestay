<?php
// Include database connection
require_once '../include/connection.php';
require_once '../include/image_helpers.php';

// Define getMultipleRows function if not already defined
if (!function_exists('getMultipleRows')) {
    function getMultipleRows($query, $types = '', $params = []) {
        global $conn;
        $result = $conn->query($query);
        $rows = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }
}

// Fetch house rules from database
$house_rules = getMultipleRows("SELECT * FROM house_rules WHERE is_active = 1 ORDER BY section, display_order", "", []);

// Fetch cancellation policies from database
$cancellation_policies = getMultipleRows("SELECT * FROM cancellation_policy WHERE is_active = 1 ORDER BY section, display_order", "", []);

// Fetch info cards from database
$info_cards = getMultipleRows("SELECT * FROM house_info_cards WHERE is_active = 1 ORDER BY display_order", "", []);

// Group house rules by section
$grouped_rules = [];
if ($house_rules) {
    foreach ($house_rules as $rule) {
        $section = $rule['section'];
        if (!isset($grouped_rules[$section])) {
            $grouped_rules[$section] = [];
        }
        $grouped_rules[$section][] = $rule;
    }
}

// Group cancellation policies by section
$grouped_policies = [];
if ($cancellation_policies) {
    foreach ($cancellation_policies as $policy) {
        $section = $policy['section'];
        if (!isset($grouped_policies[$section])) {
            $grouped_policies[$section] = [];
        }
        $grouped_policies[$section][] = $policy;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>House Rules | Virunga Homestay</title>
  <link rel="stylesheet" href="../css/houserules.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/logo.css">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/4e9c2b2c0a.js" crossorigin="anonymous"></script>
</head>
<body>
  <?php include 'include/header.php'; ?>
  <section class="houserules-hero">
    <div class="hero-content">
      <h1>Welcome to Virunga Homestay</h1>
      <p>Enjoy a unique and immersive experience blending nature, culture, adventure, and conservation. Please review our house rules to ensure a comfortable stay for everyone.</p>
    </div>
    <div class="hero-image">
      <img src="../img/house.jpg" alt="Virunga Homestay">
    </div>
  </section>
   <section id="cancel" class="cancellation-policy">
    <h2>Cancellation & Refund Policy</h2>
    <div class="policy-container">
      <?php if ($cancellation_policies): ?>
        <?php foreach ($grouped_policies as $section => $policies): ?>
          <?php foreach ($policies as $policy): ?>
            <div class="policy-card">
              <h3><i class="<?php echo htmlspecialchars($policy['icon']); ?>"></i> <?php echo htmlspecialchars($policy['title']); ?></h3>
              <?php echo $policy['content']; ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback content if no policies are found in the database -->
        <div class="policy-card">
          <h3><i class="fas fa-money-bill-wave"></i> Refund Schedule</h3>
          <ul>
            <li><strong>Full Refund (100%)</strong> – Cancel 7 days or more before your check-in date.</li>
            <li><strong>Partial Refund (50%)</strong> – Cancel 3 to 6 days before check-in.</li>
            <li><strong>No Refund</strong> – Cancel less than 48 hours before check-in.</li>
            <li><strong>No-Show</strong> – If you do not arrive on your scheduled check-in date, it will be treated as a no-show with no refund issued.</li>
          </ul>
        </div>

        <div class="policy-card">
          <h3><i class="fas fa-info-circle"></i> Important Notes</h3>
          <ul>
            <li>Unused inclusions in your booking (e.g. meals, activities) are non-refundable.</li>
            <li>Refunds will be processed within 7 working days to the original payment account.</li>
            <li>Discounted or promotional bookings are non-refundable.</li>
            <li>Refunds apply only to the basic room tariff. Convenience fees and taxes are non-refundable.</li>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="houserules-info-grid">
    <?php if ($info_cards): ?>
      <?php foreach ($info_cards as $card): ?>
        <div class="info-card"><i class="<?php echo htmlspecialchars($card['icon']); ?>"></i><span><?php echo htmlspecialchars($card['content']); ?></span></div>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Fallback content if no info cards are found in the database -->
      <div class="info-card"><i class="fas fa-door-open"></i><span>Check-In: 12:00 PM</span></div>
      <div class="info-card"><i class="fas fa-door-closed"></i><span>Check-Out: 10:00 PM</span></div>
      <div class="info-card"><i class="fas fa-moon"></i><span>Quiet Hours: 10:00 PM - 7:00 AM</span></div>
      <div class="info-card"><i class="fas fa-user-shield"></i><span>Respect Privacy Policy</span></div>
      <div class="info-card"><i class="fas fa-smoking-ban"></i><span>No Smoking</span></div>
      <div class="info-card"><i class="fas fa-dog"></i><span>No Pets</span></div>
      <div class="info-card"><i class="fas fa-user-slash"></i><span>No Unapproved Guests</span></div>
      <div class="info-card"><i class="fas fa-shield-alt"></i><span>Guest Liable for Damage</span></div>
    <?php endif; ?>
  </section>

 

  <button id="toggleRulesBtn" class="view-all-btn" style="margin: 30px auto 0; display: block; padding: 10px 28px; font-size: 1.1em; border-radius: 6px; background: #1e90ff; color: #fff; border: none; cursor: pointer; transition: background 0.2s;">View All Rules</button>
  <section class="houserules-main" id="houserulesMainSection">
    <h2>House Rules</h2>
    <div class="rules-list">
      <?php if ($house_rules): ?>
        <?php foreach ($grouped_rules as $section => $rules): ?>
          <?php if (count($grouped_rules) > 1): ?>
            <h3 class="section-title">
              <?php 
              $section_title = 'General Rules';
              if ($section == 'safety') $section_title = 'Safety Rules';
              if ($section == 'amenities') $section_title = 'Amenities Rules';
              echo $section_title; 
              ?>
            </h3>
          <?php endif; ?>
          
          <?php foreach ($rules as $rule): ?>
            <div class="rule-card">
              <h3><i class="<?php echo htmlspecialchars($rule['icon']); ?>"></i> <?php echo htmlspecialchars($rule['title']); ?></h3>
              <?php echo $rule['content']; ?>
            </div>
          <?php endforeach; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback content if no house rules are found in the database -->
        <div class="rule-card">
          <h3><i class="fas fa-utensils"></i> Breakfast</h3>
          <p>The breakfast is served from 6:00 AM to 8:00 AM.<br>
          <span class="highlight">Note: Hosts can offer a complimentary light breakfast at their discretion. All other meals, including a full breakfast, might incur an additional cost, if offered. Meals and any additional payment should be arranged directly with your host.</span></p>
        </div>
        <div class="rule-card">
          <h3><i class="fas fa-volume-mute"></i> Quiet Hours</h3>
          <p>Observe quiet hours (starting from 10:00 PM to 7:00 AM). Respect the host's and other guests' right to peace and quiet during specified hours. Avoid loud noise or music that may disturb others at any time.</p>
        </div>
        <div class="rule-card">
          <h3><i class="fas fa-smoking-ban"></i> No Smoking & Drug Use</h3>
          <p>Do not smoke or use drugs in the homestay or on the property. If smoking is allowed, only smoke in designated outdoor areas and dispose of cigarette butts appropriately.</p>
        </div>
        <div class="rule-card">
          <h3><i class="fas fa-user-secret"></i> Respect Privacy</h3>
          <p>Respect the privacy of the host and other guests staying at the homestay. Do not enter other guests' rooms without permission, and ensure that any shared spaces are left tidy after use.</p>
        </div>
      <?php endif; ?>
    </div>
  </section>
  <?php include 'include/footer.php'; ?>

    <!-- Floating WhatsApp Button -->
    <a
      href="https://wa.me/+250788123456?text=Hello! I'd like to know more about Virunga Homestay"
      class="floating-whatsapp"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.688"
        />
      </svg>
    </a>
    <script>
      const btn = document.getElementById('toggleRulesBtn');
      const section = document.getElementById('houserulesMainSection');
      let rulesVisible = false;
      btn.addEventListener('click', function() {
        rulesVisible = !rulesVisible;
        section.style.display = rulesVisible ? 'block' : 'none';
        btn.textContent = rulesVisible ? 'Hide Rules' : 'View All Rules';
      });
      // Start with rules hidden
      section.style.display = 'none';
      btn.textContent = 'View All Rules';
    </script>
    <script src="../js/header.js"></script>
</body>
</html>

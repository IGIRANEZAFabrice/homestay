<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle form submission
$success = false;
$error = '';
$debug = '';
require_once '../include/connection.php';
if (!$conn) {
    die('<div class="error-message">Database connection failed: ' . mysqli_connect_error() . '</div>');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $debug .= 'POST: ' . print_r($_POST, true) . '<br>';
    if ($name && $email && $message && $subject) {
        $fullMessage = '';
        if ($phone) $fullMessage .= "Phone: $phone\n";
        if ($subject) $fullMessage .= "Subject: $subject\n";
        $fullMessage .= $message;
        $sql = "INSERT INTO contact_messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('sss', $name, $email, $fullMessage);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to save your message. Please try again. Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = 'Database error: ' . $conn->error;
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Virunga Homestay</title>
    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/rooms.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      .popup-message {
        display: none;
        position: fixed;
        top: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        min-width: 300px;
        max-width: 90vw;
        padding: 18px 30px 18px 18px;
        border-radius: 8px;
        font-size: 1.1em;
        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
        color: #fff;
        background: #28a745;
        transition: opacity 0.3s;
      }
      .popup-message.error { background: #dc3545; }
      .popup-message .close-btn {
        position: absolute;
        right: 12px;
        top: 10px;
        color: #fff;
        background: none;
        border: none;
        font-size: 1.2em;
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <?php include 'include/header.php'; ?>
    <!-- Hero Section -->
    <section class="rooms-hero">
    <h1>Get In touch</h1>
    <p>We would love to replay to you !</p>
  </section>

    <!-- Contact Section -->
    <section class="contact-section">
      <div class="container">
        <div class="contact-grid">
          <!-- Contact Form -->
          <div class="contact-form-container">
            <h2>Send us a Message</h2>
            <?php if ($success): ?>
              <div class="success-message">Thank you! Your message has been sent.</div>
            <?php elseif ($error): ?>
              <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php else: ?>
              <div class="info-message">Please fill out the form below to contact us.</div>
            <?php endif; ?>
            <form id="contactForm" class="contact-form" method="post" action="">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
              </div>
              <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                  <option value="">Select a subject</option>
                  <option value="booking" <?php if(($_POST['subject'] ?? '')==='booking') echo 'selected'; ?>>Booking Inquiry</option>
                  <option value="information" <?php if(($_POST['subject'] ?? '')==='information') echo 'selected'; ?>>General Information</option>
                  <option value="feedback" <?php if(($_POST['subject'] ?? '')==='feedback') echo 'selected'; ?>>Feedback</option>
                  <option value="other" <?php if(($_POST['subject'] ?? '')==='other') echo 'selected'; ?>>Other</option>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
              </div>
              <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane"></i>
                Send Message
              </button>
            </form>
          </div>

          <!-- Contact Information -->
          <div class="contact-info">
            <div class="info-card">
              <h2>Contact Information</h2>
              <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                  <h3>Address</h3>
                  <p>Virunga Homestay<br>NM 61 ST, Ruhengeri, Musanze, Rwanda.</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                  <h3>Phone</h3>
                  <p>(+250) 784 513 435</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                  <h3>Email</h3>
                  <p>info@virungaecotours.com</p>
                </div>
              </div>
              <div class="info-item">
                <i class="fas fa-clock"></i>
                <div>
                  <h3>Business Hours</h3>
                  <p>Monday - Sunday: 8:00 AM - 8:00 PM</p>
                </div>
              </div>
            </div>

            <!-- Social Media Links -->
            <div class="social-links">
              <h3>Follow Us</h3>
              <div class="social-icons">
                <a href="#" class="social-icon">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-icon">
                  <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-icon">
                  <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-icon">
                  <i class="fab fa-whatsapp"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
      <div class="map-container">
       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7976.917181020954!2d29.629925494327598!3d-1.4958902032725103!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca74680751f5d%3A0x8181ab39eecaf265!2sVirunga%20Homestays!5e0!3m2!1sen!2srw!4v1750855097843!5m2!1sen!2srw" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </section>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/+250788123456?text=Hi! I'd like to know more about Virunga Homestay" class="whatsapp-float">
      <i class="fab fa-whatsapp"></i>
    </a>
<?php include 'include/footer.php'; ?>

    <div id="popupMsg" class="popup-message<?php if ($success) echo ''; elseif ($error) echo ' error'; ?>">
      <span id="popupMsgText">
        <?php if ($success) echo 'Thank you! Your message has been sent.'; elseif ($error) echo htmlspecialchars($error); ?>
      </span>
      <button class="close-btn" onclick="document.getElementById('popupMsg').style.display='none'">&times;</button>
    </div>
    <script>
      window.onload = function() {
        var msg = document.getElementById('popupMsg');
        var txt = document.getElementById('popupMsgText').innerText.trim();
        if (txt.length > 0) {
          msg.style.display = 'block';
          setTimeout(function(){ msg.style.display = 'none'; }, 6000);
        }
      };
    </script>
    <script src="../js/contact.js"></script>
  </body>
</html>

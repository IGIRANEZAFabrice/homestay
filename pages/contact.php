<?php
// Contact page - form submission is handled via AJAX
$success = false;
$error = '';

// Handle URL parameters for fallback (non-AJAX) submissions
if (isset($_GET['success'])) {
    $success = true;
    $success_message = $_GET['success'];
} elseif (isset($_GET['error'])) {
    $error = $_GET['error'];
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
            <div id="form-messages" class="<?php
                if ($success) echo 'success-message';
                elseif ($error) echo 'error-message';
                else echo 'info-message';
            ?>">
                <?php
                    if ($success) echo htmlspecialchars($success_message ?? 'Thank you! Your message has been sent successfully.');
                    elseif ($error) echo htmlspecialchars($error);
                    else echo 'Please fill out the form below to contact us.';
                ?>
            </div>
            <form id="contactForm" class="contact-form" method="post" action="../backend/contact_handler.php">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="subject">Subject</label>
                <select id="subject" name="subject" required>
                  <option value="">Select a subject</option>
                  <option value="Booking Inquiry">Booking Inquiry</option>
                  <option value="General Information">General Information</option>
                  <option value="Feedback">Feedback</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
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

    <div id="popupMsg" class="popup-message<?php if ($error) echo ' error'; ?>">
      <span id="popupMsgText">
        <?php
            if ($success) echo htmlspecialchars($success_message ?? 'Thank you! Your message has been sent successfully.');
            elseif ($error) echo htmlspecialchars($error);
        ?>
      </span>
      <button class="close-btn" onclick="document.getElementById('popupMsg').style.display='none'">&times;</button>
    </div>

    <script>
      // Show popup for URL parameters (fallback mode)
      window.onload = function() {
        var msg = document.getElementById('popupMsg');
        var txt = document.getElementById('popupMsgText').innerText.trim();
        if (txt.length > 0) {
          msg.style.display = 'block';
          setTimeout(function(){ msg.style.display = 'none'; }, 6000);
        }
      };

      // AJAX form submission
      // AJAX form submission
      document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('.submit-btn');
        const formMessages = document.getElementById('form-messages');
        const popupMsg = document.getElementById('popupMsg');
        const popupMsgText = document.getElementById('popupMsgText');

        // Disable submit button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        formMessages.className = 'info-message';
        formMessages.textContent = 'Sending your message...';

        // Send AJAX request
        fetch(form.action, {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Success
            formMessages.className = 'success-message';
            formMessages.textContent = data.message;
            popupMsgText.textContent = data.message;
            popupMsg.className = 'popup-message';
            popupMsg.style.display = 'block';
            form.reset(); // Clear the form

            // Hide popup after 6 seconds
            setTimeout(() => {
              popupMsg.style.display = 'none';
            }, 6000);
          } else {
            // Error
            formMessages.className = 'error-message';
            formMessages.textContent = data.message;
            popupMsgText.textContent = data.message;
            popupMsg.className = 'popup-message error';
            popupMsg.style.display = 'block';

            // Hide popup after 8 seconds for errors
            setTimeout(() => {
              popupMsg.style.display = 'none';
            }, 8000);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          const errorMsg = 'Sorry, there was an error sending your message. Please try again.';
          formMessages.className = 'error-message';
          formMessages.textContent = errorMsg;
          popupMsgText.textContent = errorMsg;
          popupMsg.className = 'popup-message error';
          popupMsg.style.display = 'block';

          setTimeout(() => {
            popupMsg.style.display = 'none';
          }, 8000);
        })
        .finally(() => {
          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
        });
      });
    </script>
    <script src="../js/contact.js"></script>
  </body>
</html>

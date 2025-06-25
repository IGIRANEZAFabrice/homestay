<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Virunga Homestay - Modern Experience</title>
    <link rel="stylesheet" href="./css/styles.css" />
    <link rel="stylesheet" href="./css/hero.css" />
    <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/rooms.css">
  </head>
  <body>
    <?php include 'include/header.php'; ?>
    <!-- Hero Section -->
    <?php
    // Fetch hero slides from the database
    require_once 'include/connection.php';
    $heroSlides = [];
    $sql = "SELECT * FROM hero_images WHERE is_active = 1 ORDER BY display_order ASC LIMIT 3";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $heroSlides[] = $row;
        }
    }
    // Fallback if not enough slides
    for ($i = count($heroSlides); $i < 3; $i++) {
        $heroSlides[] = [
            'image' => 'img/hero/' . ($i+1) . '.png',
            'title' => 'Experience Paradise at Our Homestay',
            'paragraph' => 'Discover the perfect blend of comfort and nature in our serene homestay retreat'
        ];
    }
    ?>
    <section class="hero-section">
      <div class="hero-slider">
        <?php foreach ($heroSlides as $idx => $slide): ?>
          <div class="hero-slide<?php echo $idx === 0 ? ' active' : ''; ?>">
            <img src="<?php echo (strpos($slide['image'], 'uploads/') === 0 || strpos($slide['image'], '../uploads/') === 0) ? $slide['image'] : $slide['image']; ?>" alt="Homestay View <?php echo $idx+1; ?>" />
            <div class="hero-content">
              <h1 class="hero-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
              <p class="hero-subtitle"><?php echo htmlspecialchars($slide['paragraph']); ?></p>
              <a href="#contact" class="cta-button">
                Book Now
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="ml-2"
                >
                  <path d="M5 12h14"></path>
                  <path d="m12 5 7 7-7 7"></path>
                </svg>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="hero-slider-controls">
        <button class="slider-control prev" aria-label="Previous slide">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="m15 18-6-6 6-6"></path>
          </svg>
        </button>
        <div class="slider-dots">
          <button class="slider-dot active" aria-label="Slide 1"></button>
          <button class="slider-dot" aria-label="Slide 2"></button>
          <button class="slider-dot" aria-label="Slide 3"></button>
        </div>
        <button class="slider-control next" aria-label="Next slide">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="m9 18 6-6-6-6"></path>
          </svg>
        </button>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="stats-container">
        <div class="stat-card">
          <div class="stat-number">4K+</div>
          <div class="stat-label">Happy Visitors</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">80+</div>
          <div class="stat-label">Attractions</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">12</div>
          <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card">
          <div class="stat-number">5★</div>
          <div class="stat-label">Rating</div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <?php
    // Fetch about section from the database
    $about = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];
    $sql = "SELECT * FROM homepage_about LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $about = $result->fetch_assoc();
    }
    ?>
    <section class="about-section">
      <div class="about-grid">
        <div class="about-content">
          <h2><?php echo htmlspecialchars($about['title']); ?></h2>
          <?php
            $desc = explode("\n", $about['description']);
            foreach ($desc as $p) {
              if (trim($p)) echo '<p>' . htmlspecialchars($p) . '</p>';
            }
          ?>
        </div>
        <div class="about-image">
          <img
            src="<?php echo !empty($about['image']) ? $about['image'] : 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 400\'><rect fill=\'%23f4f4f4\' width=\'400\' height=\'400\'/><rect fill=\'%23e0e0e0\' x=\'50\' y=\'100\' width=\'300\' height=\'200\' rx=\'10\'/><circle fill=\'%23d0d0d0\' cx=\'200\' cy=\'200\' r=\'50\'/><rect fill=\'%23c0c0c0\' x=\'100\' y=\'320\' width=\'200\' height=\'30\' rx=\'5\'/></svg>' ?>"
            alt="Homestay Interior"
          />
        </div>
      </div>
    </section>
    <section class="services-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            The unrivalled Travel Information Center
          </h2>
          <p class="section-subtitle">
            Discover comprehensive travel services and comfortable
            accommodations designed for the modern traveler
          </p>
        </div>

        <div class="services-grid">
          <?php
          // Fetch services from the database
          $services = [];
          $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC, id ASC";
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $services[] = $row;
              }
          }
          ?>
          <?php if (count($services) > 0): ?>
            <?php foreach ($services as $idx => $service): ?>
              <div class="service-item<?php echo $idx % 2 === 1 ? ' reverse' : ''; ?>">
                <div class="service-image">
                  <img
                    src="<?php echo (preg_match('/^(http|\/)/', $service['image']) ? $service['image'] : './' . $service['image']); ?>"
                    alt="<?php echo htmlspecialchars($service['title']); ?>"
                  />
                </div>
                <div class="service-content">
                  <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                  <p>
                    <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-services-message">
              <p>No services available at the moment. Please check back later.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <!-- Rooms Section -->
    <section class="rooms-section">
      <?php
      // Include database connection
      require_once 'include/connection.php';

      // Fetch rooms from the database (limit to 6 for homepage)
      $sql = "SELECT * FROM rooms ORDER BY created_at DESC LIMIT 6";
      $result = $conn->query($sql);
      ?>
      <h2 class="section-title">Explore Our Rooms</h2>
      <div class="rooms-grid">
        <?php if($result && $result->num_rows > 0): ?>
          <?php while($room = $result->fetch_assoc()): ?>
            <div class="room-card">
              <div class="room-image">
                <?php if($room['image'] && $room['image'] != 'default-room.jpg'): ?>
                  <img src="uploads/rooms/<?php echo $room['image']; ?>" alt="<?php echo $room['title']; ?>">
                <?php endif; ?>
              </div>
              <div class="room-content">
                <h3 class="room-title"><?php echo strtoupper($room['title']); ?></h3>
                <p class="room-description">
                  <?php echo substr($room['description'], 0, 100) . '...'; ?>
                </p>
                <div class="room-buttons">
                  <a
                    href="https://wa.me/+250784513435?text=I'm interested in the <?php echo $room['title']; ?>"
                    class="btn btn-primary"
                    >Book Now</a
                  >
                  <a
                    href="#"
                    class="btn btn-secondary read-more-btn"
                    data-room-title="<?php echo strtoupper($room['title']); ?>"
                    data-room-description="<?php echo $room['description']; ?>"
                    >Read More</a
                  >
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="no-rooms-message">
            <p>No rooms available at the moment. Please check back later.</p>
          </div>
        <?php endif; ?>
      </div>
      <div class="view-all-rooms">
        <a href="pages/rooms.php" class="btn btn-secondary">View All Rooms</a>
      </div>
    </section>

    <!-- Experiences Section -->
    <?php
    // Fetch activities for experiences section
    $activities = [];
    $sql = "SELECT * FROM activities WHERE is_active = 1 ORDER BY display_order ASC, id ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $activities[] = $row;
        }
    }
    ?>
    <section class="experiences-section">
      <div class="section-header">
        <h2 class="section-title">Things to Do and See at Virunga Homestay</h2>
        <p class="section-subtitle">
          Guided walks, local cuisine, culture, and nature—unforgettable moments await.
        </p>
      </div>

      <div class="experiences-grid" id="experiencesGrid">
        <?php foreach ($activities as $idx => $activity): ?>
          <a href="pages/activities.php?id=<?php echo $activity['id']; ?>" class="experience-card-link">
            <div class="experience-card<?php echo $idx > 5 ? ' hidden' : ''; ?>">
              <div class="card-image">
                  <img src="uploads/activities/<?php echo htmlspecialchars($activity['image']); ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" />
                <div class="card-overlay"></div>
              </div>
              <div class="card-content">
                <h3 class="card-title"><?php echo htmlspecialchars($activity['title']); ?></h3>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php if (count($activities) > 6): ?>
      <div class="load-more-container" style="display: flex; justify-content: center; margin-top: 5px;">
        <button class="load-more-btn" id="loadMoreBtn" style="display: inline-flex; align-items: center; gap: 10px; background: #0a7b83; color: #fff; border: none; border-radius: 24px; padding: 12px 32px; font-size: 1.1rem; font-weight: 500; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: background 0.2s;">
          <div class="loading-spinner" style="display:none;"></div>
          <span class="btn-text">Show More</span>
          
        </button>
        <br><br><br><br><br><br>
      </div>
      <?php endif; ?>
      <script>
      document.addEventListener('DOMContentLoaded', function() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
          loadMoreBtn.style.display = 'inline-flex';
          loadMoreBtn.addEventListener('click', function() {
            const hiddenCards = document.querySelectorAll('.experience-card.hidden');
            let shown = 0;
            hiddenCards.forEach((card, idx) => {
              if (shown < 6) {
                card.classList.remove('hidden');
                shown++;
              }
            });
            // Hide button if no more hidden cards
            if (document.querySelectorAll('.experience-card.hidden').length === 0) {
              loadMoreBtn.style.display = 'none';
            }
          });
        }
      });
      </script>
    </section>

    <section class="attractions-section">
      <div class="section-header" style="color: linear-gradient(45deg, #fff, #e0e7ff);">
        <h2 class="section-title" style="color:whitesmoke;">Eco Adventures Await</h2>
        <p class="section-subtitle" style="color:whitesmoke;">
          Discover sustainable tourism experiences that connect you with nature
          while supporting local communities
        </p>
      </div>
      <div class="attractions-grid">
        <div class="attraction-card">
          <div class="attraction-icon">🦍</div>
          <h3>Gorilla Trekking</h3>
          <p>
            Experience the magic of meeting mountain gorillas in their natural
            habitat
          </p>
        </div>
        <div class="attraction-card">
          <div class="attraction-icon">🏔️</div>
          <h3>Volcano Hiking</h3>
          <p>Conquer the majestic Virunga Mountains with breathtaking views</p>
        </div>
        <div class="attraction-card">
          <div class="attraction-icon">🎭</div>
          <h3>Cultural Experiences</h3>
          <p>Immerse yourself in authentic Rwandan traditions and customs</p>
        </div>

        <div class="attraction-card">
          <div class="attraction-icon">📸</div>
          <h3>Photography Tours</h3>
          <p>Capture stunning landscapes and wildlife moments</p>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <div class="testimonials-container">
        <div class="testimonials-header">
          <h2 class="testimonials-title">What Our Guests Say</h2>
          <p class="testimonials-subtitle">
            Real stories from travelers who've experienced Virunga Homestay.
          </p>
        </div>
        <div class="reviews-scroller" id="reviewsScroller">
          <!-- Reviews will be loaded here by JS -->
        </div>
        <button class="review-us-btn" id="openReviewModalBtn">Review Us</button>
      </div>
    </section>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" id="closeReviewModalBtn">&times;</span>
        <h3>Share Your Experience</h3>
        <form id="reviewForm">
          <div class="modal-form-group">
            <label for="reviewerName">Name or Email:</label>
            <input type="text" id="reviewerName" name="reviewerName" required />
          </div>
          <div class="modal-form-group">
            <label for="reviewRating">Rating:</label>
            <div class="rating-input" id="reviewRatingInput">
              <span class="star-label" data-value="1">★</span>
              <span class="star-label" data-value="2">★</span>
              <span class="star-label" data-value="3">★</span>
              <span class="star-label" data-value="4">★</span>
              <span class="star-label" data-value="5">★</span>
            </div>
            <input
              type="hidden"
              id="reviewerRating"
              name="reviewerRating"
              value="0"
              required
            />
          </div>
          <div class="modal-form-group">
            <label for="reviewContent">Your Review:</label>
            <textarea
              id="reviewContent"
              name="reviewContent"
              required
            ></textarea>
          </div>
          <button type="submit">Submit Review</button>
        </form>
      </div>
    </div>

    <!-- Room Details Modal -->
    <div id="roomDetailsModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" id="closeRoomDetailsModalBtn">&times;</span>
        <div class="room-details-content">
          <h3 id="roomModalTitle">Room Details</h3>
          <div class="room-details-body">
            <p id="roomModalDescription">
              Room description will be displayed here.
            </p>
          </div>
        </div>
      </div>
    </div>
    <?php include 'include/footer.php'; ?>

    <!-- Floating WhatsApp Button -->
    <a
      href="https://wa.me/+250784513435?text=Hello! I'd like to know more about Virunga Homestay"
      class="floating-whatsapp"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path
          d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.893 3.688"
        />
      </svg>
    </a>

    <script src="./js/script.js"></script>
    <script src="./js/hero.js"></script>
    <script src="./js/header.js"></script>
    
    <script>
      // Initialize room details modal functionality
      document.addEventListener('DOMContentLoaded', function() {
        // Room Details Modal Functionality
        const roomDetailsModal = document.getElementById('roomDetailsModal');
        const closeRoomDetailsModalBtn = document.getElementById('closeRoomDetailsModalBtn');
        const roomModalTitle = document.getElementById('roomModalTitle');
        const roomModalDescription = document.getElementById('roomModalDescription');
        const readMoreButtons = document.querySelectorAll('.read-more-btn');

        // Function to open room details modal
        function openRoomDetailsModal(title, description) {
          roomModalTitle.textContent = title;
          roomModalDescription.textContent = description;
          roomDetailsModal.style.display = 'block';
          roomDetailsModal.classList.add('show');
          document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        // Function to close room details modal
        function closeRoomDetailsModal() {
          roomDetailsModal.style.display = 'none';
          roomDetailsModal.classList.remove('show');
          document.body.style.overflow = 'auto'; // Restore background scrolling
        }

        // Add click event listeners to all "Read More" buttons
        readMoreButtons.forEach(button => {
          button.addEventListener('click', function(e) {
            e.preventDefault();
            const title = this.getAttribute('data-room-title');
            const description = this.getAttribute('data-room-description');
            openRoomDetailsModal(title, description);
          });
        });

        // Close modal when close button is clicked
        if (closeRoomDetailsModalBtn) {
          closeRoomDetailsModalBtn.addEventListener('click', closeRoomDetailsModal);
        }

        // Close modal when clicking outside the modal content
        if (roomDetailsModal) {
          roomDetailsModal.addEventListener('click', function(e) {
            if (e.target === roomDetailsModal) {
              closeRoomDetailsModal();
            }
          });
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && roomDetailsModal.style.display === 'block') {
            closeRoomDetailsModal();
          }
        });
      });
    </script>
  </body>
</html>

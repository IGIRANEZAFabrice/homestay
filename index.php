<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Virunga Homestay - Modern Experience</title>
    <link rel="stylesheet" href="./css/styles.css" />
    <link rel="stylesheet" href="./css/hero.css" />
    <link rel="stylesheet" href="./css/style.css">
      <link rel="stylesheet" href="./css/rooms.css">
      <link rel="stylesheet" href="./css/logo.css">
      <link rel="stylesheet" href="./css/dropdown.css">
      <link rel="stylesheet" href="./css/scroll-fix.css">
      
    
    <style>
      .experience-card-hidden {
        display: none !important;
      }
      
      .show-more-container {
        text-align: center;
        margin-top: 30px;
      }
      
      #showMoreBtn {
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      }
      
      #showMoreBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
      }
      
      #showMoreBtn:active {
        transform: translateY(0);
      }
    </style>
      
  </head>
  <body>
    <?php include 'include/header.php'; ?>
    <!-- Hero Section -->
    <?php
    // Fetch hero slides from the database
    require_once 'include/connection.php';
    require_once 'include/image_helpers.php';
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
            <img src="../homestay/<?php echo buildImageUrl($slide['image'], 'hero'); ?>" alt="Homestay View <?php echo $idx+1; ?>" />
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
      
      <div class="hero-slider-controls" style="display: none;">
        <button class="slider-control prev">
          <i class="fas fa-chevron-left"></i>
        </button>
        <div class="slider-dots">
          <?php foreach ($heroSlides as $idx => $slide): ?>
            <button class="slider-dot<?php echo $idx === 0 ? ' active' : ''; ?>" data-slide="<?php echo $idx; ?>"></button>
          <?php endforeach; ?>
        </div>
        <button class="slider-control next">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      
      <a href="#about" class="scroll-down">
        Scroll Down
        <span class="arrow"></span>
      </a>
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
    <section id="about" class="about-section">
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
            src="homestay/<?php echo !empty($about['image']) ? $about['image'] : 'data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 400\'><rect fill=\'%23f4f4f4\' width=\'400\' height=\'400\'/><rect fill=\'%23e0e0e0\' x=\'50\' y=\'100\' width=\'300\' height=\'200\' rx=\'10\'/><circle fill=\'%23d0d0d0\' cx=\'200\' cy=\'200\' r=\'50\'/><rect fill=\'%23c0c0c0\' x=\'100\' y=\'320\' width=\'200\' height=\'30\' rx=\'5\'/></svg>' ?>"
            alt="Homestay Interior"
          />
        </div>
      </div>
    </section>
    <section class="services-section">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            Virunga Homestay services
          </h2>
          <p class="section-subtitle">
            Virunga Homestay offers comfortable lodging, local cuisine, guided tours, and cultural experiences near the Virunga Massif, providing an authentic Rwandan stay.
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
                    src="homestay/<?php echo buildImageUrl($service['image'], 'services'); ?>"
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
       </section>
    <!-- Rooms Section -->
    <section class="rooms-section">
      <?php
      // Include database connection
      require_once 'include/connection.php';

      // Fetch rooms from the database (limit to 3 for homepage)
      $sql = "SELECT * FROM rooms ORDER BY created_at DESC LIMIT 3";
      $result = $conn->query($sql);
      ?>
      <h2 class="section-title">Explore Our Rooms</h2>
      <div class="rooms-grid">
        <?php if($result && $result->num_rows > 0): ?>
          <?php while($room = $result->fetch_assoc()): ?>
            <div class="room-card">
              <div class="room-image">
                <?php if($room['image'] && $room['image'] != 'default-room.jpg'): ?>
                  <img src="homestay/<?php echo buildImageUrl($room['image'], 'rooms'); ?>" alt="<?php echo $room['title']; ?>">
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
      <a href="pages/booking.php" class="booking-button">
        <i class="fas fa-calendar-check"></i> View Booking Information
     </a>
     
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
          Guided walks, local cuisine, culture, and nature-unforgettable moments await.
        </p>
      </div>


      <div class="experiences-grid" id="experiencesGrid" style="margin-bottom: 0;">
        <?php foreach ($activities as $idx => $activity): ?>
          <a href="pages/activities.php?id=<?php echo $activity['id']; ?>" class="experience-card-link <?php echo $idx >= 3 ? 'experience-card-hidden' : ''; ?>" data-index="<?php echo $idx; ?>">
            <div class="experience-card">
              <div class="card-image">
                  <img src="homestay/<?php echo buildImageUrl($activity['image'], 'activities'); ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" />
                <div class="card-overlay"></div>
              </div>
              <div class="card-content">
                <h3 class="card-title"><?php echo htmlspecialchars($activity['title']); ?></h3>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      
      <?php if (count($activities) > 3): ?>
        <div class="show-more-container" style="text-align: center; margin-top: 30px;">
          <button id="showMoreBtn" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px; border-radius: 25px;">
            Show More Activities
          </button>
        </div>
      <?php endif; ?>
        </br></br>
    </section>

    <section class="attractions-section">
      <div class="section-header" style="color: linear-gradient(45deg, #fff, #e0e7ff);">
        <h2 class="section-title-w">Eco Adventures Await</h2>
        <p class="section-subtitle" style="color:whitesmoke;">
          Discover sustainable tourism experiences that connect you with nature
          while supporting local communities
        </p>
      </div>
      <div class="attractions-grid">
        <div class="attraction-card">
          <div class="attraction-icon"><i class="fas fa-person-hiking"></i></div>
          <h3>Gorilla Trekking</h3>
          <p>
            Experience the magic of meeting mountain gorillas in their natural
            habitat
          </p>
        </div>
        <div class="attraction-card">
          <div class="attraction-icon"><i class="fas fa-mountain"></i></div>
          <h3>Volcano Hiking</h3>
          <p>Conquer the majestic Virunga Mountains with breathtaking views</p>
        </div>
        <div class="attraction-card">
          <div class="attraction-icon"><i class="fas fa-masks-theater"></i></div>
          <h3>Cultural Experiences</h3>
          <p>Immerse yourself in authentic Rwandan traditions and customs</p>
        </div>

        <div class="attraction-card">
          <div class="attraction-icon"><i class="fas fa-camera-retro"></i></div>
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
        <a href="https://www.tripadvisor.com/Hotel_Review-g317075-d20326735-Reviews-Virunga_Homestay-Ruhengeri_Musanze_District_Northern_Province.html?m=19905" target="_blank" class="review-us-btn">Review Us on TripAdvisor</a>
        </br></br>
      </div>
    
    </section>

    <!-- Parallax Image Section -->
    <section class="parallax-section" style="width: 100%; height: 400px; position: relative; margin: 40px 0; background-image: url('./img/food.jpg'); background-attachment: fixed; background-position: center; background-repeat: no-repeat; background-size: cover;">
      <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4));">
        <div style="position: relative; height: 100%; display: flex; align-items: center; justify-content: center; color: white; text-align: center; padding: 0 20px;">
          <div>
            <h2 style="font-size: 2.5rem; margin-bottom: 15px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Experience Local Cuisine</h2>
            <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">Savor authentic Rwandan dishes prepared with locally-sourced ingredients</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">Why Choose Virunga Homestay in Musanze, Rwanda?</h2>
          <p class="section-subtitle">Experience authentic Rwandan hospitality with professional tourist services</p>
        </div>

        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-home"></i>
            </div>
            <h3>Authenticity and Warmth</h3>
            <p>Integrated within a local family residence, offering a welcoming, informal atmosphere that fosters genuine human connection.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-concierge-bell"></i>
            </div>
            <h3>Personalized Hospitality</h3>
            <p>Deeply committed to personalized guest care, from lending bicycles to preparing home-cooked meals with thoughtful attention to detail.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-map-marked-alt"></i>
            </div>
            <h3>Local Expertise</h3>
            <p>Access to insider knowledge about Musanze and the Virunga Massif region, with guidance from accredited bilingual specialists.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-utensils"></i>
            </div>
            <h3>Traditional Cuisine</h3>
            <p>Experience freshly prepared Rwandan dishes made from local ingredients, with opportunities to learn traditional recipes.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Community Connection</h3>
            <p>Foster meaningful relationships and create lasting friendships within our warm community atmosphere.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-leaf"></i>
            </div>
            <h3>Peaceful Setting</h3>
            <p>Enjoy tranquility away from urban noise, perfect for rest and reflection after wilderness adventures.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Safety & Trust</h3>
            <p>Experience peace of mind with our strong community reputation and commitment to guest wellbeing.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <i class="fas fa-hand-holding-heart"></i>
            </div>
            <h3>Responsible Tourism</h3>
            <p>Support local communities and cultural preservation through sustainable tourism practices.</p>
          </div>
        </div>

        <div class="why-choose-content">
          <p class="intro-text">Virunga Homestay offers a unique fusion of authentic local hospitality and professional tourist services in the heart of Musanze, the gateway to the majestic Virunga Massif. Our Tourist Information Centre is staffed by accredited bilingual specialists who provide tailored guidance for exploring Rwanda, Uganda, and the Democratic Republic of Congo.</p>
          
          <div class="commitment-statement" style="background-color: #212020;">
            <p style="color: white;">In essence, we represent an ideal choice for travelers seeking an authentic, professionally supported, and culturally rich experience in Musanze. We combine heartfelt hospitality with expert tourist assistance, ensuring every visit to the Virunga Massif is memorable, meaningful, and responsibly engaged.</p>
          </div>
        </div>
      </div>
    </section>
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
    </div>
    

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
   
    <?php include 'include/footer.php'; ?>

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

        // Show More Activities Functionality
        const showMoreBtn = document.getElementById('showMoreBtn');
        const experienceCards = document.querySelectorAll('.experience-card-link');
        let currentlyShown = 3;
        const cardsToShow = 3;

        if (showMoreBtn) {
          showMoreBtn.addEventListener('click', function() {
            const hiddenCards = document.querySelectorAll('.experience-card-hidden');
            const cardsToReveal = Math.min(cardsToShow, hiddenCards.length);
            
            for (let i = 0; i < cardsToReveal; i++) {
              hiddenCards[i].classList.remove('experience-card-hidden');
            }
            
            currentlyShown += cardsToReveal;
            
            // Hide button if all cards are shown
            if (currentlyShown >= experienceCards.length) {
              showMoreBtn.style.display = 'none';
            }
          });
        }
      });

      // Hero Slider Functionality
      const heroSlides = document.querySelectorAll(".hero-slide");
      const heroDots = document.querySelectorAll(".slider-dot");
      const heroPrevBtn = document.querySelector(".slider-control.prev");
      const heroNextBtn = document.querySelector(".slider-control.next");
      let heroCurrentSlide = 0;
      let heroSlideInterval;

      // Function to show a specific slide
      function showHeroSlide(index) {
        // Remove active class from all slides and dots
        heroSlides.forEach((slide) => slide.classList.remove("active"));
        heroDots.forEach((dot) => dot.classList.remove("active"));

        // Add active class to current slide and dot
        heroSlides[index].classList.add("active");
        heroDots[index].classList.add("active");

        heroCurrentSlide = index;
      }

      // Function to show next slide
      function nextHeroSlide() {
        let next = heroCurrentSlide + 1;
        if (next >= heroSlides.length) {
          next = 0;
        }
        showHeroSlide(next);
      }

      // Function to show previous slide
      function prevHeroSlide() {
        let prev = heroCurrentSlide - 1;
        if (prev < 0) {
          prev = heroSlides.length - 1;
        }
        showHeroSlide(prev);
      }

      // Start automatic sliding with 3-second interval
      function startHeroSlideInterval() {
        heroSlideInterval = setInterval(nextHeroSlide, 3000); // Change slide every 3 seconds
      }

      // Stop automatic sliding
      function stopHeroSlideInterval() {
        clearInterval(heroSlideInterval);
      }

      // Event listeners for controls
      if (heroPrevBtn) {
        heroPrevBtn.addEventListener("click", () => {
          stopHeroSlideInterval();
          prevHeroSlide();
          startHeroSlideInterval();
        });
      }

      if (heroNextBtn) {
        heroNextBtn.addEventListener("click", () => {
          stopHeroSlideInterval();
          nextHeroSlide();
          startHeroSlideInterval();
        });
      }

      // Event listeners for dots
      heroDots.forEach((dot, index) => {
        dot.addEventListener("click", () => {
          stopHeroSlideInterval();
          showHeroSlide(index);
          startHeroSlideInterval();
        });
      });

      // Start the slider
      startHeroSlideInterval();

      // Pause slider when hovering over controls
      const heroControls = document.querySelector(".hero-slider-controls");
      if (heroControls) {
        heroControls.addEventListener("mouseenter", stopHeroSlideInterval);
        heroControls.addEventListener("mouseleave", startHeroSlideInterval);
      }
    </script>
  </body>
</html>
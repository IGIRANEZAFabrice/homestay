<?php
  $pageTitle = 'Virunga Homestay - Rooms';
  $pageCss = ['page-hero.css','room-cards.css','room.css'];
  $pageHeroKey = 'rooms';
  $pageScripts = ['home.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>
   <!-- ══ ROOMS ══ -->
    <section id="rooms">
      <div class="section-container">
        <div class="section-label" id="roomsLabel">Our Accommodations</div>
        <h2 class="section-heading" id="roomsHeading">Our Stay Experience</h2>
        <div class="rooms-intro-section">
          <p class="rooms-intro-text">
            Experience Comfort and Culture at Virunga Homestay. Virunga Homestay offers a range of thoughtfully designed accommodations that combine comfort, local charm, and modern amenities, creating an authentic Rwandan experience for every guest.
          </p>
          
          <div class="rooms-features-grid">
            <div class="feature-card">
              <h3 class="feature-card__title">Private Rooms</h3>
              <p class="feature-card__text">
                Our private rooms provide a serene retreat with comfortable beds, clean linens, and ensuite bathrooms. Decor inspired by Rwandan heritage adds a warm, welcoming touch, while large windows frame stunning views of the Virunga Massif. Ideal for couples, solo travelers, or anyone seeking privacy and relaxation.
              </p>
            </div>
            
            <div class="feature-card">
              <h3 class="feature-card__title">Shared Rooms</h3>
              <p class="feature-card__text">
                Perfect for groups, families, or adventurous travelers, our shared rooms offer cozy beds in a safe, friendly environment with shared bathroom facilities. These spaces encourage social interaction while maintaining comfort and convenience for every guest.
              </p>
            </div>
            
            <div class="feature-card">
              <h3 class="feature-card__title">Communal Spaces</h3>
              <p class="feature-card__text">
                Guests can gather in our inviting shared areas, including the lounge, communal dining area, and conservation-themed sitting room. These spaces provide opportunities to relax, connect with other travelers, and immerse in local culture, making each stay more engaging and memorable.
              </p>
            </div>
          </div>
          
          <div class="rooms-footer-info">
            <p>Where the Virunga mountains rises in the distance, Virunga Homestay opens its doors to comfort, connection, and an authentic glimpse of life in this breathtaking region.</p>
            <a href="<?php echo isset($baseLink) ? $baseLink('bookinginfo') : 'bookinginfo.php'; ?>" class="booking-link">
               View Booking Information
            </a>

            <div class="direct-booking-notice">
              <div class="direct-booking-notice__icon">
                <i class="fa-solid fa-tag"></i>
              </div>
              <div class="direct-booking-notice__content">
                <h3 class="direct-booking-notice__title">Unlock Direct Booking Discounts</h3>
                <p class="direct-booking-notice__text">
                  Guests who book directly through our email or WhatsApp/phone enjoy exclusive discounts and special rates not offered on external booking platforms. This guarantees you the best price for your stay.
                </p>
              </div>
            </div>
          </div>
        </div>
          <div class="rooms-grid" id="roomsGrid">
            <?php
            require_once __DIR__ . '/../config/db.php';
            $sql = "SELECT * FROM rooms WHERE status = 'active'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imgVal = !empty($row['image']) ? $row['image'] : 'services/1.JPG';
                    if (strpos($imgVal, 'http') === 0) {
                        $image = $imgVal;
                    } else {
                        $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                        if (strpos($imgVal, '/') === false) {
                            $imgVal = 'rooms/' . $imgVal;
                        }
                        $image = './img/' . $imgVal;
                    }
                    $image = htmlspecialchars($image);
                    $title = htmlspecialchars($row['title']);
                    $meters = isset($row['meters']) ? (int)$row['meters'] : 0;
                    $guests = isset($row['guest_number']) ? (int)$row['guest_number'] : 2;
                    $bed = isset($row['bed_type']) ? htmlspecialchars($row['bed_type']) : 'King Bed';
                    $price_single = isset($row['price_single']) ? (int)$row['price_single'] : 0;
                    $price_double = isset($row['price_double']) ? (int)$row['price_double'] : 0;
                    
                    // Generate a default tag
                    $tag = 'Premium';
                    
                    $whatsappMsg = rawurlencode("Hello Francis, I would like to book the " . $title . " room");
                    $whatsappUrl = "https://wa.me/250784513435?text=" . $whatsappMsg;
                    
                    echo '
                    <a class="room-card" href="' . $whatsappUrl . '" target="_blank" rel="noopener">
                      <img class="room-card__img" src="' . $image . '" alt="' . $title . '" />
                      <div class="room-card__overlay"></div>
                      <span class="room-card__tag">' . $tag . '</span>
                      <div class="room-card__content">
                        <h3 class="room-card__name">' . $title . '</h3>
                        <div class="room-card__pricing-grid">
                          <div class="room-card__price">
                            <span class="amount">$' . $price_single . '</span>
                            <span class="per">Single Occupancy / night</span>
                          </div>
                          <div class="room-card__price">
                            <span class="amount">$' . $price_double . '</span>
                            <span class="per">Double Occupancy / night</span>
                          </div>
                        </div>
                        <div class="room-card__details-reveal">
                          <div class="room-card__meta">
                            <span><i class="fa-solid fa-maximize"></i> ' . $meters . ' m²</span>
                            <span><i class="fa-solid fa-user-group"></i> ' . $guests . ' Guests</span>
                            <span><i class="fa-solid fa-bed"></i> ' . $bed . '</span>
                          </div>
                          <div class="room-card__amenities">
                             <div class="room-card__amenity">
                               <i class="fa-solid fa-mug-hot"></i> Breakfast included in a stay
                             </div>
                             <div class="room-card__amenity">
                               <i class="fa-solid fa-wifi"></i> Free High-Speed WiFi
                             </div>
                             <div class="room-card__amenity">
                               <i class="fa-solid fa-temperature-arrow-up"></i> Hot Shower
                             </div>
                           </div>
                        </div>
                      </div>
                      <span class="room-card__cta">Book Now <i class="fa-solid fa-arrow-right fa-xs"></i></span>
                    </a>';
                }
            } else {
                echo '<p style="grid-column: 1 / -1; text-align: center;">No rooms available at the moment.</p>';
            }
            ?>
          </div>
        <div class="rooms-actions" id="roomsActions">
          <button class="btn-primary" onclick="window.location.href=\'<?php echo isset($baseLink) ? $baseLink('rooms') : 'rooms.php'; ?>\'">
            <i class="fa-solid fa-grid-2 fa-xs"></i> View All Rooms
          </button>
          <a class="btn-ghost" href="<?php echo isset($baseLink) ? $baseLink('bookinginfo') : 'bookinginfo.php'; ?>" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
            Booking Information
          </a>
        </div>
      </div>
    </section>

<?php include 'includes/footer.php'; ?>

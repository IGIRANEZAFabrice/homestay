<?php
// Include database connection
require_once '../include/connection.php';
require_once '../include/image_helpers.php';

// Fetch all rooms from the database
$sql = "SELECT * FROM rooms ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Rooms | Virunga Homestay</title>
  
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
  <section class="rooms-hero">
    <h1>Our Rooms</h1>
    <p>Experience comfort and culture in our thoughtfully designed rooms</p>
  </section>
  
  <main>
    <!-- New Intro Section -->
    <section class="rooms-intro">
      <div class="intro-container">
        <h2 class="intro-title">Experience Comfort and Culture at Virunga Homestay</h2>
        <p class="intro-description">Virunga Homestay offers a range of thoughtfully designed accommodations that combine comfort, local charm, and modern amenities, creating an authentic Rwandan experience for every guest.</p>
        
        <div class="accommodation-types">
          <div class="accommodation-card">
            <div class="accommodation-icon">
              <i class="fas fa-bed"></i>
            </div>
            <h3>Private Rooms</h3>
            <p>Our private rooms provide a serene retreat with comfortable beds, clean linens, and ensuite bathrooms. Decor inspired by Rwandan heritage adds a warm, welcoming touch, while large windows frame stunning views of the Virunga Massif. Ideal for couples, solo travelers, or anyone seeking privacy and relaxation.</p>
          </div>
          
          <div class="accommodation-card">
            <div class="accommodation-icon">
              <i class="fas fa-users"></i>
            </div>
            <h3>Shared Rooms</h3>
            <p>Perfect for groups, families, or adventurous travelers, our shared rooms offer cozy beds in a safe, friendly environment with shared bathroom facilities. These spaces encourage social interaction while maintaining comfort and convenience for every guest.</p>
          </div>
          
          <div class="accommodation-card">
            <div class="accommodation-icon">
              <i class="fas fa-couch"></i>
            </div>
            <h3>Communal Spaces</h3>
            <p>Guests can gather in our inviting shared areas, including the lounge, communal dining area, and conservation-themed sitting room. These spaces provide opportunities to relax, connect with other travelers, and immerse in local culture, making each stay more engaging and memorable.</p>
          </div>
        </div>
        
        <p class="intro-footer">At Virunga Homestay, every room and shared space is designed to ensure comfort, connection, and a true taste of life near the breathtaking Virunga Massif.</p>
        
        <a href="booking.php" class="booking-button">
          <i class="fas fa-calendar-check"></i> View Booking Information
        </a>
      </div>
    </section>
    
    <section class="rooms-container">
      <div class="rooms-grid">
        <?php if($result && $result->num_rows > 0): ?>
          <?php while($room = $result->fetch_assoc()): ?>
            <div class="room-card">
              <div class="room-image">
                <?php if($room['image'] && $room['image'] != 'default-room.jpg'): ?>
                  <img src="../<?php echo buildImageUrl($room['image'], 'rooms'); ?>" alt="<?php echo $room['title']; ?>" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                <?php else: ?>
                  <!-- No image available -->
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
                  <a href="#" class="btn btn-secondary read-more-btn" 
                     data-room-title="<?php echo strtoupper($room['title']); ?>"
                     data-room-description="<?php echo $room['description']; ?>">Read More</a>
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
    </section>
  </main>
  
  <!-- Room Details Modal -->
  <div class="modal" id="roomDetailsModal">
    <div class="modal-content">
      <span class="close-modal" id="closeRoomDetailsModal">&times;</span>
      <h2 id="roomModalTitle"></h2>
      <p id="roomModalDescription"></p>
    </div>
  </div>
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
    // Room Details Modal Functionality
    const roomDetailsModal = document.getElementById('roomDetailsModal');
    const roomModalTitle = document.getElementById('roomModalTitle');
    const roomModalDescription = document.getElementById('roomModalDescription');
    const closeRoomDetailsModal = document.getElementById('closeRoomDetailsModal');
    const readMoreBtns = document.querySelectorAll('.read-more-btn');
    
    // Open modal with room details
    readMoreBtns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        const title = this.getAttribute('data-room-title');
        const description = this.getAttribute('data-room-description');
        
        roomModalTitle.textContent = title;
        roomModalDescription.textContent = description;
        roomDetailsModal.style.display = 'block';
        roomDetailsModal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
      });
    });
    
    // Close modal
    closeRoomDetailsModal.addEventListener('click', function() {
      roomDetailsModal.style.display = 'none';
      roomDetailsModal.classList.remove('show');
      document.body.style.overflow = 'auto'; // Restore background scrolling
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
      if (e.target === roomDetailsModal) {
        roomDetailsModal.style.display = 'none';
        roomDetailsModal.classList.remove('show');
        document.body.style.overflow = 'auto';
      }
    });
  </script>
  <script src="../js/header.js"></script>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</body>
</html>

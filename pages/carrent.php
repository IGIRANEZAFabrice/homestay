<?php
require_once '../include/connection.php';
$cars = [];
$result = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $row['features'] = json_decode($row['features'], true) ?: [];
    $cars[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental - Virunga Homestay</title>
    <link rel="stylesheet" href="../css/carrent.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/rooms.css">
    <link rel="stylesheet" href="../css/logo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'include/header.php'; ?>
    <!-- Hero Section -->
    <section class="rooms-hero">
        <h1>Rent/ Lease a travel car</h1>
        <p>Discover our range of reliable, comfortable, and well-maintained vehicles for your travels. Whether you need an SUV for adventure, a sedan for city drives, or a luxury car for special occasions, we have the perfect option for you. All cars are regularly serviced and equipped with modern features to ensure a safe and enjoyable journey.</p>
    </section>


    <!-- Car Rental Service Info Section -->
    <section class="rental-info-section">
        <div class="container">
            <div class="rental-info-content">
                <div class="rental-info-text">
                    <h2>Car Rental Services at Virunga Homestay – Travel the Virunga Massif with Comfort and Ease</h2>
                    <p>Virunga Homestay offers professional car rental services with experienced drivers, providing safe, reliable, and comfortable transportation for all your travel needs around the Virunga Massif. Whether you are trekking to see mountain gorillas, exploring natural wonders, or embarking on adventure trips, our fleet is tailored to meet the requirements of every traveler.</p>
                    <h3>Our Fleet Options:</h3>
                    <ul>
                        <li>
                            <i class="fas fa-truck-monster"></i>
                            <strong>4x4 SUVs</strong>
                            Designed for off-road adventures, rugged terrain, and national park excursions. Ideal for trekkers and adventure travelers seeking comfort and reliability in challenging landscapes.
                        </li>
                        <li>
                            <i class="fas fa-car"></i>
                            <strong>Sedans</strong>
                            Perfect for couples or small groups looking for efficient and comfortable transportation for city tours, airport transfers, or short journeys.
                        </li>
                        <li>
                            <i class="fas fa-shuttle-van"></i>
                            <strong>Minivans & Coasters</strong>
                            Spacious and versatile, these vehicles accommodate larger groups, families, or student tours, offering ample room for passengers and luggage.
                        </li>
                        <li>
                            <i class="fas fa-truck-pickup"></i>
                            <strong>Pickups & Specialized Off-Road Vehicles</strong>
                            Ideal for trekking expeditions, photographers, or guests with equipment requiring extra cargo space and maneuverability.
                        </li>
                    </ul>
                    <h3>Professional Drivers:</h3>
                    <p>Our drivers are licensed, highly trained, and intimately familiar with the Virunga region. They ensure safe travel while sharing local knowledge, cultural insights, and tips to enhance your journey.</p>
                    <h3>Tailored Services:</h3>
                    <ul>
                        <li>
                            <i class="fas fa-plane-arrival"></i>
                            <strong>Airport Transfers</strong>
                            Convenient pickups and drop-offs at local airports with professional drivers and comfortable vehicles.
                        </li>
                        <li>
                            <i class="fas fa-hiking"></i>
                            <strong>Day Trips</strong>
                            Exciting excursions for gorilla trekking, nature explorations, and sightseeing around the Virunga region.
                        </li>
                        <li>
                            <i class="fas fa-route"></i>
                            <strong>Multi-day Excursions</strong>
                            Comprehensive journeys with planned stops at key attractions and comfortable accommodations.
                        </li>
                        <li>
                            <i class="fas fa-users"></i>
                            <strong>Custom Itineraries</strong>
                            Personalized travel plans for schools, student groups, families, and corporate travelers.
                        </li>
                    </ul>
                    <p>At Virunga Homestay, our car rental services combine comfort, safety, and convenience, allowing you to focus entirely on exploring the breathtaking landscapes and unique experiences of the Virunga Massif. Every journey with us is smooth, reliable, and memorable.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cars Grid Section -->
    <section class="cars-section">
        <div class="container">
        
           
            <div class="cars-grid">
                <?php if (count($cars) === 0): ?>
                    <div style="padding:2rem;text-align:center;color:#888;">No cars available at the moment.</div>
                <?php else: ?>
                <?php foreach ($cars as $car): ?>
                <?php
                    // Determine price range for filtering
                    $price = floatval($car['price']);
                    if ($price < 100) $priceRange = 'budget';
                    elseif ($price < 200) $priceRange = 'mid';
                    else $priceRange = 'premium';
                ?>
                <div class="car-card" data-type="<?php echo htmlspecialchars($car['type']); ?>" data-price="<?php echo $priceRange; ?>" data-transmission="<?php echo htmlspecialchars($car['transmission']); ?>">
                    <div class="car-image">
                        <img src="<?php echo '../homestay/uploads/cars/' . htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
                        <?php if ($car['badge']): ?>
                            <div class="car-badge"><?php echo htmlspecialchars($car['badge']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="car-details">
                        <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                        <div class="car-specs">
                            <span><i class="fas fa-car"></i> <?php echo strtoupper(htmlspecialchars($car['type'])); ?></span>
                            <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                            <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                        </div>
                        <div class="car-features">
                            <?php 
                            $icons = [
                                'ac' => 'snowflake',
                                'audio' => 'music',
                                'wifi' => 'wifi',
                                'bluetooth' => 'bluetooth',
                                'climate' => 'temperature-low',
                                'premium' => 'music'
                            ];
                            foreach ($car['features'] as $feature): ?>
                                <span><i class="fas fa-<?php echo $icons[$feature] ?? 'check'; ?>"></i> <?php echo htmlspecialchars($feature); ?></span>
                            <?php endforeach; ?>
                        </div>
                       
                        <a href="https://wa.me/+250788123456?text=Hello! I'd like to book the <?php echo htmlspecialchars(urlencode($car['name'])); ?> car from Virunga Homestay" class="book-btn">
                            <i class="fab fa-whatsapp"></i> Book Now
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
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
    <script src="../js/carrent.js"></script>
</body>
</html>

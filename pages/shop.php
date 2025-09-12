<?php
require_once '../include/connection.php';
// Fetch all shop items
$items = [];
$sql = "SELECT * FROM shop_items ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traditional Shop - Virunga Homestay</title>
    <link rel="stylesheet" href="../css/shop.css">
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
        <h1>Discover Rwanda's Heritage Treasures</h1>
        <p>Experience comfort and culture in all traditional items</p>
    </section>
  

    <!-- Shop Impact Section -->
    <section class="shop-impact">
        <div class="container">
            <div class="impact-content">
                <h2 class="impact-title">Shop at Virunga Homestay – Unique Souvenirs with Meaning</h2>
                <p class="impact-lead">At Virunga Homestay, our shop offers guests an opportunity to explore and purchase exclusive, handcrafted souvenirs that capture the spirit of the Virunga region. Each item is skillfully made by women artisans from a cooperative supported by our Community-Based Tourism (CBT) program, ensuring authenticity and cultural value.</p>
                <div class="impact-body">
                    <p>When you purchase these beautiful artworks, you are not just taking home a memorable keepsake—you are also supporting local artisans and contributing to their economic empowerment. Your choice helps foster sustainable livelihoods, preserve traditional craftsmanship, and create meaningful opportunities for women in our community.</p>
                    <p>We sincerely appreciate your support and the positive impact you make in the lives of our artisans.</p>
                </div>
                <div class="impact-contact">
                    <h3>For more information</h3>
                    <ul class="contact-list">
                        <li>
                            <i class="fa-brands fa-whatsapp"></i>
                            <a href="https://wa.me/250784513435" target="_blank" rel="noopener">WhatsApp/Phone: +250 784 513 435</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <a href="mailto:virungahomestay@gmail.com">virungahomestay@gmail.com</a>
                        </li>
                    </ul>
                    <p class="impact-note">Take home a piece of the Virunga Massif while making a real difference in our community.</p>
                </div>
            </div>
        </div>
    </section>
   
    <!-- Products Section -->
    <section class="products-section">
        <div class="section-title" style="text-align:center;font-size:2.2rem;color:#b85c19;margin-bottom:0.5rem;font-family:'Playfair Display',serif;font-weight:700;">Featured Traditional Items</div>
        <div class="section-desc" style="text-align:center;color:#666;font-size:1.1rem;margin-bottom:2.5rem;font-weight:400;">Each piece is a celebration of Rwandan heritage, made with care by local artisans.</div>
        <div class="container">
         

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (empty($items)): ?>
                  <div style="grid-column: 1/-1; text-align:center; color:#b85c19; font-size:1.3rem; padding:2rem 0;">No items available in the shop yet. Please check back soon!</div>
                <?php else: ?>
                  <?php foreach ($items as $item): ?>
                    <div class="product-card" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                      <div class="product-image">
                        <img src="<?php echo '../homestay/' . htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">

                        <?php if (!empty($item['tag'])): ?>
                          <div class="product-badge"><?php echo htmlspecialchars($item['tag']); ?></div>
                        <?php endif; ?>
                        
                      </div>
                      <div class="product-details">
                        <h3 style="text-transform:uppercase;" ><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p style="text-transform:capitalize;"  class="product-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="product-price-action">
                        
                          <a href="https://wa.me/250784513435?text=I'm%20interested%20in%20buying%20<?php echo urlencode($item['title']); ?>" target="_blank" class="whatsapp-button-small">
                            <i class="fab fa-whatsapp"></i> Buy Now
                          </a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <?php include 'include/footer.php'; ?>

    <script src="../js/shop.js"></script>
    <script src="../js/header.js"></script>
</body>
</html>
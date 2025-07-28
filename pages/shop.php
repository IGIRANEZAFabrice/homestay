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
    <h1>Our Tradition | our tressure</h1>
    <p>Experience comfort and culture in all traditional items</p>
  </section>
  

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-subtitle" style="text-align:center;color:#b85c19;font-size:1.15rem;margin-bottom:2.5rem;font-weight:500;letter-spacing:0.5px;">Shop by Category – Find unique crafts, textiles, jewelry, and art that tell a story.</div>
        <div class="container">
            <div class="categories-grid">
                <a href="#handicrafts" class="category-card">
                    <img src="../img/shop/handicrafts.jpg" alt="Handicrafts">
                    <h3>Handicrafts</h3>
                </a>
                <a href="#textiles" class="category-card">
                    <img src="../img/shop/textiles.jpg" alt="Textiles">
                    <h3>Textiles</h3>
                </a>
                <a href="#jewelry" class="category-card">
                    <img src="../img/shop/jewelry.jpg" alt="Jewelry">
                    <h3>Jewelry</h3>
                </a>
                <a href="#art" class="category-card">
                    <img src="../img/shop/art.jpg" alt="Art">
                    <h3>Art</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-title" style="text-align:center;font-size:2.2rem;color:#b85c19;margin-bottom:0.5rem;font-family:'Playfair Display',serif;font-weight:700;">Featured Traditional Items</div>
        <div class="section-desc" style="text-align:center;color:#666;font-size:1.1rem;margin-bottom:2.5rem;font-weight:400;">Each piece is a celebration of Rwandan heritage, made with care by local artisans.</div>
        <div class="container">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search products...">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-options">
                    <select id="categoryFilter">
                        <option value="all">All Categories</option>
                        <option value="handicrafts">Handicrafts</option>
                        <option value="textiles">Textiles</option>
                        <option value="jewelry">Jewelry</option>
                        <option value="art">Art</option>
                    </select>
                    <select id="priceFilter">
                        <option value="all">All Prices</option>
                        <option value="budget">Under $20</option>
                        <option value="mid">$20 - $50</option>
                        <option value="premium">Over $50</option>
                    </select>
                    <select id="sortBy">
                        <option value="featured">Featured</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="newest">Newest First</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php if (empty($items)): ?>
                  <div style="grid-column: 1/-1; text-align:center; color:#b85c19; font-size:1.3rem; padding:2rem 0;">No items available in the shop yet. Please check back soon!</div>
                <?php else: ?>
                  <?php foreach ($items as $item): ?>
                    <div class="product-card" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                      <div class="product-image">
                        <img src="<?php echo '../uploads/shop/' . htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if (!empty($item['tag'])): ?>
                          <div class="product-badge"><?php echo htmlspecialchars($item['tag']); ?></div>
                        <?php endif; ?>
                        <div class="product-actions">
                          <button class="action-btn" onclick="quickView('<?php echo htmlspecialchars($item['id']); ?>')">
                            <i class="fas fa-eye"></i>
                          </button>
                          <button class="action-btn" onclick="addToCart('<?php echo htmlspecialchars($item['id']); ?>')">
                            <i class="fas fa-shopping-cart"></i>
                          </button>
                        </div>
                      </div>
                      <div class="product-details">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="product-price">
                          <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                        </div>
                        <!-- Optionally, you can add a static rating or leave it out -->
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="quick-view-content">
                <div class="quick-view-image">
                    <img src="" alt="Product Image" id="modalProductImage">
                </div>
                <div class="quick-view-details">
                    <h2></h2>
                    <p class="product-description"></p>
                    <div class="product-price">
                        <span class="price"></span>
                        <span class="original-price"></span>
                    </div>
                    <div class="product-rating"></div>
                    <div class="quantity-selector">
                        <button class="quantity-btn minus">-</button>
                        <input type="number" value="1" min="1" max="10">
                        <button class="quantity-btn plus">+</button>
                    </div>
                    <button class="add-to-cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping Cart Sidebar -->
    <div id="cartSidebar" class="cart-sidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="close-cart">&times;</button>
        </div>
        <div class="cart-items">
            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span class="total-amount">$0.00</span>
            </div>
            <button class="checkout-btn">
                <i class="fas fa-lock"></i>
                Proceed to Checkout
            </button>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div id="cartOverlay" class="cart-overlay"></div>
    <?php include 'include/footer.php'; ?>

    <script src="../js/shop.js"></script>
</body>
</html>

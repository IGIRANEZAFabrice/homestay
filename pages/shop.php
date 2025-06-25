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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'include/header.php'; ?>
    <!-- Hero Section -->
    <section class="rooms-hero">
    <h1>Our Cultural shop</h1>
    <p>Discover unique, handcrafted items that celebrate Rwandan culture and tradition.</p>
  </section>

    <!-- Categories Section -->
    <section class="categories-section">
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
                <!-- Product Card 1 -->
                <div class="product-card" data-category="handicrafts" data-price="mid">
                    <div class="product-image">
                        <img src="../img/shop/products/basket.jpg" alt="Traditional Basket">
                        <div class="product-badge">Best Seller</div>
                        <div class="product-actions">
                            <button class="action-btn" onclick="quickView('basket')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart('basket')">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3>Traditional Basket</h3>
                        <p class="product-description">Hand-woven basket made from natural fibers</p>
                        <div class="product-price">
                            <span class="price">$35</span>
                            <span class="original-price">$45</span>
                        </div>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>(24)</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="product-card" data-category="textiles" data-price="budget">
                    <div class="product-image">
                        <img src="../img/shop/products/kitenge.jpg" alt="Kitenge Fabric">
                        <div class="product-actions">
                            <button class="action-btn" onclick="quickView('kitenge')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart('kitenge')">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3>Kitenge Fabric</h3>
                        <p class="product-description">Colorful traditional fabric, perfect for clothing</p>
                        <div class="product-price">
                            <span class="price">$15</span>
                        </div>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>(18)</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="product-card" data-category="jewelry" data-price="premium">
                    <div class="product-image">
                        <img src="../img/shop/products/necklace.jpg" alt="Traditional Necklace">
                        <div class="product-badge">New</div>
                        <div class="product-actions">
                            <button class="action-btn" onclick="quickView('necklace')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart('necklace')">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3>Traditional Necklace</h3>
                        <p class="product-description">Handcrafted bead necklace with traditional patterns</p>
                        <div class="product-price">
                            <span class="price">$65</span>
                        </div>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>(32)</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="product-card" data-category="art" data-price="mid">
                    <div class="product-image">
                        <img src="../img/shop/products/painting.jpg" alt="Traditional Painting">
                        <div class="product-actions">
                            <button class="action-btn" onclick="quickView('painting')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn" onclick="addToCart('painting')">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="product-details">
                        <h3>Traditional Painting</h3>
                        <p class="product-description">Hand-painted scene of Rwandan village life</p>
                        <div class="product-price">
                            <span class="price">$45</span>
                            <span class="original-price">$60</span>
                        </div>
                        <div class="product-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>(15)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick View Modal -->
    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="quick-view-content">
                <div class="quick-view-image">
                    <img src="" alt="Product Image">
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

    <script src="../js/shop.js"></script>
</body>
</html>

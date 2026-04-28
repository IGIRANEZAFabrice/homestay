<?php
  $pageTitle   = 'Virunga Homestay – Shop';
  $pageCss     = ['page-hero.css', 'shop.css'];
  $pageHeroKey = 'shop';
  $pageScripts = ['shop.js'];
  include 'includes/header.php';
?>
<?php include 'page-hero.php'; ?>

<!-- ══════════════════════════════════════════════════════
  MARQUEE TICKER
══════════════════════════════════════════════════════ -->
<div class="sp-ticker" aria-hidden="true">
  <div class="sp-ticker__track">
    <?php
    require_once __DIR__ . '/../config/db.php';
    $sqlTicker = "SELECT title FROM shop_items WHERE status = 'active' ORDER BY id DESC LIMIT 10";
    $resTicker = $conn->query($sqlTicker);
    $tickerItems = [];
    if ($resTicker && $resTicker->num_rows > 0) {
        while ($rowT = $resTicker->fetch_assoc()) {
            $tickerItems[] = htmlspecialchars($rowT['title']);
        }
    }
    // Echo items multiple times to ensure smooth infinite scrolling width
    if (!empty($tickerItems)) {
        for ($i = 0; $i < 3; $i++) {
            foreach ($tickerItems as $item) {
                echo '<span>' . $item . '</span><span class="sp-ticker__dot">✦</span>';
            }
        }
    }
    ?>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
  SHOP INTRO
══════════════════════════════════════════════════════ -->
<section class="sp-intro" id="spIntro">
  <div class="sp-intro__orb sp-intro__orb--tl"></div>
  <div class="sp-intro__orb sp-intro__orb--br"></div>
  <div class="sp-container">
    <div class="sp-intro__grid">
      <div class="sp-intro__left" data-reveal="fade-up">
        <span class="sp-eyebrow"><i class="fa-solid fa-store"></i> The Virunga Collection</span>
        <h2 class="sp-intro__title">
          Carry a piece of<br><em>Rwanda</em> home
        </h2>
        <p class="sp-intro__desc">
          Every item in our shop is sourced directly from local artisans, weavers, farmers, and
          craftspeople in the Musanze region. When you buy here, you invest directly in the
          communities that make Virunga extraordinary.
        </p>
        <?php
        // Get real stats from database
        $sqlProducts = "SELECT COUNT(*) as total FROM shop_items WHERE status = 'active'";
        $resProducts = $conn->query($sqlProducts);
        $totalProducts = $resProducts ? $resProducts->fetch_assoc()['total'] : 0;
        
        $sqlCategories = "SELECT COUNT(DISTINCT category) as total FROM shop_items WHERE status = 'active' AND category IS NOT NULL AND category != ''";
        $resCategories = $conn->query($sqlCategories);
        $totalCategories = $resCategories ? $resCategories->fetch_assoc()['total'] : 0;
        
        // Estimate artisans - we'll use a reasonable estimate based on categories
        // In a real scenario, you might have an artisans table or artisan_id field
        $totalArtisans = max($totalCategories * 2, 1); // At least 2 artisans per category
        ?>
        <div class="sp-intro__stats">
          <div class="sp-stat">
            <span class="sp-stat__num" data-count="<?php echo $totalProducts; ?>">0</span>
            <span class="sp-stat__label">Products</span>
          </div>
          <div class="sp-stat">
            <span class="sp-stat__num" data-count="<?php echo $totalArtisans; ?>">0</span>
            <span class="sp-stat__label">Local Artisans</span>
          </div>
          <div class="sp-stat">
            <span class="sp-stat__num" data-count="<?php echo $totalCategories; ?>">0</span>
            <span class="sp-stat__label">Categories</span>
          </div>
        </div>
      </div>
      <div class="sp-intro__right" data-reveal="scale-in" data-delay="150">
        <div class="sp-intro__img-cluster">
          <?php
          require_once __DIR__ . '/../config/db.php';
          $sqlIntro = "SELECT image FROM shop_items WHERE status = 'active' ORDER BY id DESC LIMIT 3";
          $resIntro = $conn->query($sqlIntro);
          $introImgs = [];
          if ($resIntro && $resIntro->num_rows > 0) {
              while ($rowI = $resIntro->fetch_assoc()) {
                  $imgVal = !empty($rowI['image']) ? $rowI['image'] : 'shop/2.jpg';
                  if (strpos($imgVal, 'http') === 0) {
                      $introImgs[] = $imgVal;
                  } else {
                      $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
                      if (strpos($imgVal, '/') === false) {
                          $imgVal = 'shop/' . $imgVal;
                      }
                      $introImgs[] = './img/' . $imgVal;
                  }
              }
          }
          ?>
          <?php if (isset($introImgs[0])): ?>
          <div class="sp-intro__img sp-intro__img--a">
            <img src="<?php echo htmlspecialchars($introImgs[0]); ?>" alt="Rwandan craft">
          </div>
          <?php endif; ?>
          <?php if (isset($introImgs[1])): ?>
          <div class="sp-intro__img sp-intro__img--b">
            <img src="<?php echo htmlspecialchars($introImgs[1]); ?>" alt="Specialty local">
          </div>
          <?php endif; ?>
          <?php if (isset($introImgs[2])): ?>
          <div class="sp-intro__img sp-intro__img--c">
            <img src="<?php echo htmlspecialchars($introImgs[2]); ?>" alt="Woven textiles">
          </div>
          <?php endif; ?>
          <div class="sp-intro__badge">
            <i class="fa-solid fa-leaf"></i>
            <span>100% Local &amp; Ethical</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
  FEATURED PRODUCT SPOTLIGHT
══════════════════════════════════════════════════════ -->
<section class="sp-featured" id="spFeatured">
  <div class="sp-container">
    <div class="sp-featured__inner" data-reveal="fade-up">
      <div class="sp-featured__label">
        <i class="fa-solid fa-star"></i> Featured This Month
      </div>
      <?php
      $sqlFeat = "SELECT * FROM shop_items WHERE status = 'active' ORDER BY id DESC LIMIT 1";
      $resFeat = $conn->query($sqlFeat);
      if ($resFeat && $resFeat->num_rows > 0) {
          $feat = $resFeat->fetch_assoc();
          
          $imgVal = !empty($feat['image']) ? $feat['image'] : 'shop/agaseke.jpg';
          if (strpos($imgVal, 'http') === 0) {
              $featImg = $imgVal;
          } else {
              $imgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($imgVal, '/'));
              if (strpos($imgVal, '/') === false) {
                  $imgVal = 'shop/' . $imgVal;
              }
              $featImg = './img/' . $imgVal;
          }
          
          $fBadge    = !empty($feat['badge']) ? htmlspecialchars($feat['badge']) : '';
          $fTag      = !empty($feat['tag']) ? htmlspecialchars($feat['tag']) : '';
          $fCat      = !empty($feat['category']) ? htmlspecialchars($feat['category']) : 'Crafts';
          $fTitle    = htmlspecialchars($feat['title']);
          $fDesc     = htmlspecialchars($feat['description']);
          $fMeta1    = !empty($feat['meta_1']) ? htmlspecialchars($feat['meta_1']) : '';
          $fMeta2    = !empty($feat['meta_2']) ? htmlspecialchars($feat['meta_2']) : '';
          $fMeta3    = !empty($feat['meta_3']) ? htmlspecialchars($feat['meta_3']) : '';
          $fPrice    = htmlspecialchars($feat['price']);
          $fId       = $feat['id'];
          ?>
          <div class="sp-featured__grid">
            <div class="sp-featured__img-wrap">
              <img src="<?php echo htmlspecialchars($featImg); ?>" alt="<?php echo $fTitle; ?>" class="sp-featured__img">
              <?php if ($fTag): ?>
                <div class="sp-featured__img-tag"><?php echo $fTag; ?></div>
              <?php endif; ?>
              <?php if ($fBadge): ?>
                <div class="sp-featured__img-badge">
                  <span><?php echo $fBadge; ?></span>
                </div>
              <?php endif; ?>
            </div>
            <div class="sp-featured__content">
              <span class="sp-featured__cat"><i class="fa-solid fa-weebly"></i> <?php echo $fCat; ?></span>
              <h3 class="sp-featured__title"><?php echo $fTitle; ?></h3>
              <p class="sp-featured__desc">
                <?php echo $fDesc; ?>
              </p>
              <div class="sp-featured__meta">
                <?php if ($fMeta1): ?><span><i class="fa-solid fa-ruler-combined"></i> <?php echo $fMeta1; ?></span><?php endif; ?>
                <?php if ($fMeta2): ?><span><i class="fa-solid fa-seedling"></i> <?php echo $fMeta2; ?></span><?php endif; ?>
                <?php if ($fMeta3): ?><span><i class="fa-solid fa-handshake-angle"></i> <?php echo $fMeta3; ?></span><?php endif; ?>
              </div>
              <div class="sp-featured__price-row">
                <span class="sp-featured__price">$<?php echo $fPrice; ?></span>
              </div>
              <div class="sp-featured__actions">
                <button class="sp-btn sp-btn--primary sp-add-to-cart"
                  data-id="<?php echo $fId; ?>"
                  data-name="<?php echo $fTitle; ?>"
                  data-price="<?php echo $fPrice; ?>"
                  data-img="<?php echo htmlspecialchars($featImg); ?>">
                  <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                </button>
                <button class="sp-btn sp-btn--ghost sp-wishlist" data-id="<?php echo $fId; ?>">
                  <i class="fa-regular fa-heart"></i>
                </button>
              </div>
            </div>
          </div>
          <?php
      } else {
          echo "<p style='text-align:center;'>No featured item found.</p>";
      }
      ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
  FILTER + SEARCH BAR
══════════════════════════════════════════════════════ -->
<section class="sp-catalog-controls" id="spControls">
  <div class="sp-container">
    <div class="sp-controls__row">

      <!-- Search -->
      <div class="sp-search-wrap" data-reveal="fade-up">
        <i class="fa-solid fa-magnifying-glass sp-search__icon"></i>
        <input type="text" id="spSearch" class="sp-search" placeholder="Search products…" autocomplete="off">
        <button class="sp-search__clear" id="spSearchClear" aria-label="Clear search">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- Filter tabs -->
      <div class="sp-filters" id="spFilters" data-reveal="fade-up" data-delay="80">
        <button class="sp-filter-btn active" data-filter="all">
          <i class="fa-solid fa-border-all"></i> All
        </button>
        <button class="sp-filter-btn" data-filter="crafts">
          <i class="fa-solid fa-hands-holding-child"></i> Crafts
        </button>
        <button class="sp-filter-btn" data-filter="textiles">
          <i class="fa-solid fa-shirt"></i> Textiles
        </button>
        <button class="sp-filter-btn" data-filter="food">
          <i class="fa-solid fa-mug-saucer"></i> Food &amp; Drink
        </button>
        <button class="sp-filter-btn" data-filter="art">
          <i class="fa-solid fa-palette"></i> Art
        </button>
        <button class="sp-filter-btn" data-filter="wellness">
          <i class="fa-solid fa-spa"></i> Wellness
        </button>
      </div>

      <!-- Sort + View toggle -->
      <div class="sp-sort-row" data-reveal="fade-up" data-delay="160">
        <select class="sp-sort" id="spSort">
          <option value="default">Sort: Featured</option>
          <option value="price-asc">Price: Low → High</option>
          <option value="price-desc">Price: High → Low</option>
          <option value="name">A → Z</option>
        </select>
        <div class="sp-view-toggle">
          <button class="sp-view-btn active" id="viewGrid" aria-label="Grid view">
            <i class="fa-solid fa-grip"></i>
          </button>
          <button class="sp-view-btn" id="viewList" aria-label="List view">
            <i class="fa-solid fa-list"></i>
          </button>
        </div>
      </div>

    </div>
    <?php
      // Get the actual product count for the results display
      $sqlCount = "SELECT COUNT(*) as total FROM shop_items WHERE status = 'active'";
      $resCount = $conn->query($sqlCount);
      $displayCount = $resCount ? $resCount->fetch_assoc()['total'] : 0;
      ?>
      <div class="sp-results-count" id="spResultsCount">Showing <strong><?php echo $displayCount; ?></strong> products</div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════
  PRODUCT GRID
══════════════════════════════════════════════════════ -->
<section class="sp-grid-section" id="spGridSection">
  <div class="sp-container">
    <div class="sp-grid" id="spGrid">
      <?php
      $sqlGrid = "SELECT * FROM shop_items WHERE status = 'active' ORDER BY created_at DESC";
      $resGrid = $conn->query($sqlGrid);
      $delayG = 0;
      if ($resGrid && $resGrid->num_rows > 0) {
          while ($rowG = $resGrid->fetch_assoc()) {
              $gImgVal = !empty($rowG['image']) ? $rowG['image'] : 'shop/2.jpg';
              if (strpos($gImgVal, 'http') === 0) {
                  $gImg = $gImgVal;
              } else {
                  $gImgVal = preg_replace('/^(\.\/)?img\//', '', ltrim($gImgVal, '/'));
                  if (strpos($gImgVal, '/') === false) {
                      $gImgVal = 'shop/' . $gImgVal;
                  }
                  $gImg = './img/' . $gImgVal;
              }
              
              $gCat   = !empty($rowG['category']) ? htmlspecialchars($rowG['category']) : 'Crafts';
              $gTitle = htmlspecialchars($rowG['title']);
              $gPrice = htmlspecialchars($rowG['price']);
              $gId    = $rowG['id'];
              $gBadge = !empty($rowG['badge']) ? htmlspecialchars($rowG['badge']) : '';
              $gDesc  = htmlspecialchars($rowG['description']);
              
              $gFilter = strtolower(str_replace([' ', '&', 'amp;'], ['-', '', ''], $gCat));
              
              echo '
              <div class="sp-card" data-category="' . $gFilter . '" data-price="' . $gPrice . '" data-name="' . $gTitle . '" data-reveal="fade-up" data-delay="' . $delayG . '">
                <div class="sp-card__media">
                  <img src="' . $gImg . '" alt="' . $gTitle . '" class="sp-card__img" loading="lazy">
                  <div class="sp-card__overlay-actions">
                    <button class="sp-card__action sp-quick-view"
                      data-id="' . $gId . '" data-name="' . $gTitle . '" data-price="' . $gPrice . '" data-cat="' . $gCat . '"
                      data-img="' . $gImg . '"
                      data-desc="' . $gDesc . '"
                      aria-label="Quick view">
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    <button class="sp-card__action sp-wishlist" data-id="' . $gId . '" aria-label="Wishlist">
                      <i class="fa-regular fa-heart"></i>
                    </button>
                  </div>';
                  
                  if ($gBadge) {
                      $badgeClass = strtolower(str_replace(' ', '-', $gBadge));
                      echo '<span class="sp-card__badge sp-card__badge--' . $badgeClass . '">' . $gBadge . '</span>';
                  }

              echo '
                </div>
                <div class="sp-card__body">
                  <span class="sp-card__cat">' . $gCat . '</span>
                  <h3 class="sp-card__name">' . $gTitle . '</h3>
                  <div class="sp-card__stars">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span>(24)</span>
                  </div>
                  <div class="sp-card__footer">
                    <span class="sp-card__price">$' . $gPrice . '</span>
                    <button class="sp-card__cart-btn sp-add-to-cart"
                      data-id="' . $gId . '" data-name="' . $gTitle . '" data-price="' . $gPrice . '"
                      data-img="' . $gImg . '">
                      <i class="fa-solid fa-bag-shopping"></i>
                    </button>
                  </div>
                </div>
              </div>';
              
              $delayG = ($delayG + 60) % 240;
          }
      } else {
          echo "<p style=\"grid-column:1/-1;text-align:center;\">No products available currently.</p>";
      }
      ?>
    </div><!-- /sp-grid -->

    <!-- Empty state -->
    <div class="sp-empty" id="spEmpty" style="display:none;">
      <i class="fa-solid fa-box-open"></i>
      <p>No products found. Try a different search or filter.</p>
    </div>

    <!-- Load more -->
    <div class="sp-load-more" id="spLoadMore" data-reveal="fade-up">
      <button class="sp-btn sp-btn--outline" id="spLoadMoreBtn">
        <i class="fa-solid fa-arrow-down"></i> Load More Products
      </button>
    </div>

  </div>
</section>

<!-- ══════════════════════════════════════════════════════
  ARTISAN PLEDGE BAND
══════════════════════════════════════════════════════ -->
<div class="sp-pledge" data-reveal="fade-up">
  <div class="sp-container">
    <div class="sp-pledge__inner">
      <div class="sp-pledge__item">
        <i class="fa-solid fa-hands-holding-heart"></i>
        <div>
          <strong>Fair Trade Always</strong>
          <p>Every artisan earns a fair, transparent wage for their work.</p>
        </div>
      </div>
      <div class="sp-pledge__item">
        <i class="fa-solid fa-leaf"></i>
        <div>
          <strong>Sustainably Sourced</strong>
          <p>All materials are natural, local, and harvested responsibly.</p>
        </div>
      </div>
      <div class="sp-pledge__item">
        <i class="fa-solid fa-truck-fast"></i>
        <div>
          <strong>Ship Worldwide</strong>
          <p>We ship to over 50 countries via DHL and Rwanda Post.</p>
        </div>
      </div>
      <div class="sp-pledge__item">
        <i class="fa-solid fa-rotate-left"></i>
        <div>
          <strong>30-Day Returns</strong>
          <p>Not happy? Return any item within 30 days, no questions asked.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
  QUICK VIEW MODAL
══════════════════════════════════════════════════════ -->
<div class="sp-modal-overlay" id="spModalOverlay" aria-hidden="true">
  <div class="sp-modal" id="spModal" role="dialog" aria-modal="true" aria-label="Product quick view">
    <button class="sp-modal__close" id="spModalClose" aria-label="Close">
      <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="sp-modal__grid">
      <div class="sp-modal__img-wrap">
        <img src="" alt="" class="sp-modal__img" id="spModalImg">
      </div>
      <div class="sp-modal__content">
        <span class="sp-modal__cat" id="spModalCat"></span>
        <h3 class="sp-modal__title" id="spModalTitle"></h3>
        <p class="sp-modal__desc" id="spModalDesc"></p>
        <div class="sp-modal__price-row">
          <span class="sp-modal__price" id="spModalPrice"></span>
        </div>
        <div class="sp-modal__qty-row">
          <button class="sp-qty-btn" id="spQtyMinus" aria-label="Decrease quantity">−</button>
          <span class="sp-qty-val" id="spQtyVal">1</span>
          <button class="sp-qty-btn" id="spQtyPlus" aria-label="Increase quantity">+</button>
        </div>
        <button class="sp-btn sp-btn--primary sp-modal__add" id="spModalAdd" style="width:100%;">
          <i class="fa-solid fa-bag-shopping"></i> Add to Cart
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
  CART DRAWER
══════════════════════════════════════════════════════ -->
<div class="sp-cart-overlay" id="spCartOverlay" aria-hidden="true"></div>
<aside class="sp-cart-drawer" id="spCartDrawer" aria-label="Shopping cart">
  <div class="sp-cart-drawer__header">
    <h4><i class="fa-solid fa-bag-shopping"></i> Your Cart</h4>
    <button class="sp-cart-drawer__close" id="spCartClose" aria-label="Close cart">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>
  <div class="sp-cart-drawer__items" id="spCartItems">
    <div class="sp-cart-empty" id="spCartEmpty">
      <i class="fa-solid fa-basket-shopping"></i>
      <p>Your cart is empty</p>
      <span>Add some beautiful local products!</span>
    </div>
  </div>
  <div class="sp-cart-drawer__footer" id="spCartFooter" style="display:none;">
    <div class="sp-cart-subtotal">
      <span>Subtotal</span>
      <strong id="spCartTotal">$0.00</strong>
    </div>
    <a href="checkout.php" class="sp-btn sp-btn--primary" style="width:100%;justify-content:center;">
      <i class="fa-solid fa-lock"></i> Checkout Securely
    </a>
    <button class="sp-btn sp-btn--ghost" style="width:100%;justify-content:center;margin-top:.75rem;" id="spClearCart">
      <i class="fa-solid fa-trash"></i> Clear Cart
    </button>
  </div>
</aside>

<!-- ══════════════════════════════════════════════════════
  FLOATING CART BUTTON
══════════════════════════════════════════════════════ -->
<button class="sp-cart-fab" id="spCartFab" aria-label="Open cart">
  <i class="fa-solid fa-bag-shopping"></i>
  <span class="sp-cart-fab__badge" id="spCartBadge" style="display:none;">0</span>
</button>

<!-- Toast notification -->
<div class="sp-toast" id="spToast" aria-live="polite"></div>

<?php include 'includes/footer.php'; ?>
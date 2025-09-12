<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-logo">
            <i class="fas fa-mountain"></i>
            <span class="nav-text">Virunga Admin</span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="activities/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/activities/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-hiking"></i>
                <span class="nav-text">Activities</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="blogs/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/blogs/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i>
                <span class="nav-text">Blogs</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="cars/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/cars/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-car"></i>
                <span class="nav-text">Cars</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="events/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/events/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span class="nav-text">Events</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="reviews/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/reviews/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-star"></i>
                <span class="nav-text">Reviews</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="rooms/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/rooms/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-bed"></i>
                <span class="nav-text">Rooms</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="services/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/services/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-concierge-bell"></i>
                <span class="nav-text">Services</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="shop/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/shop/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-text">Shop</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="about-us/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/about-us/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i>
                <span class="nav-text">About Us</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="hero-images/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/hero-images/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span class="nav-text">Hero Images</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="homepage-about/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/homepage-about/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span class="nav-text">Homepage About</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="house-rules/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/house-rules/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-list-alt"></i>
                <span class="nav-text">House Rules</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="contact-messages/index.php" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/contact-messages/') !== false) ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span class="nav-text">Contact Messages</span>
            </a>
        </div>
    </nav>
</aside>
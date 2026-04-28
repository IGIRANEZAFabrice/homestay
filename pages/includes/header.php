<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Virunga Homestay'; ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience the best stay at Virunga Homestay. Your perfect sanctuary in the heart of nature near Virunga volcanoes.'; ?>">
    <meta name="keywords" content="<?php echo isset($pageKeywords) ? $pageKeywords : 'homestay, Virunga, Rwanda, accommodation, travel, nature, volcanoes'; ?>">
    <link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle : 'Virunga Homestay'; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience the best stay at Virunga Homestay. Your perfect sanctuary in the heart of nature near Virunga volcanoes.'; ?>">
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/img/logo/logo.png'; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle : 'Virunga Homestay'; ?>">
    <meta property="twitter:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience the best stay at Virunga Homestay. Your perfect sanctuary in the heart of nature near Virunga volcanoes.'; ?>">
    <meta property="twitter:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/img/logo/logo.png'; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo/logo-small.png">
    <link rel="shortcut icon" href="img/logo/logo-small.png">

    <!-- Webmaster Tools Verification -->
    <meta name="google-site-verification" content="lJq7E1iB-kVBSsouRewk9b9SRn0d2aBHCe3D7C96HPo" />
    <meta name="msvalidate.01" content="YOUR_BING_VERIFICATION_CODE" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="./css/theme.css" />
    <link rel="stylesheet" href="./css/main.css" />
    <?php if (!empty($pageCss) && is_array($pageCss)) foreach ($pageCss as $css): ?>
      <link rel="stylesheet" href="./css/<?php echo $css; ?>" />
    <?php endforeach; ?>
  </head>
  <body>
    <!-- --- NAV -------------------------------------------------- -->
    <nav id="mainNav">
      <a href="<?php echo $baseLink('home'); ?>" class="logo-wrap">
        <img
          src="./img/logo/logo.png"
          class="logo-full"
          alt="Virunga Homestay"
        />
        <img
          src="./img/logo/logo-small.png"
          class="logo-sm"
          alt="Virunga Homestay"
        />
      </a>

      <ul class="nav-links">
        <li><a href="<?php echo $baseLink('home'); ?>">Home</a></li>
        <li><a href="<?php echo $baseLink('rooms'); ?>">Rooms</a></li>
        <li><a href="<?php echo $baseLink('houserules'); ?>">House Rules</a></li>

        <!-- -- Services dropdown -- -->
        <li class="has-dropdown">
          <a href="" tabindex="0">
            Services
            <span class="chevron" aria-hidden="true"></span>
          </a>
          <div class="dropdown" role="menu">
            <a href="<?php echo $baseLink('shop'); ?>" class="dropdown-item" role="menuitem"> Shop </a>
            <a href="<?php echo $baseLink('carrent'); ?>" class="dropdown-item" role="menuitem">
              Car Rent
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?php echo $baseLink('activity'); ?>" class="dropdown-item" role="menuitem">
              Community Activities
            </a>
          </div>
        </li>
        <li><a href="<?php echo $baseLink('about'); ?>">About</a></li>
        <li><a href="<?php echo $baseLink('blog'); ?>">Blogs</a></li>
        
        <li class="cta-link"><a href="<?php echo $baseLink('contact'); ?>">Contact Us</a></li>
      </ul>

      <button
        class="hamburger"
        id="hamburger"
        aria-label="Toggle menu"
        aria-expanded="false"
      >
        <span></span><span></span><span></span>
      </button>
    </nav>

    <!-- -- mobile drawer -- -->
    <div class="mobile-menu" id="mobileMenu" aria-hidden="true">
      <ul>
        <li><a href="/home">Home</a></li>
        <li><a href="<?php echo $baseLink('about'); ?>">About</a></li>
        <li><a href="<?php echo $baseLink('rooms'); ?>">Rooms</a></li>

        <!-- Services accordion -->
        <li style="border-bottom: 1px solid var(--color-border-dark)">
          <button
            class="mob-services-toggle"
            id="mobServicesBtn"
            aria-expanded="false"
          >
            Services
            <span class="mob-chevron" aria-hidden="true"></span>
          </button>
          <div class="mob-services-panel" id="mobServicesPanel">
            <a href="<?php echo $baseLink('shop'); ?>">Shop</a>
            <a href="<?php echo $baseLink('carrent'); ?>">Car Rent</a>
            <a href="<?php echo $baseLink('activity'); ?>">Community Activities</a>
          </div>
        </li>

        <li><a href="<?php echo $baseLink('blog'); ?>">Blogs</a></li>
        <li><a href="<?php echo $baseLink('rules'); ?>">House Rules</a></li>
        <li><a href="<?php echo $baseLink('contact'); ?>">Contact Us</a></li>
      </ul>
    </div>

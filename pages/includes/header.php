<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php 
      $displayTitle = isset($pageTitle) ? $pageTitle : 'Virunga Homestay';
      if (isset($slug) && $slug !== 'home') {
        echo $displayTitle . ' | Virunga Homestay - Musanze Rwanda';
      } else {
        echo 'Virunga Homestay | Best Accommodation in Musanze, Rwanda';
      }
    ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience authentic Rwandan hospitality at Virunga Homestay in Musanze. Perfect for gorilla trekking, volcano hikes, and cultural immersion. Book your stay today!'; ?>">
    <meta name="keywords" content="<?php echo isset($pageKeywords) ? $pageKeywords : 'homestay Musanze, Virunga accommodation, Rwanda gorilla trekking stay, Volcanoes National Park lodging, authentic Rwanda travel'; ?>">
    <?php
      $canonicalSlug = (isset($slug) && $slug !== 'home') ? $slug : '';
      $canonicalUrl = 'https://virungahomestay.com/' . $canonicalSlug;
    ?>
    <link rel="canonical" href="<?php echo $canonicalUrl; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle : 'Virunga Homestay'; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience the best stay at Virunga Homestay. Your perfect sanctuary in the heart of nature near Virunga volcanoes.'; ?>">
    <meta property="og:image" content="<?php echo 'https://virungahomestay.com/img/hero/room.jpg'; ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $canonicalUrl; ?>">
    <meta property="twitter:title" content="<?php echo isset($pageTitle) ? $pageTitle : 'Virunga Homestay'; ?>">
    <meta property="twitter:description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Experience authentic Rwandan hospitality at Virunga Homestay in Musanze. Perfect for gorilla trekking, volcano hikes, and cultural immersion. Book your stay today!'; ?>">
    <meta property="twitter:image" content="<?php echo 'https://virungahomestay.com/img/hero/room.jpg'; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo/logo-small.png">
    <link rel="shortcut icon" href="img/logo/logo-small.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#c8711a">

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

    <!-- JSON-LD Schema Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LodgingBusiness",
      "name": "Virunga Homestay",
      "image": "https://virungahomestay.com/img/logo/logo.png",
      "@id": "https://virungahomestay.com",
      "url": "https://virungahomestay.com",
      "telephone": "+250784513435",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Musanze",
        "addressLocality": "Musanze",
        "addressRegion": "Northern Province",
        "addressCountry": "RW"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -1.5000,
        "longitude": 29.6333
      },
      "url": "https://virungahomestay.com",
      "priceRange": "$$",
      "description": "Premium homestay experience at the foot of the Virunga Volcanoes in Musanze, Rwanda.",
      "amenityFeature": [
        {
          "@type": "LocationFeatureSpecification",
          "name": "Free WiFi",
          "value": true
        },
        {
          "@type": "LocationFeatureSpecification",
          "name": "Breakfast Included",
          "value": true
        },
        {
          "@type": "LocationFeatureSpecification",
          "name": "Private Bathroom",
          "value": true
        },
        {
          "@type": "LocationFeatureSpecification",
          "name": "Hot Shower",
          "value": true
        }
      ]
    }
    </script>
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
        <li><a href="<?php echo $baseLink('rooms'); ?>">Stay</a></li>
        <li><a href="<?php echo $baseLink('activity'); ?>">Experiences</a></li>
        <li><a href="<?php echo $baseLink('impact'); ?>">Impact</a></li>
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
          </div>
        </li>
        <li><a href="<?php echo $baseLink('about'); ?>">Story</a></li>
        <li class="cta-link"><a href="<?php echo $baseLink('contact'); ?>">Book Stay</a></li>
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
        <li><a href="<?php echo $baseLink('home'); ?>">Home</a></li>
        <li><a href="<?php echo $baseLink('impact'); ?>">Impact</a></li>
        <li><a href="<?php echo $baseLink('about'); ?>">Story</a></li>
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

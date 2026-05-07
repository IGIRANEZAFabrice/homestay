<?php
// Shared page hero component. Set $pageHeroKey before including.
$pageHeroConfig = [
  'contact' => [
    'tag' => 'Virunga Homestay',
    'title' => 'Get in <em>touch</em> with us',
    'bg' => './img/hero/1.jpg',
    'crumb' => 'Contact Us'
  ],
  'about' => [
    'tag' => 'Our Story',
    'title' => 'Discover <em>Virunga Homestay</em>',
    'bg' => './img/hero/2.jpg',
    'crumb' => 'About'
  ],
  'rooms' => [
    'tag' => 'Stay With Us',
    'title' => 'Cozy <em>rooms</em> with a view',
    'bg' => './img/hero/room.jpg',
    'crumb' => 'Rooms'
  ],
  'carrent' => [
    'tag' => 'Explore Rwanda',
    'title' => 'Private <em>car rentals</em> & transfers',
    'bg' => './img/services/1.JPG',
    'crumb' => 'Car Rent'
  ],
  'shop' => [
    'tag' => 'Crafted Locally',
    'title' => 'Shop <em>Virunga</em> goods',
    'bg' => './img/home/food.jpg',
    'crumb' => 'Shop'
  ],
  'activity' => [
    'tag' => 'Local Experiences',
    'title' => 'Community <em>activities</em> and culture',
    'bg' => './img/services/3.jpg',
    'crumb' => 'Community Activities'
  ],
  'blog' => [
    'tag' => 'Stories and Guides',
    'title' => 'Virunga <em>blog</em> journal',
    'bg' => './img/hero/1.jpg',
    'crumb' => 'Blog'
  ],
  'house-rules' => [
    'tag' => 'Virunga Homestay',
    'title' => 'House <em>Rules</em>',
    'bg' => './img/hero/3.jpg',
    'crumb' => 'House Rules'
  ],
  'booking' => [
    'tag' => 'Reservations',
    'title' => 'Booking &amp; <em>Payments</em>',
    'bg' => './img/hero/2.jpg',
    'crumb' => 'Booking Information'
  ],
  'privacy' => [
    'tag' => 'Virunga Homestay',
    'title' => 'Privacy & <em>Policies</em>',
    'bg' => './img/hero/1.jpg',
    'crumb' => 'Privacy'
  ],
];

$key = isset($pageHeroKey) && isset($pageHeroConfig[strtolower($pageHeroKey)]) ? strtolower($pageHeroKey) : 'contact';
$config = $pageHeroConfig[$key];
?>
<header class="page-hero">
  <div class="page-hero-bg" style="background-image: url('<?php echo $config['bg']; ?>');"></div>
  <div class="page-hero-content">
    <p class="page-tag"><?php echo $config['tag']; ?></p>
    <h1><?php echo $config['title']; ?></h1>
    <span class="hero-rule"></span>
  </div>
</header>

<div class="breadcrumb">
  <a href="/">Home</a>
  <span class="sep">&#8250;</span>
  <span style="color: white;"><?php echo $config['crumb']; ?></span>
</div>

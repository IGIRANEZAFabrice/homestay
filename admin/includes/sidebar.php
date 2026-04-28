<?php
  $currentPage = isset($currentPage) ? $currentPage : basename($_SERVER['PHP_SELF'], '.php');
  $nav = [
    'overview' => [
      'label' => 'Overview',
      'items' => [
        ['id' => 'dashboard',   'icon' => 'fa-chart-line',          'label' => 'Dashboard'],
      ]
    ],
    'content' => [
      'label' => 'Content',
      'items' => [
        ['id' => 'rooms',        'icon' => 'fa-bed',                 'label' => 'Rooms & Suites'],
        ['id' => 'hero-images',  'icon' => 'fa-images',             'label' => 'Hero Images'],
        ['id' => 'home-about',   'icon' => 'fa-house-chimney',      'label' => 'Home About'],
        ['id' => 'home-experience', 'icon' => 'fa-mountain-sun',   'label' => 'Home Experience'],
        ['id' => 'why-choose',   'icon' => 'fa-star',               'label' => 'Why Choose Us'],
        ['id' => 'activities',   'icon' => 'fa-person-hiking',       'label' => 'Activities'],
        ['id' => 'blog',        'icon' => 'fa-newspaper',           'label' => 'Travel Blog'],
        ['id' => 'faqs',        'icon' => 'fa-question-circle',     'label' => 'FAQs'],
      ]
    ],
    'management' => [
      'label' => 'Management',
      'items' => [
        ['id' => 'cars',  'icon' => 'fa-car',                 'label' => 'Cars'],
        ['id' => 'shop',  'icon' => 'fa-store',               'label' => 'Shop Items'],
      ]
    ],
    'settings' => [
      'label' => 'Settings',
      'items' => [
        ['id' => 'site-settings','icon' => 'fa-sliders',            'label' => 'Site Settings'],
        ['id' => 'users',        'icon' => 'fa-user-shield',        'label' => 'Admin Users'],
      ]
    ],
  ];
?>

<aside class="dash-sidebar" id="dashSidebar">
  <div class="sidebar-inner">

    <!-- Mobile brand -->
    <div class="sidebar-brand-mobile">
      <div class="brand-icon">
        <img src="../img/logo/logo-small.png" alt="Virunga Logo">
      </div>
      <span>Virunga CMS</span>
      <button class="sidebar-close" id="sidebarClose"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Site health pill -->
    <div class="sidebar-health">
      <div class="health-dot"></div>
      <span>virungahomestay.com</span>
      <span class="health-badge">Live</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
      <?php foreach ($nav as $groupKey => $group): ?>
      <div class="nav-group">
        <p class="nav-group__label"><?= $group['label'] ?></p>
        <?php foreach ($group['items'] as $item): ?>
        <a
          href="<?= $item['id'] ?>.php"
          class="nav-item <?= ($currentPage === $item['id']) ? 'nav-item--active' : '' ?>"
          data-page="<?= $item['id'] ?>"
        >
          <span class="nav-item__icon">
            <i class="fa-solid <?= $item['icon'] ?>"></i>
          </span>
          <span class="nav-item__label"><?= $item['label'] ?></span>
          <?php if ($item['id'] === 'inquiries'): ?>
            <span class="nav-item__badge">4</span>
          <?php endif; ?>
          <?php if ($item['id'] === 'dashboard'): ?>
            <span class="nav-item__badge nav-item__badge--green">New</span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </nav>

    <!-- Sidebar footer -->
    <div class="sidebar-footer">
      <div class="sf-quick">
        <a href="../index.php" target="_blank" class="sf-btn" title="View live site">
          <i class="fa-solid fa-globe"></i>
          <span>Live Site</span>
        </a>
        <a href="site-settings.php" class="sf-btn" title="Settings">
          <i class="fa-solid fa-gear"></i>
        </a>
      </div>
      <div class="sf-version">CMS v1.0 &nbsp;·&nbsp; Virunga <?= date('Y') ?></div>
    </div>

  </div>
</aside>

<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

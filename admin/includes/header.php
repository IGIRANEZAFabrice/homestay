<?php
  require_once __DIR__ . '/../../config/db.php';
  $adminName = "Admin";
  $adminRole = "Admin";
  $adminEmail = "admin@virungahomestay.com";
  $adminInitial = "A";

  if (isset($_SESSION['admin_id'])) {
      $stmt = $conn->prepare("SELECT full_name, role, email FROM admin_users WHERE id = ?");
      $stmt->bind_param("i", $_SESSION['admin_id']);
      $stmt->execute();
      $res = $stmt->get_result();
      if ($row = $res->fetch_assoc()) {
          $adminName = !empty($row['full_name']) ? $row['full_name'] : 'Admin';
          $adminRole = ucwords(str_replace('_', ' ', $row['role']));
          $adminEmail = !empty($row['email']) ? $row['email'] : '';
          $adminInitial = strtoupper(substr($adminName, 0, 1));
      }
  }

  $currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<header class="dash-header" id="dashHeader">
  <div class="dash-header__left">
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <span></span><span></span><span></span>
    </button>
    <div class="dash-header__brand">
      <div class="brand-icon"><i class="fa-solid fa-mountain-sun"></i></div>
      <div class="brand-text">
        <span class="brand-name">Virunga</span>
        <span class="brand-sub">Dashboard</span>
      </div>
    </div>
  </div>

  <div class="dash-header__center">
    <div class="dash-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" placeholder="Search pages, rooms, bookings…" />
      <kbd>⌘K</kbd>
    </div>
  </div>

  <div class="dash-header__right">
    <button class="hdr-btn" title="View live site">
      <i class="fa-solid fa-arrow-up-right-from-square"></i>
      <span>Live Site</span>
    </button>

    <div class="hdr-icon-btn" title="Notifications">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-dot"></span>
    </div>

    <div class="hdr-icon-btn" title="Dark mode toggle" id="themeToggle">
      <i class="fa-solid fa-moon"></i>
    </div>

    <div class="hdr-avatar" id="avatarBtn">
      <div class="avatar-ring">
        <div class="avatar-img"><?= $adminInitial ?></div>
      </div>
      <div class="avatar-info">
        <span class="avatar-name"><?= htmlspecialchars($adminName) ?></span>
        <span class="avatar-role"><?= htmlspecialchars($adminRole) ?></span>
      </div>
      <i class="fa-solid fa-chevron-down fa-xs"></i>

      <div class="avatar-dropdown" id="avatarDropdown">
        <div class="dropdown-header">
          <div class="dh-avatar"><?= $adminInitial ?></div>
          <div>
            <p><?= htmlspecialchars($adminName) ?></p>
            <span><?= htmlspecialchars($adminEmail) ?></span>
          </div>
        </div>
        <hr class="dropdown-divider"/>
        <a href="site-settings.php" class="dropdown-item"><i class="fa-solid fa-user"></i> Profile Settings</a>
        <a href="site-settings.php" class="dropdown-item"><i class="fa-solid fa-gear"></i> Preferences</a>
        <a href="site-settings.php" class="dropdown-item"><i class="fa-solid fa-shield-halved"></i> Security</a>
        <hr class="dropdown-divider"/>
        <a href="logout.php" class="dropdown-item dropdown-item--danger"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
      </div>
    </div>
  </div>
</header>
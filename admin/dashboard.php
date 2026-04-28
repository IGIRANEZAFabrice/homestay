<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Calculate Analytics
$totalVisits = 0;
$stmtAll = $conn->query("SELECT SUM(visit_count) as total FROM page_visits");
if ($stmtAll && $row = $stmtAll->fetch_assoc()) {
    $totalVisits = (int)$row['total'];
}

$monthlyVisits = 0;
$stmtMonth = $conn->query("SELECT SUM(visit_count) as monthly FROM page_visits WHERE MONTH(visit_date) = MONTH(CURRENT_DATE()) AND YEAR(visit_date) = YEAR(CURRENT_DATE())");
if ($stmtMonth && $row = $stmtMonth->fetch_assoc()) {
    $monthlyVisits = (int)$row['monthly'];
}

$lastMonthVisits = 0;
$stmtLastMonth = $conn->query("SELECT SUM(visit_count) as last_month FROM page_visits WHERE MONTH(visit_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(visit_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)");
if ($stmtLastMonth && $row = $stmtLastMonth->fetch_assoc()) {
    $lastMonthVisits = (int)$row['last_month'];
}

$trendPercent = 0;
if ($lastMonthVisits > 0) {
    $trendPercent = round((($monthlyVisits - $lastMonthVisits) / $lastMonthVisits) * 100, 1);
} else if ($monthlyVisits > 0) {
    $trendPercent = 100; // 100% up if we had 0 last month
}

// Format the percentage for display
$trendSign = $trendPercent >= 0 ? '+' : '';
$trendClass = $trendPercent >= 0 ? 'kpi-delta--up' : 'kpi-delta--down';
$trendIcon = $trendPercent >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';

$pageTitle = 'Dashboard — Virunga Homestay CMS';
$currentPage = 'dashboard';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="css/header.css" />
    <link rel="stylesheet" href="css/sidebar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
  </head>
  <body>
    <?php include 'includes/header.php'; ?>

    <div class="dash-layout" id="dashLayout">
      <?php include 'includes/sidebar.php'; ?>

      <main class="dash-main">
        <!-- ── Welcome banner ── -->
        <div class="dash-banner" id="dashBanner">
          <i class="fa-solid fa-mountain-sun dash-banner__icon"></i>
          <div class="dash-banner__text">
            <strong
              >Welcome back, <?= htmlspecialchars($adminName) ?> 👋 — Your site is performing well this
              month.</strong
            >
            <p>
              Visitor count is up 18% from last month. 4 new inquiries need your
              attention.
            </p>
          </div>
          <button
            class="dash-banner__close"
            onclick="document.getElementById('dashBanner').remove()"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <!-- ── Page header ── -->
        <div class="page-header">
          <div>
            <h1 class="page-header__title">Site Overview</h1>
            <p class="page-header__sub">
              Virunga Homestay · Musanze, Rwanda &nbsp;·&nbsp;
              <span
                class="live-ticker"
                style="
                  display: inline-flex;
                  padding: 3px 10px;
                  font-size: 11.5px;
                "
              >
                <span class="live-dot"></span>
                <span id="liveCount">3</span> visitors right now
              </span>
            </p>
          </div>
          <div class="page-header__actions">
            <div class="date-range">
              <i class="fa-regular fa-calendar"></i>
              <span id="dateRangeLabel">Last 30 days</span>
              <i class="fa-solid fa-chevron-down fa-xs"></i>
            </div>
            <button class="btn btn-ghost">
              <i class="fa-solid fa-arrow-down-to-bracket"></i> Export
            </button>
            <button class="btn btn-amber">
              <i class="fa-solid fa-plus"></i> Add Content
            </button>
          </div>
        </div>

        <!-- ── KPI Cards ── -->
        <div class="kpi-row gap-section">
          <div
            class="kpi-card"
            style="--kpi-color: #e8a844; --kpi-bg: rgba(232, 168, 68, 0.12)"
          >
            <div class="kpi-card__top">
              <div>
                <div class="kpi-card__label">Total Visitors</div>
              </div>
              <div class="kpi-card__icon">
                <i class="fa-solid fa-users"></i>
              </div>
            </div>
            <div class="kpi-card__value" data-target="0" data-suffix="">
              0
            </div>
            <div class="kpi-card__bottom">
              <span class="kpi-delta" style="color:var(--color-text-on-light-2)"
                >0%</span
              >
              <span class="kpi-card__period">vs last month</span>
            </div>
            <div class="kpi-sparkline"><canvas id="spark0"></canvas></div>
          </div>

          <div
            class="kpi-card"
            style="--kpi-color: #2eb8a0; --kpi-bg: rgba(46, 184, 160, 0.12)"
          >
            <div class="kpi-card__top">
              <div>
                <div class="kpi-card__label">Page Views</div>
              </div>
              <div class="kpi-card__icon"><i class="fa-solid fa-eye"></i></div>
            </div>
            <div class="kpi-card__value" data-target="<?php echo $totalVisits; ?>" data-suffix="">
              0
            </div>
            <div class="kpi-card__bottom">
              <span class="kpi-delta <?php echo $trendClass; ?>"
                ><i class="fa-solid <?php echo $trendIcon; ?> fa-xs"></i> <?php echo $trendSign . $trendPercent; ?>%</span
              >
              <span class="kpi-card__period">vs last month</span>
            </div>
            <div class="kpi-sparkline"><canvas id="spark1"></canvas></div>
          </div>

          <div
            class="kpi-card"
            style="--kpi-color: #8b5cf6; --kpi-bg: rgba(139, 92, 246, 0.12)"
          >
            <div class="kpi-card__top">
              <div>
                <div class="kpi-card__label">Inquiries Received</div>
              </div>
              <div class="kpi-card__icon">
                <i class="fa-solid fa-envelope"></i>
              </div>
            </div>
            <div class="kpi-card__value" data-target="0" data-suffix="">0</div>
            <div class="kpi-card__bottom">
              <span class="kpi-delta" style="color:var(--color-text-on-light-2)"
                >0</span
              >
              <span class="kpi-card__period">new this week</span>
            </div>
            <div class="kpi-sparkline"><canvas id="spark2"></canvas></div>
          </div>

          <div
            class="kpi-card"
            style="--kpi-color: #f43f5e; --kpi-bg: rgba(244, 63, 94, 0.12)"
          >
            <div class="kpi-card__top">
              <div>
                <div class="kpi-card__label">Avg. Session</div>
              </div>
              <div class="kpi-card__icon">
                <i class="fa-solid fa-clock"></i>
              </div>
            </div>
            <div
              class="kpi-card__value"
              style="font-size: 28px"
              data-target-text="0m 0s"
            >
              0
            </div>
            <div class="kpi-card__bottom">
              <span class="kpi-delta" style="color:var(--color-text-on-light-2)"
                >0s</span
              >
              <span class="kpi-card__period">vs last month</span>
            </div>
            <div class="kpi-sparkline"><canvas id="spark3"></canvas></div>
          </div>
        </div>

        <!-- ── Main Data Row ── -->
        <div class="dash-grid gap-section" style="grid-template-columns: 1fr;">
          <!-- Pages by Views -->
          <div class="panel">
            <div class="panel__header">
              <div>
                <div class="panel__title">Top Pages by Views</div>
                <div class="panel__sub">Live traffic from tracked paths</div>
              </div>
            </div>
            <div class="panel__body--tight">
              <table class="page-table">
                <thead>
                  <tr>
                    <th>Page</th>
                    <th>Views</th>
                    <th style="min-width: 120px">Share</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $pageStmt = $conn->query("SELECT page_url, SUM(visit_count) as views FROM page_visits GROUP BY page_url ORDER BY views DESC LIMIT 6");
                  $maxViews = $totalVisits > 0 ? $totalVisits : 1;
                  if ($pageStmt && $pageStmt->num_rows > 0) {
                      $colors = ['var(--amber)', 'var(--teal)', 'var(--purple)', 'var(--rose)', 'var(--teal-lt)', '#f59e0b'];
                      $colorIdx = 0;
                      while ($pRow = $pageStmt->fetch_assoc()) {
                          $pUrl = htmlspecialchars($pRow['page_url']);
                          $pViews = (int)$pRow['views'];
                          $pct = round(($pViews / $maxViews) * 100);
                          
                          $icon = 'fa-file';
                          if (strpos($pUrl, 'index') !== false || $pUrl === '') $icon = 'fa-house';
                          elseif (strpos($pUrl, 'room') !== false) $icon = 'fa-bed';
                          elseif (strpos($pUrl, 'act') !== false) $icon = 'fa-person-hiking';
                          elseif (strpos($pUrl, 'blog') !== false) $icon = 'fa-newspaper';
                          elseif (strpos($pUrl, 'car') !== false) $icon = 'fa-car';
                          elseif (strpos($pUrl, 'about') !== false) $icon = 'fa-circle-info';
                          
                          $name = ucfirst(str_replace(['.php', '/'], ['', ''], $pUrl));
                          if (empty($name) || strtolower($name) === 'index') $name = 'Home';
                          
                          $c = $colors[$colorIdx % count($colors)];
                          $colorIdx++;
                          
                          echo '<tr>
                              <td>
                                <div class="page-name">
                                  <div class="page-icon"><i class="fa-solid '.$icon.'"></i></div>
                                  <div><strong>'.$name.'</strong><br /><span>/'.$pUrl.'</span></div>
                                </div>
                              </td>
                              <td><strong style="color: var(--text-1)">'.number_format($pViews).'</strong></td>
                              <td>
                                <div class="bar-wrap">
                                  <div class="bar-bg"><div class="bar-fill" style="width: '.$pct.'%; background: '.$c.'"></div></div>
                                  <span class="bar-pct">'.$pct.'%</span>
                                </div>
                              </td>
                              <td><span class="status-pill status-pill--live"><i class="fa-solid fa-circle" style="font-size: 6px"></i> Live</span></td>
                            </tr>';
                      }
                  } else {
                      echo '<tr><td colspan="4" style="text-align:center; padding:25px; color:var(--text-3);">No analytics data recorded yet.</td></tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- ── Bottom row: Activity + Top Rooms + Quick Actions ── -->
        <div class="dash-grid dash-grid--3b gap-section">
          <!-- Recent Activity -->
          <div class="panel">
            <div class="panel__header">
              <div>
                <div class="panel__title">Recent Activity</div>
                <div class="panel__sub">Latest site events</div>
              </div>
              <span class="status-pill status-pill--live" style="flex-shrink: 0"
                ><span class="live-dot" style="width: 6px; height: 6px"></span>
                Live</span
              >
            </div>
            <div class="panel__body--tight">
              <div class="activity-feed">
                <div class="activity-item">
                  <div
                    class="activity-avatar"
                    style="
                      background: rgba(232, 168, 68, 0.15);
                      color: var(--amber);
                    "
                  >
                    J
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      <strong>John M.</strong> submitted a room inquiry for
                      <strong>Junior Suite</strong>
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> 4 minutes ago ·
                      WhatsApp
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div
                    class="activity-icon-wrap"
                    style="
                      background: rgba(46, 184, 160, 0.12);
                      color: var(--teal);
                    "
                  >
                    <i class="fa-solid fa-image"></i>
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      Gallery updated — <strong>6 new photos</strong> added to
                      Rooms collection
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> 27 minutes ago ·
                      You
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div
                    class="activity-avatar"
                    style="background: rgba(139, 92, 246, 0.15); color: #a78bfa"
                  >
                    S
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      <strong>Sarah K.</strong> left a
                      <strong>5-star</strong> review on TripAdvisor
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> 1 hour ago ·
                      TripAdvisor
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div
                    class="activity-icon-wrap"
                    style="
                      background: rgba(232, 168, 68, 0.1);
                      color: var(--amber);
                    "
                  >
                    <i class="fa-solid fa-pen"></i>
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      Page <strong>"Home"</strong> content was updated (hero
                      text + slides)
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> 2 hours ago ·
                      You
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div
                    class="activity-avatar"
                    style="background: rgba(244, 63, 94, 0.12); color: #f87171"
                  >
                    A
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      <strong>Anna W.</strong> asked about gorilla trekking
                      availability for <strong>May 2026</strong>
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> 3 hours ago ·
                      WhatsApp
                    </div>
                  </div>
                </div>
                <div class="activity-item">
                  <div
                    class="activity-icon-wrap"
                    style="
                      background: rgba(52, 201, 147, 0.1);
                      color: var(--success);
                    "
                  >
                    <i class="fa-solid fa-car"></i>
                  </div>
                  <div class="activity-body">
                    <div class="activity-text">
                      Car rental page received
                      <strong>84 new views</strong> today
                    </div>
                    <div class="activity-time">
                      <i class="fa-regular fa-clock fa-xs"></i> Today ·
                      Analytics
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Top Rooms -->
          <div class="panel">
            <div class="panel__header">
              <div>
                <div class="panel__title">Most Viewed Rooms</div>
                <div class="panel__sub">This month's top listings</div>
              </div>
              <a
                href="rooms.php"
                class="btn btn-ghost"
                style="font-size: 12px; padding: 6px 12px"
                >Manage</a
              >
            </div>
            <div class="panel__body--tight">
              <div class="room-row">
                <div class="room-thumb">
                  <i class="fa-solid fa-crown" style="color: var(--amber)"></i>
                </div>
                <div class="room-info">
                  <div class="room-name">Deluxe Double Room</div>
                  <div class="room-type">King Bed · 32 m² · $120/night</div>
                </div>
                <div class="room-views">
                  <strong>3,204</strong><span>views</span>
                </div>
              </div>
              <div class="room-row">
                <div class="room-thumb">
                  <i class="fa-solid fa-star" style="color: var(--teal)"></i>
                </div>
                <div class="room-info">
                  <div class="room-name">Junior Suite</div>
                  <div class="room-type">King Bed · 48 m² · $185/night</div>
                </div>
                <div class="room-views">
                  <strong>2,841</strong><span>views</span>
                </div>
              </div>
              <div class="room-row">
                <div class="room-thumb">
                  <i class="fa-solid fa-bed" style="color: #a78bfa"></i>
                </div>
                <div class="room-info">
                  <div class="room-name">Superior Twin Room</div>
                  <div class="room-type">Twin Beds · 28 m² · $95/night</div>
                </div>
                <div class="room-views">
                  <strong>1,948</strong><span>views</span>
                </div>
              </div>
            </div>

            <!-- WhatsApp conversions -->
            <div
              class="panel__header"
              style="
                border-top: 1px solid var(--border);
                border-bottom: none;
                padding: 16px 22px 12px;
              "
            >
              <div>
                <div class="panel__title" style="font-size: 13.5px">
                  WhatsApp Conversions
                </div>
                <div class="panel__sub">Clicks to chat from each page</div>
              </div>
            </div>
            <div class="panel__body" style="padding-top: 8px">
              <div class="chart-wrap" style="height: 130px">
                <canvas id="waChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Quick Actions + Pending -->
          <div style="display: flex; flex-direction: column; gap: 20px">
            <div class="panel">
              <div class="panel__header">
                <div class="panel__title">Quick Actions</div>
              </div>
              <div class="panel__body">
                <div class="quick-actions">
                  <a href="rooms.php" class="qa-card">
                    <i class="fa-solid fa-bed"></i>
                    <span>Add Room</span>
                  </a>
                  <a href="gallery.php" class="qa-card">
                    <i class="fa-solid fa-images"></i>
                    <span>Upload Photo</span>
                  </a>
                  <a href="blog.php" class="qa-card">
                    <i class="fa-solid fa-pen-nib"></i>
                    <span>New Post</span>
                  </a>
                  <a href="activities.php" class="qa-card">
                    <i class="fa-solid fa-person-hiking"></i>
                    <span>Add Activity</span>
                  </a>
                  <a href="site-settings.php" class="qa-card">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Settings</span>
                  </a>
                  <a href="seo.php" class="qa-card">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>SEO</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Pending inquiries -->
            <div class="panel">
              <div class="panel__header">
                <div>
                  <div class="panel__title">Pending Inquiries</div>
                  <div class="panel__sub">4 awaiting response</div>
                </div>
                <a
                  href="inquiries.php"
                  class="btn btn-ghost"
                  style="font-size: 12px; padding: 6px 12px"
                  >View All</a
                >
              </div>
              <div
                class="panel__body"
                style="display: flex; flex-direction: column; gap: 10px"
              >
                <div
                  style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    background: var(--surface-3);
                    border-radius: var(--radius-sm);
                  "
                >
                  <div
                    style="
                      width: 36px;
                      height: 36px;
                      border-radius: 50%;
                      background: rgba(232, 168, 68, 0.15);
                      display: grid;
                      place-items: center;
                      font-family: var(--font-display);
                      font-weight: 700;
                      color: var(--amber);
                      flex-shrink: 0;
                    "
                  >
                    J
                  </div>
                  <div style="flex: 1; min-width: 0">
                    <div
                      style="
                        font-size: 13px;
                        color: var(--text-1);
                        font-weight: 500;
                      "
                    >
                      John M. — Junior Suite
                    </div>
                    <div style="font-size: 11.5px; color: var(--text-3)">
                      4 min ago · WhatsApp
                    </div>
                  </div>
                  <a
                    href="#"
                    style="
                      display: grid;
                      place-items: center;
                      width: 30px;
                      height: 30px;
                      background: var(--amber);
                      border-radius: 7px;
                      color: #1a1000;
                      font-size: 12px;
                      text-decoration: none;
                      transition: var(--transition);
                    "
                    title="Reply on WhatsApp"
                  >
                    <i class="fa-brands fa-whatsapp"></i>
                  </a>
                </div>
                <div
                  style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    background: var(--surface-3);
                    border-radius: var(--radius-sm);
                  "
                >
                  <div
                    style="
                      width: 36px;
                      height: 36px;
                      border-radius: 50%;
                      background: rgba(244, 63, 94, 0.12);
                      display: grid;
                      place-items: center;
                      font-family: var(--font-display);
                      font-weight: 700;
                      color: #f87171;
                      flex-shrink: 0;
                    "
                  >
                    A
                  </div>
                  <div style="flex: 1; min-width: 0">
                    <div
                      style="
                        font-size: 13px;
                        color: var(--text-1);
                        font-weight: 500;
                      "
                    >
                      Anna W. — Gorilla Trek
                    </div>
                    <div style="font-size: 11.5px; color: var(--text-3)">
                      3 hrs ago · WhatsApp
                    </div>
                  </div>
                  <a
                    href="#"
                    style="
                      display: grid;
                      place-items: center;
                      width: 30px;
                      height: 30px;
                      background: var(--amber);
                      border-radius: 7px;
                      color: #1a1000;
                      font-size: 12px;
                      text-decoration: none;
                    "
                    title="Reply"
                  >
                    <i class="fa-brands fa-whatsapp"></i>
                  </a>
                </div>
                <div
                  style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    background: var(--surface-3);
                    border-radius: var(--radius-sm);
                  "
                >
                  <div
                    style="
                      width: 36px;
                      height: 36px;
                      border-radius: 50%;
                      background: rgba(46, 184, 160, 0.12);
                      display: grid;
                      place-items: center;
                      font-family: var(--font-display);
                      font-weight: 700;
                      color: var(--teal);
                      flex-shrink: 0;
                    "
                  >
                    M
                  </div>
                  <div style="flex: 1; min-width: 0">
                    <div
                      style="
                        font-size: 13px;
                        color: var(--text-1);
                        font-weight: 500;
                      "
                    >
                      Marie C. — Car Rental
                    </div>
                    <div style="font-size: 11.5px; color: var(--text-3)">
                      Yesterday · Email
                    </div>
                  </div>
                  <a
                    href="#"
                    style="
                      display: grid;
                      place-items: center;
                      width: 30px;
                      height: 30px;
                      background: var(--surface-4);
                      border-radius: 7px;
                      color: var(--text-2);
                      font-size: 12px;
                      text-decoration: none;
                    "
                    title="Reply"
                  >
                    <i class="fa-solid fa-envelope"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ── Footer ── -->
        <div
          style="
            text-align: center;
            padding: 28px 0 8px;
            font-size: 12px;
            color: var(--text-3);
            border-top: 1px solid var(--border);
          "
        >
          Virunga Homestay CMS
          &nbsp;·&nbsp;
          <a
            href="../index.php"
            target="_blank"
            style="color: var(--amber); text-decoration: none"
            >View Live Site
            <i class="fa-solid fa-arrow-up-right-from-square fa-xs"></i
          ></a>
        </div>
      </main>
    </div>
    <script src="js/index.js"></script>
  </body>
</html>

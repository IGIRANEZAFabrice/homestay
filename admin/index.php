<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homestay Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/4e9c2b2c0a.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <div class="sidebar-logo">
        <i class="fas fa-home"></i> <span>Virunga Admin</span>
      </div>
      <nav class="sidebar-nav">
        <a href="../" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="./pages/blogs.php"><i class="fas fa-calendar-check"></i> Blogs</a>
        <a href="./pages/carrent.php"><i class="fas fa-users"></i> Cars</a>
        <a href="./pages/rooms.php"><i class="fas fa-bed"></i> Rooms</a>
        <a href="./pages/event.php"><i class="fas fa-calendar-alt"></i> Events</a>
        <a href="./pages/shop.html"><i class="fas fa-store"></i> Shop</a>
        <a href="./pages/hero.php"><i class="fas fa-blog"></i> Hero</a>
        <a href="./pages/homeabout.php"><i class="fas fa-cog"></i> Home About</a>
        <a href="./pages/about.php"><i class="fas fa-cog"></i> About Us</a>
        <a href="./pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
    </aside>
    <main class="main-content">
      <header class="dashboard-header">
        <div class="header-title">
          <h1>Dashboard</h1>
        </div>
        <div class="header-user">
          <span class="user-name">Admin</span>
          <img src="../img/admin-avatar.png" alt="Admin Avatar" class="user-avatar">
          <i class="fas fa-bell"></i>
        </div>
      </header>
      <section class="dashboard-stats">
        <div class="stat-card">
          <i class="fas fa-calendar-check"></i>
          <div>
            <h2>128</h2>
            <p>Bookings</p>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-bed"></i>
          <div>
            <h2>87%</h2>
            <p>Occupancy</p>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-dollar-sign"></i>
          <div>
            <h2>$4,200</h2>
            <p>Revenue</p>
          </div>
        </div>
        <div class="stat-card">
          <i class="fas fa-star"></i>
          <div>
            <h2>4.8</h2>
            <p>Reviews</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>

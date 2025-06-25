<aside class="sidebar" id="adminSidebar">
  <div class="sidebar-logo small-header">
    <div class="sidebar-logo-container">
      <span class="sidebar-logo-icon"><i class="fas fa-home"></i></span>
      <span class="sidebar-logo-text">Virunga Admin</span>
    </div>
    <div class="sidebar-controls">
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar"><i class="fas fa-bars"></i></button>
      <button class="sidebar-hide" id="sidebarHide" aria-label="Hide Sidebar"><i class="fas fa-angle-left"></i></button>
    </div>
  </div>
  
  <div class="sidebar-user">
    <img src="../../img/admin-avatar.png" alt="Admin Avatar" class="sidebar-avatar">
    <div class="sidebar-user-info">
      <h4 class="sidebar-user-name">Admin User</h4>
      <span class="sidebar-user-role">Administrator</span>
    </div>
  </div>
  
  <div class="sidebar-divider"><span>Main Navigation</span></div>
  
  <nav class="sidebar-nav">
    <a href="../" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-tachometer-alt"></i></div>
      <span>Dashboard</span>
    </a>
    
    <div class="sidebar-divider"><span>Content Management</span></div>
    
    <a href="./blogs.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-calendar-check"></i></div>
      <span>Blogs</span>
    </a>
    <a href="./carrent.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-car"></i></div>
      <span>Cars</span>
    </a>
    <a href="./rooms.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-bed"></i></div>
      <span>Rooms</span>
    </a>
    <a href="./event.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-calendar-alt"></i></div>
      <span>Events</span>
    </a>
    <a href="./shop.html" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-store"></i></div>
      <span>Shop</span>
    </a>
    
    <div class="sidebar-divider"><span>Page Management</span></div>
    
    <a href="./hero.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-image"></i></div>
      <span>Hero</span>
    </a>
    <a href="./homeabout.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-home"></i></div>
      <span>Home About</span>
    </a>
    <a href="./about.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-info-circle"></i></div>
      <span>About Us</span>
    </a>
    
    <div class="sidebar-divider"><span>Account</span></div>
    
    <a href="./logout.php" class="sidebar-link">
      <div class="sidebar-link-icon"><i class="fas fa-sign-out-alt"></i></div>
      <span>Logout</span>
    </a>
  </nav>
  
  <div class="sidebar-footer">
    <div class="sidebar-footer-content">
      <span>Virunga Homestay &copy; 2023</span>
    </div>
  </div>
</aside>
<style>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 260px;
  height: 100vh;
  background: linear-gradient(180deg, #232946 0%, #1a1f36 100%);
  color: #fff;
  box-shadow: 2px 0 20px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  z-index: 1000;
  transition: all 0.3s ease;
  overflow-y: auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(255,255,255,0.2) transparent;
}

.sidebar::-webkit-scrollbar {
  width: 5px;
}

.sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
  background-color: rgba(255,255,255,0.2);
  border-radius: 10px;
}

.sidebar.small {
  width: 70px;
}

/* Hide text in small sidebar mode */
.sidebar.small .sidebar-logo-text,
.sidebar.small .sidebar-user-info,
.sidebar.small .sidebar-divider span,
.sidebar.small .sidebar-link span,
.sidebar.small .sidebar-footer-content {
  display: none;
}

.sidebar.small .sidebar-footer {
  justify-content: center;
  padding: 1rem 0.5rem;
  /* Ensuring footer is properly styled in small mode */
}

/* Center icons in small sidebar mode */
.sidebar.small .sidebar-link {
  justify-content: center;
}

.sidebar.small .sidebar-link-icon {
  margin-right: 0;
  margin: 0 auto;
}

.sidebar.hide {
  left: -260px;
  /* Add box-shadow when visible to emphasize it's there */
  box-shadow: none;
}

/* Ensure sidebar is visible by default */
.sidebar:not(.hide) {
  box-shadow: 2px 0 20px rgba(0,0,0,0.1);
}

.sidebar-logo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  min-height: 60px;
  background: rgba(0,0,0,0.1);
}

.sidebar.small .sidebar-logo {
  justify-content: center;
  padding: 1rem 0.5rem;
}



.sidebar.small .sidebar-controls {
  display: none;
  /* Removing conflicting position rule */
}

.sidebar-logo-container {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.sidebar-controls {
  display: flex;
  gap: 0.5rem;
}
.sidebar-logo-icon {
  color: var(--secondary-color);
  font-size: 1.5rem;
  filter: drop-shadow(0 0 3px rgba(238, 191, 99, 0.3));
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: rgba(238, 191, 99, 0.1);
  border-radius: 10px;
  transition: all 0.3s ease;
}

.sidebar-logo-text {
  font-size: 1.2rem;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  background: linear-gradient(to right, #fff, #eebf63);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  letter-spacing: 0.5px;
}

.sidebar-toggle, .sidebar-hide {
  background: rgba(255,255,255,0.1);
  border: none;
  color: #fff;
  font-size: 1rem;
  cursor: pointer;
  padding: 0.4rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
}

.sidebar-toggle:hover, .sidebar-hide:hover {
  background: rgba(255,255,255,0.2);
  transform: translateY(-2px);
}

.sidebar-hide {
  display: inline-flex;
}

.sidebar-user {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.2rem 1rem;
  background: rgba(0,0,0,0.05);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}



.sidebar-avatar {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  object-fit: cover;
  border: 2px solid var(--secondary-color);
  box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
}

.sidebar-user-info {
  display: flex;
  flex-direction: column;
}

.sidebar-user-name {
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0;
  color: white;
}

.sidebar-user-role {
  font-size: 0.8rem;
  color: var(--secondary-color);
  opacity: 0.9;
}

.sidebar-divider {
  padding: 0 1rem;
  margin: 1rem 0 0.5rem 0;
  font-size: 0.75rem;
  color: rgba(255,255,255,0.5);
  text-transform: uppercase;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.sidebar.small .sidebar-divider {
  padding: 0;
  justify-content: center;
}

.sidebar-divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: rgba(255,255,255,0.1);
  display: block;
}
.sidebar-nav {
  display: flex;
  flex-direction: column;
  padding: 0.5rem 0;
  gap: 0.2rem;
  flex: 1;
}

.sidebar-link {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  padding: 0.8rem 1rem;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.2s ease;
  border-radius: 8px;
  margin: 0 0.5rem;
  position: relative;
  overflow: hidden;
}

/* Adjust sidebar link in small mode */
.sidebar.small .sidebar-link {
  padding: 0.8rem 0.5rem;
  justify-content: center;
}

.sidebar-link-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  margin-right: 0.5rem;
}

.sidebar.small .sidebar-link-icon {
  margin-right: 0;
  border-radius: 8px;
  background: rgba(255,255,255,0.1);
  transition: all 0.2s ease;
}

.sidebar-link-icon i {
  font-size: 1rem;
  color: rgba(255,255,255,0.8);
  transition: all 0.2s ease;
}

.sidebar-link.active {
  background: rgba(238, 191, 99, 0.15);
  color: white;
}

.sidebar-link.active .sidebar-link-icon {
  background: var(--secondary-color);
}

.sidebar-link.active .sidebar-link-icon i {
  color: var(--primary-color);
}

.sidebar-link:hover {
  background: rgba(255,255,255,0.1);
  color: white;
  transform: translateY(-2px);
}

.sidebar-link:hover .sidebar-link-icon {
  background: rgba(238, 191, 99, 0.2);
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid rgba(255,255,255,0.05);
  font-size: 0.8rem;
  color: rgba(255,255,255,0.5);
  text-align: center;
}












@media (max-width: 900px) {
  .sidebar {
    left: 0;
  }
  .sidebar.hide {
    left: -260px;
  }
  .sidebar-toggle {
    display: inline-flex;
  }
}
@media (max-width: 600px) {
  .sidebar {
    width: 220px;
    font-size: 0.95rem;
  }
  
  .sidebar.small {
    width: 60px;
  }
  
  .sidebar-link {
    padding: 0.7rem 0.7rem;
    font-size: 0.9rem;
  }
  
  .sidebar-logo {
    font-size: 0.95rem;
    padding: 0.7rem;
  }
  
  .sidebar-user-name {
    font-size: 0.9rem;
  }
  
  .sidebar-user-role {
    font-size: 0.75rem;
  }
}
</style>
<script>
// Wait for DOM to be fully loaded before initializing sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const hideBtn = document.getElementById('sidebarHide');
  
  if (sidebar) {
    // Make sure sidebar is visible by default
    sidebar.classList.remove('hide');
    
    // Clear any previous hidden state to ensure sidebar is visible
    localStorage.removeItem('sidebarHidden');
    
    // Always start with full sidebar (not small)
    sidebar.classList.remove('small');
    localStorage.removeItem('sidebarSmall');
    
    // Set up toggle button to completely hide sidebar
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('hide');
        // Persist state in localStorage
        if (sidebar.classList.contains('hide')) {
          localStorage.setItem('sidebarHidden', '1');
        } else {
          localStorage.removeItem('sidebarHidden');
        }
        console.log('Sidebar toggle button clicked. Sidebar hidden:', sidebar.classList.contains('hide'));
      });
    }
    
    // Set up hide/small button to toggle between full and small view
    if (hideBtn) {
      hideBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('small');
        if (sidebar.classList.contains('small')) {
          localStorage.setItem('sidebarSmall', '1');
        } else {
          localStorage.removeItem('sidebarSmall');
        }
        console.log('Sidebar hide button clicked. Sidebar small:', sidebar.classList.contains('small'));
      });
    }
    
    // Highlight active link
    const links = document.querySelectorAll('.sidebar-link');
    const path = window.location.pathname.split('/').pop();
    links.forEach(link => {
      if (link.getAttribute('href').includes(path)) {
        link.classList.add('active');
      }
    });
  } else {
    console.error('Sidebar element not found');
  }
});
// All sidebar functionality is now handled inside the DOMContentLoaded event handler
</script>
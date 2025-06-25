<!-- Modern Responsive Admin Header -->
<header class="admin-header" id="adminHeader">
  <div class="header-left">
    <button class="header-toggle" id="headerSidebarToggle" aria-label="Toggle Sidebar" style="display: flex;">
      <i class="fas fa-bars"></i>
    </button>
    <div class="header-brand">
      <i class="fas fa-home header-logo-icon"></i>
      <span class="header-title">Virunga Admin</span>
    </div>
  </div>
  <div class="header-right">
    <div class="header-user-container">
      <span class="header-user">Admin</span>
      <img src="../../img/admin-avatar.png" alt="Admin Avatar" class="header-avatar">
    </div>
    <div class="header-actions">
      <button class="header-bell" aria-label="Notifications">
        <i class="fas fa-bell"></i>
        <span class="notification-badge">3</span>
      </button>
      <button class="header-settings" aria-label="Settings">
        <i class="fas fa-cog"></i>
      </button>
    </div>
  </div>
</header>
<style>
.admin-header {
  position: sticky;
  top: 0;
  left: 0;
  width: 100%;
  background: linear-gradient(135deg, #232946 0%, #2a3158 100%);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.7rem 1.5rem;
  z-index: 1100;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  backdrop-filter: blur(10px);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.header-logo-icon {
  color: var(--secondary-color);
  font-size: 1.4rem;
  filter: drop-shadow(0 0 2px rgba(238, 191, 99, 0.3));
}

.header-toggle {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  font-size: 1.2rem;
  cursor: pointer;
  margin-right: 0.7rem;
  padding: 0.4rem 0.6rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.header-toggle:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.header-title {
  font-size: 1.2rem;
  font-weight: 600;
  letter-spacing: 1px;
  background: linear-gradient(to right, #fff, #eebf63);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1.2rem;
}

.header-search {
  position: relative;
  display: flex;
  align-items: center;
}

.header-search-input {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 20px;
  padding: 0.5rem 1rem 0.5rem 2.5rem;
  color: white;
  font-size: 0.9rem;
  width: 200px;
  transition: all 0.3s ease;
}

.header-search-input::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.header-search-input:focus {
  outline: none;
  background: rgba(255, 255, 255, 0.15);
  width: 250px;
  box-shadow: 0 0 0 2px rgba(238, 191, 99, 0.3);
}

.header-search-icon {
  position: absolute;
  left: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
  pointer-events: none;
}

.header-user-container {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.1);
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.2s ease;
}

.header-user-container:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
}

.header-user {
  font-size: 0.95rem;
  font-weight: 500;
  margin-right: 0.5rem;
}

.header-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--secondary-color);
  box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.header-actions {
  display: flex;
  gap: 0.8rem;
}

.header-bell, .header-settings {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.header-bell:hover, .header-settings:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: var(--danger-color);
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #232946;
}
@media (max-width: 1024px) {
  .header-search-input {
    width: 150px;
  }
  
  .header-search-input:focus {
    width: 180px;
  }
}

@media (max-width: 768px) {
  .header-search {
    display: none;
  }
}

@media (max-width: 700px) {
  .admin-header {
    padding: 0.7rem 0.7rem;
  }
  
  .header-title {
    font-size: 1rem;
  }
  
  .header-avatar {
    width: 32px;
    height: 32px;
  }
  
  .header-user {
    display: none;
  }
  
  .header-user-container {
    background: transparent;
    border: none;
    padding: 0;
  }
  
  .header-bell, .header-settings {
    width: 32px;
    height: 32px;
    font-size: 1rem;
  }
}
</style>
<script>
// Header toggle controls sidebar
// Wait for DOM to be fully loaded before initializing header functionality
document.addEventListener('DOMContentLoaded', function() {
  // Get references to elements after DOM is loaded
  const sidebar = document.getElementById('adminSidebar');
  const headerSidebarToggle = document.getElementById('headerSidebarToggle');
  
  console.log('Header.php: DOM loaded, sidebar element:', sidebar ? 'found' : 'not found');
  console.log('Header.php: DOM loaded, toggle button:', headerSidebarToggle ? 'found' : 'not found');
  
  if (headerSidebarToggle && sidebar) {
    // Make sure toggle button is visible
    headerSidebarToggle.style.display = 'flex';
    
    // Add click event listener
    headerSidebarToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      // Toggle small class instead of hide class
      sidebar.classList.toggle('small');
      
      // Update localStorage
      if (sidebar.classList.contains('small')) {
        localStorage.setItem('sidebarSmall', '1');
      } else {
        localStorage.removeItem('sidebarSmall');
      }
      
      // Log for debugging
      console.log('Header.php: Sidebar toggle clicked. Sidebar small:', sidebar.classList.contains('small'));
      console.log('Header.php: Sidebar classes:', sidebar.className);
    });
    
    console.log('Header.php: Event listener added to toggle button');
  } else {
    console.error('Header.php: Sidebar or toggle button not found');
  }
});
</script>

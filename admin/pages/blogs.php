<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Blogs - Admin Panel</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../css/blog-admin-modern.css" />
  </head>
  <body>
  <?php include 'sidebar.php'; ?>
  <?php include 'header.php'; ?>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="admin-container">
      <div class="page-header">
        <h1>Manage Blogs</h1>
        <div class="page-actions">
          <div class="search-container">
            <input type="text" id="searchBlogs" placeholder="Search blogs..." class="search-input">
            <button id="clearSearch" class="clear-search" style="display: none;">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="filter-container">
            <select id="blogFilter" class="filter-select">
              <option value="newest">Newest First</option>
              <option value="oldest">Oldest First</option>
              <option value="a-z">Title (A-Z)</option>
              <option value="z-a">Title (Z-A)</option>
            </select>
          </div>
          <a href="addblog.php" class="btn-primary">
            <i class="fas fa-plus"></i> Add New Blog
          </a>
        </div>
      </div>

      <!-- Loading state -->
      <div class="loading-state" id="loadingState">
        <div class="loading-spinner"></div>
        <p class="loading-text">Loading blog posts...</p>
      </div>
      
      <!-- Empty state message -->
      <div class="empty-state" id="emptyState">
        <div class="empty-state-icon">
          <i class="fas fa-file-alt"></i>
        </div>
        <h3>No Blogs Found</h3>
        <p>There are no blog posts available. Click the button below to create your first blog post.</p>
        <a href="addblog.php" class="btn-primary">
          <i class="fas fa-plus"></i> Create New Blog
        </a>
      </div>

      <div class="blogs-grid" id="blogsGrid">
        <!-- Blog cards will be dynamically added here -->
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
          <button class="close-modal" onclick="closeDeleteModal()">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p>
            Are you sure you want to delete this blog post? This action cannot
            be undone.
          </p>
          <div class="modal-actions">
            <button class="btn-secondary" onclick="closeDeleteModal()">
              <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn-danger" onclick="confirmDelete()">
              <i class="fas fa-trash-alt"></i> Delete
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
      // --- AJAX CRUD for Blogs ---
      let blogs = [];
      let filteredBlogs = [];
      let blogToDelete = null;

      function fetchBlogs() {
        const grid = document.getElementById("blogsGrid");
        const loadingState = document.getElementById("loadingState");
        const emptyState = document.getElementById("emptyState");
        
        // Show loading state
        grid.style.display = "none";
        emptyState.style.display = "none";
        loadingState.style.display = "block";
        
        fetch("blogs_api.php")
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              blogs = data.blogs;
              filteredBlogs = [...blogs];
              // Hide loading state
              loadingState.style.display = "none";
              renderFilteredBlogs();
            }
          })
          .catch((error) => {
            console.error("Error fetching blogs:", error);
            // Hide loading state and show error message
            loadingState.style.display = "none";
            grid.style.display = "none";
            emptyState.style.display = "block";
            emptyState.innerHTML = `
              <div class="empty-state-icon error">
                <i class="fas fa-exclamation-circle"></i>
              </div>
              <h3>Error Loading Blogs</h3>
              <p>There was a problem loading the blog posts. Please try again later.</p>
              <button class="btn-primary" onclick="fetchBlogs()">
                <i class="fas fa-sync"></i> Try Again
              </button>
            `;
          });
      }

      function renderBlogs() {
        filteredBlogs = [...blogs];
        renderFilteredBlogs();
      }
      
      function renderFilteredBlogs() {
        const grid = document.getElementById("blogsGrid");
        const emptyState = document.getElementById("emptyState");
        grid.innerHTML = "";
        
        if (filteredBlogs.length === 0) {
          emptyState.style.display = "block";
          grid.style.display = "none";
          
          // Check if we're filtering or if there are no blogs at all
          const searchInput = document.getElementById("searchBlogs");
          if (searchInput.value.trim() && blogs.length > 0) {
            emptyState.innerHTML = `
              <div class="empty-state-icon">
                <i class="fas fa-search"></i>
              </div>
              <h3>No Matching Blogs</h3>
              <p>No blogs match your search criteria. Try a different search term.</p>
              <button class="btn-secondary" onclick="clearSearch()">
                <i class="fas fa-times"></i> Clear Search
              </button>
            `;
          } else {
            // Default empty state for no blogs
            emptyState.innerHTML = `
              <div class="empty-state-icon">
                <i class="fas fa-file-alt"></i>
              </div>
              <h3>No Blogs Found</h3>
              <p>There are no blog posts available. Click the button below to create your first blog post.</p>
              <a href="addblog.php" class="btn-primary">
                <i class="fas fa-plus"></i> Create New Blog
              </a>
            `;
          }
          return;
        } else {
          emptyState.style.display = "none";
          grid.style.display = "grid";
        }
        
        filteredBlogs.forEach((blog) => {
          // Create content preview
          let contentPreview = blog.content || "";
          if (contentPreview.length > 100) {
            contentPreview = contentPreview.substring(0, 100) + "...";
          }
          // Remove HTML tags for preview
          contentPreview = contentPreview.replace(/<[^>]*>/g, "");
          
          const card = document.createElement("div");
          card.className = "blog-card";
          card.innerHTML = `
                    <div class="blog-card-image">
                        <img src="../../${blog.image}" alt="${blog.title}">
                    </div>
                    <div class="blog-card-content">
                        <h3>${blog.title}</h3>
                        <p class="subtitle">${contentPreview}</p>
                        <p class="date"><i class="far fa-calendar-alt"></i> ${formatDate(
                          blog.published_at || blog.created_at
                        )}</p>
                    </div>
                    <div class="blog-card-actions">
                        <a href="addblog.php?id=${
                          blog.id
                        }" class="btn-edit" data-tooltip="Edit Blog">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="openDeleteModal(${
                          blog.id
                        })" class="btn-delete" data-tooltip="Delete Blog">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
          grid.appendChild(card);
        });
      }

      function formatDate(dateString) {
        if (!dateString) return "";
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
      }

      function openDeleteModal(blogId) {
        blogToDelete = blogId;
        document.getElementById("deleteModal").style.display = "flex";
      }

      function closeDeleteModal() {
        document.getElementById("deleteModal").style.display = "none";
        blogToDelete = null;
      }

      function confirmDelete() {
        if (blogToDelete !== null) {
          // Show loading state in the delete button
          const deleteBtn = document.querySelector(".modal-actions .btn-danger");
          const originalText = deleteBtn.innerHTML;
          deleteBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Deleting...`;
          deleteBtn.disabled = true;
          
          const formData = new FormData();
          formData.append("action", "delete");
          formData.append("id", blogToDelete);
          fetch("blogs_api.php", {
            method: "POST",
            body: formData,
          })
            .then((res) => res.json())
            .then((data) => {
              if (data.success) {
                fetchBlogs();
                closeDeleteModal();
                showToast("Blog post deleted successfully", "success");
              } else {
                showToast("Failed to delete blog post", "error");
                // Reset button
                deleteBtn.innerHTML = originalText;
                deleteBtn.disabled = false;
              }
            })
            .catch(error => {
              console.error("Error deleting blog:", error);
              showToast("An error occurred while deleting the blog post", "error");
              // Reset button
              deleteBtn.innerHTML = originalText;
              deleteBtn.disabled = false;
            });
        }
      }

      // Close modal when clicking outside
      window.onclick = function (event) {
        const modal = document.getElementById("deleteModal");
        if (event.target === modal) {
          closeDeleteModal();
        }
      };

      // Initialize blogs on page load
      document.addEventListener("DOMContentLoaded", function () {
        fetchBlogs();
        setupSearch();
      });
      
      // Setup search functionality
      function setupSearch() {
        const searchInput = document.getElementById("searchBlogs");
        const clearButton = document.getElementById("clearSearch");
        const filterSelect = document.getElementById("blogFilter");
        
        searchInput.addEventListener("input", function() {
          const searchTerm = this.value.toLowerCase().trim();
          
          if (searchTerm) {
            clearButton.style.display = "flex";
            filteredBlogs = blogs.filter(blog => 
              blog.title.toLowerCase().includes(searchTerm) || 
              (blog.content && blog.content.toLowerCase().includes(searchTerm))
            );
          } else {
            clearButton.style.display = "none";
            filteredBlogs = [...blogs];
          }
          
          // Apply current sort after filtering
          applySorting(filterSelect.value);
          renderFilteredBlogs();
        });
        
        clearButton.addEventListener("click", function() {
          searchInput.value = "";
          clearButton.style.display = "none";
          filteredBlogs = [...blogs];
          // Apply current sort after clearing filter
          applySorting(filterSelect.value);
          renderFilteredBlogs();
          searchInput.focus();
        });
        
        // Setup filter/sort functionality
        filterSelect.addEventListener("change", function() {
          applySorting(this.value);
          renderFilteredBlogs();
          showToast(`Blogs sorted by ${this.options[this.selectedIndex].text}`, "success");
        });
      }
      
      // Apply sorting to filtered blogs
      function applySorting(sortType) {
        switch(sortType) {
          case "newest":
            filteredBlogs.sort((a, b) => {
              const dateA = new Date(a.published_at || a.created_at);
              const dateB = new Date(b.published_at || b.created_at);
              return dateB - dateA;
            });
            break;
          case "oldest":
            filteredBlogs.sort((a, b) => {
              const dateA = new Date(a.published_at || a.created_at);
              const dateB = new Date(b.published_at || b.created_at);
              return dateA - dateB;
            });
            break;
          case "a-z":
            filteredBlogs.sort((a, b) => a.title.localeCompare(b.title));
            break;
          case "z-a":
            filteredBlogs.sort((a, b) => b.title.localeCompare(a.title));
            break;
          default:
            // Default to newest first
            filteredBlogs.sort((a, b) => {
              const dateA = new Date(a.published_at || a.created_at);
              const dateB = new Date(b.published_at || b.created_at);
              return dateB - dateA;
            });
        }
      }
      
      // Clear search helper function
      function clearSearch() {
        const searchInput = document.getElementById("searchBlogs");
        const clearButton = document.getElementById("clearSearch");
        searchInput.value = "";
        clearButton.style.display = "none";
        filteredBlogs = [...blogs];
        renderFilteredBlogs();
      }
      
      // Toast notification system
      function showToast(message, type = "info") {
        const toastContainer = document.getElementById("toastContainer");
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        
        // Set icon based on type
        let icon = "";
        switch(type) {
          case "success":
            icon = "<i class='fas fa-check-circle toast-icon'></i>";
            break;
          case "error":
            icon = "<i class='fas fa-exclamation-circle toast-icon'></i>";
            break;
          case "warning":
            icon = "<i class='fas fa-exclamation-triangle toast-icon'></i>";
            break;
          case "info":
          default:
            icon = "<i class='fas fa-info-circle toast-icon'></i>";
            break;
        }
        
        toast.innerHTML = `
          ${icon}
          <span class="toast-message">${message}</span>
          <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
          </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
          if (toast.parentElement) {
            toast.remove();
          }
        }, 3000);
      }
    </script>
  </body>
</html>

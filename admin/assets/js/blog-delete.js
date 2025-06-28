/**
 * Blog Delete Functionality
 * Professional delete confirmation modal for blog posts
 */

let blogToDelete = null;
let deleteUrl = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeBlogDelete();
    initializeSearch();
    initializeFilters();
});

function initializeBlogDelete() {
    const deleteButtons = document.querySelectorAll('.action-btn.delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const blogTitle = this.getAttribute('data-item-name');
            deleteUrl = this.href;
            showDeleteModal(blogTitle);
        });
    });

    // Close modal when clicking outside
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    }
}

function showDeleteModal(blogTitle) {
    const titleElement = document.getElementById('blogTitle');
    const modal = document.getElementById('deleteModal');
    
    if (titleElement && modal) {
        titleElement.textContent = blogTitle;
        modal.style.display = 'flex';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'none';
    }
    blogToDelete = null;
    deleteUrl = null;
}

async function confirmDelete() {
    if (!deleteUrl) return;

    const deleteBtn = document.getElementById('deleteBtn');
    if (!deleteBtn) return;
    
    const originalText = deleteBtn.innerHTML;
    
    // Show loading state
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    deleteBtn.disabled = true;

    try {
        console.log('Making delete request to:', deleteUrl);
        
        const response = await fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData() // Send empty form data to trigger POST
        });

        console.log('Response status:', response.status);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        console.log('Content-Type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
            // If not JSON, get text to see what was returned
            const text = await response.text();
            console.error('Expected JSON but got:', text.substring(0, 500));
            throw new Error('Server returned non-JSON response. Check console for details.');
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            closeDeleteModal();
            // Show success message and reload
            console.log('Delete successful, reloading page...');
            showNotification(data.message || 'Blog post deleted successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            console.error('Delete failed:', data);
            throw new Error(data.message || 'Failed to delete blog post');
        }
    } catch (error) {
        console.error('Delete error:', error);

        // Don't show error if it's just a response parsing issue but deletion might have worked
        if (error.message && error.message.includes('Server returned non-JSON response')) {
            console.log('Possible server redirect or HTML response - checking if deletion actually worked...');
            // Wait a moment then reload to see if the item was actually deleted
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            return;
        }

        const errorMessage = error.message || 'An error occurred while deleting the blog post.';
        showNotification(errorMessage, 'danger');

        // Reset button
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}

function showNotification(message, type) {
    console.log('Showing notification:', message, type);

    // Clear any existing notifications first
    clearExistingNotifications();

    if (window.Dashboard && window.Dashboard.showNotification) {
        console.log('Using Dashboard notification system');
        window.Dashboard.showNotification(message, type);
    } else {
        console.log('Using fallback notification system');
        // Fallback notification
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show blog-notification" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('.blog-notification');
            if (alert) alert.remove();
        }, 5000);
    }
}

function clearExistingNotifications() {
    // Clear any existing blog notifications
    const existingNotifications = document.querySelectorAll('.blog-notification');
    existingNotifications.forEach(notification => notification.remove());

    // Also clear any dashboard notifications that might be showing errors
    const dashboardNotifications = document.querySelectorAll('.notification, .alert');
    dashboardNotifications.forEach(notification => {
        if (notification.textContent.includes('error occurred while deleting')) {
            notification.remove();
        }
    });
}

function initializeSearch() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                const url = new URL(window.location);
                if (this.value.trim()) {
                    url.searchParams.set('search', this.value.trim());
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            }, 500);
        });
    }
}

function initializeFilters() {
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const url = new URL(window.location);
            if (this.value) {
                url.searchParams.set('status', this.value);
            } else {
                url.searchParams.delete('status');
            }
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        });
    }
}

// Make functions available globally for onclick handlers
window.showDeleteModal = showDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;

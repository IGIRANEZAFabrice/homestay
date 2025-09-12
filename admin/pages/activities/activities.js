/**
 * Activities Management JavaScript
 * Handles CRUD operations for activities in Virunga Homestay Admin Dashboard
 */

class ActivitiesManager {
    constructor() {
        this.apiBaseUrl = '/admin/backend/api/activities';
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.totalItems = 0;
        this.activities = [];
        this.init();
    }

    /**
     * Initialize activities manager
     */
    init() {
        this.setupEventListeners();
        this.loadActivities();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchActivities');
        if (searchInput) {
            searchInput.addEventListener('input', Utils.debounce(() => {
                this.currentPage = 1;
                this.loadActivities();
            }, 500));
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.currentPage = 1;
                this.loadActivities();
            });
        }

        // Create activity form
        const createForm = document.getElementById('createActivityForm');
        if (createForm) {
            createForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createActivity();
            });
        }

        // Edit activity form
        const editForm = document.getElementById('editActivityForm');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updateActivity();
            });
        }

        // Image upload handling
        this.setupImageUpload();
    }

    /**
     * Setup image upload functionality
     */
    setupImageUpload() {
        const imageInput = document.getElementById('activityImages');
        const uploadArea = document.querySelector('.image-upload-area');
        const previewContainer = document.getElementById('imagePreviewContainer');

        if (!imageInput || !uploadArea || !previewContainer) return;

        // Handle file selection
        imageInput.addEventListener('change', (e) => {
            this.handleImageSelection(e.target.files);
        });

        // Handle drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            this.handleImageSelection(e.dataTransfer.files);
        });
    }

    /**
     * Handle image selection
     * @param {FileList} files Selected files
     */
    handleImageSelection(files) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        if (!previewContainer) return;

        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) {
                showToast('Please select only image files', 'warning');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                showToast('Image size should be less than 5MB', 'warning');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-image" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        });
    }

    /**
     * Load activities from API
     */
    async loadActivities() {
        try {
            const searchTerm = document.getElementById('searchActivities')?.value || '';
            const statusFilter = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams({
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: searchTerm,
                status: statusFilter
            });

            // TODO: Replace with actual API endpoint
            // Backend developers should create: GET /admin/backend/api/activities/list.php
            // Expected response: { success: true, data: { items: [...], pagination: {...} } }

            const response = await fetch(`${this.apiBaseUrl}/list.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.activities = data.data.items;
                this.totalItems = data.data.pagination.total_items;
                this.displayActivities();
                this.displayPagination(data.data.pagination);
            } else {
                throw new Error(data.message || 'Failed to load activities');
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            
            // Show demo data for development
            this.displayDemoActivities();
            showToast('Using demo data - API not connected', 'warning');
        }
    }

    /**
     * Display demo activities for development
     */
    displayDemoActivities() {
        const allDemoActivities = [
            {
                id: 1,
                title: 'Gorilla Trekking Experience',
                description: 'An unforgettable journey to see mountain gorillas in their natural habitat.',
                duration: 'Full Day',
                price: 750.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Gorilla',
                created_at: '2024-01-15 10:30:00'
            },
            {
                id: 2,
                title: 'Volcano Hiking Adventure',
                description: 'Challenging hike to the summit of Mount Nyiragongo volcano.',
                duration: '2 Days',
                price: 450.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Volcano',
                created_at: '2024-01-14 14:20:00'
            },
            {
                id: 3,
                title: 'Bird Watching Tour',
                description: 'Discover the diverse bird species of Virunga National Park.',
                duration: 'Half Day',
                price: 120.00,
                status: 'inactive',
                image: 'https://via.placeholder.com/60x60?text=Birds',
                created_at: '2024-01-13 09:15:00'
            },
            {
                id: 4,
                title: 'Cultural Village Visit',
                description: 'Experience authentic Rwandan culture and traditions.',
                duration: '3 Hours',
                price: 80.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Culture',
                created_at: '2024-01-12 16:45:00'
            },
            {
                id: 5,
                title: 'Lake Kivu Boat Tour',
                description: 'Scenic boat ride on the beautiful Lake Kivu.',
                duration: '4 Hours',
                price: 200.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Lake',
                created_at: '2024-01-11 11:20:00'
            },
            {
                id: 6,
                title: 'Coffee Plantation Tour',
                description: 'Learn about Rwandan coffee production and taste local brews.',
                duration: '2 Hours',
                price: 60.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Coffee',
                created_at: '2024-01-10 09:30:00'
            },
            {
                id: 7,
                title: 'Mountain Biking Adventure',
                description: 'Explore the scenic trails around Musanze on mountain bikes.',
                duration: 'Full Day',
                price: 150.00,
                status: 'inactive',
                image: 'https://via.placeholder.com/60x60?text=Biking',
                created_at: '2024-01-09 14:15:00'
            },
            {
                id: 8,
                title: 'Photography Workshop',
                description: 'Professional photography guidance in stunning natural settings.',
                duration: '6 Hours',
                price: 300.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Photo',
                created_at: '2024-01-08 10:00:00'
            },
            {
                id: 9,
                title: 'Traditional Dance Performance',
                description: 'Watch and participate in traditional Rwandan dance.',
                duration: '2 Hours',
                price: 90.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Dance',
                created_at: '2024-01-07 18:30:00'
            },
            {
                id: 10,
                title: 'Sunset Safari',
                description: 'Evening wildlife viewing in the national park.',
                duration: '3 Hours',
                price: 180.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Safari',
                created_at: '2024-01-06 15:45:00'
            },
            {
                id: 11,
                title: 'Local Market Tour',
                description: 'Explore vibrant local markets and taste street food.',
                duration: '2 Hours',
                price: 40.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Market',
                created_at: '2024-01-05 12:00:00'
            },
            {
                id: 12,
                title: 'Conservation Education',
                description: 'Learn about wildlife conservation efforts in the region.',
                duration: '1 Hour',
                price: 25.00,
                status: 'active',
                image: 'https://via.placeholder.com/60x60?text=Conservation',
                created_at: '2024-01-04 13:20:00'
            }
        ];

        // Apply search filter
        let filteredActivities = allDemoActivities;
        const searchTerm = document.getElementById('searchActivities')?.value?.toLowerCase() || '';
        if (searchTerm) {
            filteredActivities = allDemoActivities.filter(activity => 
                activity.title.toLowerCase().includes(searchTerm) ||
                activity.description.toLowerCase().includes(searchTerm)
            );
        }

        // Apply status filter
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        if (statusFilter) {
            filteredActivities = filteredActivities.filter(activity => 
                activity.status === statusFilter
            );
        }

        this.totalItems = filteredActivities.length;
        
        // Apply pagination
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;
        this.activities = filteredActivities.slice(startIndex, endIndex);

        this.displayActivities();
        this.displayDemoPagination();
    }

    /**
     * Display activities in table
     */
    displayActivities() {
        const tbody = document.getElementById('activitiesTableBody');
        if (!tbody) return;

        if (this.activities.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center" style="padding: 2rem;">
                        <i class="fas fa-hiking" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem;"></i>
                        <p style="color: var(--gray-600); margin: 0;">No activities found</p>
                    </td>
                </tr>
            `;
            return;
        }

        const activitiesHtml = this.activities.map(activity => `
            <tr>
                <td>
                    <img src="${activity.image ? '/homestay/uploads/activities/' + activity.image : 'https://via.placeholder.com/60x60?text=No+Image'}"
                         alt="${activity.title}" class="image-preview">
                </td>
                <td>
                    <strong>${Utils.sanitizeHtml(activity.title)}</strong>
                    <br>
                    <small style="color: var(--gray-600);">
                        ${Utils.truncateText(activity.description || '', 50)}
                    </small>
                </td>
                <td>${activity.duration || 'N/A'}</td>
                <td>
                    ${activity.price ? '$' + parseFloat(activity.price).toFixed(2) : 'Free'}
                </td>
                <td>
                    <span class="status-badge ${activity.status}">
                        ${activity.status}
                    </span>
                </td>
                <td>${Utils.formatDate(activity.created_at, 'short')}</td>
                <td>
                    <div class="action-buttons-group">
                        <button class="btn btn-icon-sm btn-secondary" 
                                onclick="activitiesManager.editActivity(${activity.id})"
                                data-tooltip="Edit activity">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-icon-sm btn-danger" 
                                onclick="activitiesManager.deleteActivity(${activity.id}, '${Utils.sanitizeHtml(activity.title)}')"
                                data-tooltip="Delete activity">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tbody.innerHTML = activitiesHtml;

        // Reinitialize tooltips for new elements
        if (window.tooltipManager) {
            window.tooltipManager.refresh();
        }
    }

    /**
     * Display pagination
     * @param {Object} pagination Pagination data
     */
    displayPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        if (!container || !pagination) return;

        const { current_page, total_pages, has_prev_page, has_next_page } = pagination;

        if (total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHtml = '<div class="pagination">';

        // Previous button
        if (has_prev_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${current_page - 1}); return false;">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, current_page - 2);
        const endPage = Math.min(total_pages, current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === current_page ? 'active' : '';
            paginationHtml += `
                <li class="page-item ${isActive}">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${i}); return false;">
                        ${i}
                    </a>
                </li>
            `;
        }

        // Next button
        if (has_next_page) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${current_page + 1}); return false;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;
        }

        paginationHtml += '</div>';
        container.innerHTML = paginationHtml;
    }

    /**
     * Display demo pagination for development
     */
    displayDemoPagination() {
        const container = document.getElementById('paginationContainer');
        if (!container) return;

        const totalPages = Math.ceil(this.totalItems / this.itemsPerPage);
        
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        // Add pagination info
        const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
        
        let paginationHtml = `
            <div class="table-pagination">
                <div class="pagination-info">
                    Showing ${startItem} to ${endItem} of ${this.totalItems} activities
                </div>
                <ul class="pagination">
        `;

        // Previous button
        if (this.currentPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${this.currentPage - 1}); return false;">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            `;
        }

        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === this.currentPage ? 'active' : '';
            paginationHtml += `
                <li class="page-item ${isActive}">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${i}); return false;">
                        ${i}
                    </a>
                </li>
            `;
        }

        // Next button
        if (this.currentPage < totalPages) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="activitiesManager.goToPage(${this.currentPage + 1}); return false;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;
        }

        paginationHtml += '</ul></div>';
        container.innerHTML = paginationHtml;
    }

    /**
     * Go to specific page
     * @param {number} page Page number
     */
    goToPage(page) {
        this.currentPage = page;
        this.loadActivities();
    }

    /**
     * Create new activity
     */
    async createActivity() {
        try {
            const formData = new FormData(document.getElementById('createActivityForm'));
            
            // TODO: Replace with actual API endpoint
            // Backend developers should create: POST /admin/backend/api/activities/create.php
            // Expected payload: FormData with activity fields and images
            // Expected response: { success: true, data: {...}, message: "Activity created successfully" }

            const loadingId = showLoading('Creating activity...');

            const response = await fetch(`${this.apiBaseUrl}/create.php`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Activity created successfully', 'success');
                closeModal('createActivityModal');
                document.getElementById('createActivityForm').reset();
                document.getElementById('imagePreviewContainer').innerHTML = '';
                this.loadActivities();
            } else {
                throw new Error(data.message || 'Failed to create activity');
            }
        } catch (error) {
            console.error('Error creating activity:', error);
            showToast(error.message, 'error');
        } finally {
            modalManager.closeAndRemove(loadingId);
        }
    }

    /**
     * Edit activity
     * @param {number} activityId Activity ID
     */
    async editActivity(activityId) {
        try {
            // TODO: Replace with actual API endpoint
            // Backend developers should create: GET /admin/backend/api/activities/get.php?id={id}
            // Expected response: { success: true, data: {...} }

            const response = await fetch(`${this.apiBaseUrl}/get.php?id=${activityId}`);
            const data = await response.json();

            if (data.success) {
                this.populateEditForm(data.data);
                showModal('editActivityModal');
            } else {
                throw new Error(data.message || 'Failed to load activity');
            }
        } catch (error) {
            console.error('Error loading activity:', error);
            
            // Show demo data for development
            const demoActivity = this.activities.find(a => a.id == activityId);
            if (demoActivity) {
                this.populateEditForm(demoActivity);
                showModal('editActivityModal');
            } else {
                showToast('Activity not found', 'error');
            }
        }
    }

    /**
     * Populate edit form with activity data
     * @param {Object} activity Activity data
     */
    populateEditForm(activity) {
        document.getElementById('editActivityId').value = activity.id;
        document.getElementById('editActivityTitle').value = activity.title || '';
        document.getElementById('editActivityDuration').value = activity.duration || '';
        document.getElementById('editActivityDescription').value = activity.description || '';
        document.getElementById('editActivityPrice').value = activity.price || '';
        document.getElementById('editActivityStatus').value = activity.status || 'active';
    }

    /**
     * Update activity
     */
    async updateActivity() {
        try {
            const formData = new FormData(document.getElementById('editActivityForm'));
            const activityId = formData.get('id');

            // TODO: Replace with actual API endpoint
            // Backend developers should create: PUT /admin/backend/api/activities/update.php
            // Expected payload: FormData with activity fields
            // Expected response: { success: true, data: {...}, message: "Activity updated successfully" }

            const loadingId = showLoading('Updating activity...');

            const response = await fetch(`${this.apiBaseUrl}/update.php`, {
                method: 'POST', // Using POST for FormData compatibility
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast('Activity updated successfully', 'success');
                closeModal('editActivityModal');
                this.loadActivities();
            } else {
                throw new Error(data.message || 'Failed to update activity');
            }
        } catch (error) {
            console.error('Error updating activity:', error);
            showToast(error.message, 'error');
        } finally {
            modalManager.closeAndRemove(loadingId);
        }
    }

    /**
     * Delete activity
     * @param {number} activityId Activity ID
     * @param {string} activityTitle Activity title
     */
    async deleteActivity(activityId, activityTitle) {
        try {
            const confirmed = await showDeleteConfirmation(activityTitle, 'activity');
            if (!confirmed) return;

            // TODO: Replace with actual API endpoint
            // Backend developers should create: DELETE /admin/backend/api/activities/delete.php
            // Expected payload: { id: activityId }
            // Expected response: { success: true, message: "Activity deleted successfully" }

            const loadingId = showLoading('Deleting activity...');

            const response = await fetch(`${this.apiBaseUrl}/delete.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: activityId })
            });

            const data = await response.json();

            if (data.success) {
                showToast('Activity deleted successfully', 'success');
                this.loadActivities();
            } else {
                throw new Error(data.message || 'Failed to delete activity');
            }
        } catch (error) {
            console.error('Error deleting activity:', error);
            showToast(error.message, 'error');
        } finally {
            modalManager.closeAndRemove(loadingId);
        }
    }

    /**
     * Refresh activities list
     */
    refreshActivities() {
        this.currentPage = 1;
        this.loadActivities();
        showToast('Activities refreshed', 'info', 2000);
    }
}

// Global function for refresh button
function refreshActivities() {
    if (window.activitiesManager) {
        window.activitiesManager.refreshActivities();
    }
}

// Initialize activities manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.activitiesManager = new ActivitiesManager();
    console.log('Activities manager initialized');
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ActivitiesManager;
}

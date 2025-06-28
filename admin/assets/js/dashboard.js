/**
 * Dashboard JavaScript for Virunga Homestay Admin
 * Handles dashboard-specific functionality, statistics, and charts
 */

class Dashboard {
    constructor() {
        this.charts = {};
        this.statsData = {};
        this.refreshInterval = null;
        this.init();
    }

    /**
     * Initialize dashboard
     */
    init() {
        this.loadStatistics();
        this.loadRecentActivities();
        this.setupEventListeners();
        this.startAutoRefresh();
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Refresh button (if exists)
        const refreshBtn = document.getElementById('refreshDashboard');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshDashboard();
            });
        }

        // Quick action buttons
        const quickActions = document.querySelectorAll('.action-btn');
        quickActions.forEach(btn => {
            btn.addEventListener('click', (e) => {
                // Add loading state
                const icon = btn.querySelector('i');
                const originalClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin';
                
                // Restore after navigation
                setTimeout(() => {
                    icon.className = originalClass;
                }, 1000);
            });
        });
    }

    /**
     * Load dashboard statistics
     */
    async loadStatistics() {
        try {
            // TODO: Replace with actual API endpoint
            // Backend developers should create: GET /admin/backend/api/dashboard/stats.php
            // Expected response: { 
            //   success: true, 
            //   data: { 
            //     totalActivities: 12, 
            //     totalBlogs: 8, 
            //     totalCars: 5, 
            //     totalReviews: 45,
            //     totalRooms: 10,
            //     totalServices: 15,
            //     totalEvents: 3,
            //     totalMessages: 23
            //   } 
            // }

            const response = await fetch('/admin/backend/api/dashboard/stats.php');
            const data = await response.json();

            if (data.success) {
                this.updateStatistics(data.data);
            } else {
                throw new Error(data.message || 'Failed to load statistics');
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
            
            // Show demo data for development
            this.updateStatistics({
                totalActivities: 12,
                totalBlogs: 8,
                totalCars: 5,
                totalReviews: 45,
                totalRooms: 10,
                totalServices: 15,
                totalEvents: 3,
                totalMessages: 23
            });
        }
    }

    /**
     * Update statistics display
     * @param {Object} stats - Statistics data
     */
    updateStatistics(stats) {
        this.statsData = stats;

        // Update stat cards
        const statElements = {
            totalActivities: document.getElementById('totalActivities'),
            totalBlogs: document.getElementById('totalBlogs'),
            totalCars: document.getElementById('totalCars'),
            totalReviews: document.getElementById('totalReviews'),
            totalRooms: document.getElementById('totalRooms'),
            totalServices: document.getElementById('totalServices'),
            totalEvents: document.getElementById('totalEvents'),
            totalMessages: document.getElementById('totalMessages')
        };

        Object.entries(statElements).forEach(([key, element]) => {
            if (element && stats[key] !== undefined) {
                this.animateNumber(element, parseInt(element.textContent) || 0, stats[key]);
            }
        });

        // Update charts if they exist
        this.updateCharts(stats);
    }

    /**
     * Animate number counting
     * @param {Element} element - Element to animate
     * @param {number} start - Start number
     * @param {number} end - End number
     */
    animateNumber(element, start, end) {
        const duration = 1000; // 1 second
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.round(start + (end - start) * easeOutQuart);
            
            element.textContent = Utils.formatNumber(current);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    /**
     * Load recent activities
     */
    async loadRecentActivities() {
        try {
            // TODO: Replace with actual API endpoint
            // Backend developers should create: GET /admin/backend/api/dashboard/recent-activities.php
            // Expected response: { 
            //   success: true, 
            //   data: [
            //     { type: 'blog', title: 'New blog post created', time: '2024-01-15 10:30:00', icon: 'fas fa-blog' },
            //     { type: 'review', title: 'New review received', time: '2024-01-15 09:15:00', icon: 'fas fa-star' }
            //   ] 
            // }

            const response = await fetch('/admin/backend/api/dashboard/recent-activities.php');
            const data = await response.json();

            if (data.success) {
                this.displayRecentActivities(data.data);
            } else {
                throw new Error(data.message || 'Failed to load recent activities');
            }
        } catch (error) {
            console.error('Error loading recent activities:', error);
            
            // Show demo data for development
            this.displayRecentActivities([
                {
                    type: 'blog',
                    title: 'New blog post: "Gorilla Trekking Experience"',
                    time: '2024-01-15 10:30:00',
                    icon: 'fas fa-blog'
                },
                {
                    type: 'review',
                    title: 'New 5-star review received',
                    time: '2024-01-15 09:15:00',
                    icon: 'fas fa-star'
                },
                {
                    type: 'booking',
                    title: 'New car rental booking',
                    time: '2024-01-15 08:45:00',
                    icon: 'fas fa-car'
                },
                {
                    type: 'message',
                    title: 'New contact message received',
                    time: '2024-01-14 16:20:00',
                    icon: 'fas fa-envelope'
                }
            ]);
        }
    }

    /**
     * Display recent activities
     * @param {Array} activities - Activities data
     */
    displayRecentActivities(activities) {
        const container = document.getElementById('recentActivitiesList');
        if (!container) return;

        if (activities.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">No recent activities</p>';
            return;
        }

        const activitiesHtml = activities.map(activity => `
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="${activity.icon}"></i>
                </div>
                <div class="activity-info">
                    <h4>${activity.title}</h4>
                    <p>${Utils.formatDate(activity.time, 'datetime')}</p>
                </div>
                <div class="activity-time">
                    ${this.getTimeAgo(activity.time)}
                </div>
            </div>
        `).join('');

        container.innerHTML = activitiesHtml;
    }

    /**
     * Get time ago string
     * @param {string} timestamp - Timestamp
     * @returns {string} Time ago string
     */
    getTimeAgo(timestamp) {
        const now = new Date();
        const time = new Date(timestamp);
        const diffInSeconds = Math.floor((now - time) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }

    /**
     * Update charts
     * @param {Object} stats - Statistics data
     */
    updateCharts(stats) {
        // Create or update revenue chart if container exists
        const revenueChartContainer = document.getElementById('revenueChart');
        if (revenueChartContainer && typeof Chart !== 'undefined') {
            this.createRevenueChart(revenueChartContainer, stats);
        }

        // Create or update activity chart if container exists
        const activityChartContainer = document.getElementById('activityChart');
        if (activityChartContainer && typeof Chart !== 'undefined') {
            this.createActivityChart(activityChartContainer, stats);
        }
    }

    /**
     * Create revenue chart
     * @param {Element} container - Chart container
     * @param {Object} stats - Statistics data
     */
    createRevenueChart(container, stats) {
        const ctx = container.getContext('2d');
        
        // Destroy existing chart
        if (this.charts.revenue) {
            this.charts.revenue.destroy();
        }

        // Demo data - replace with actual revenue data
        const revenueData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 15000, 18000, 14000, 22000, 25000],
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        };

        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: revenueData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Create activity chart
     * @param {Element} container - Chart container
     * @param {Object} stats - Statistics data
     */
    createActivityChart(container, stats) {
        const ctx = container.getContext('2d');
        
        // Destroy existing chart
        if (this.charts.activity) {
            this.charts.activity.destroy();
        }

        const activityData = {
            labels: ['Activities', 'Blogs', 'Cars', 'Reviews', 'Rooms', 'Services'],
            datasets: [{
                data: [
                    stats.totalActivities || 0,
                    stats.totalBlogs || 0,
                    stats.totalCars || 0,
                    stats.totalReviews || 0,
                    stats.totalRooms || 0,
                    stats.totalServices || 0
                ],
                backgroundColor: [
                    '#6366f1',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#06b6d4'
                ],
                borderWidth: 0
            }]
        };

        this.charts.activity = new Chart(ctx, {
            type: 'doughnut',
            data: activityData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    /**
     * Refresh dashboard data
     */
    async refreshDashboard() {
        const loadingId = showLoading('Refreshing dashboard...');
        
        try {
            await Promise.all([
                this.loadStatistics(),
                this.loadRecentActivities()
            ]);
            
            showToast('Dashboard refreshed successfully', 'success');
        } catch (error) {
            console.error('Error refreshing dashboard:', error);
            showToast('Failed to refresh dashboard', 'error');
        } finally {
            modalManager.closeAndRemove(loadingId);
        }
    }

    /**
     * Start auto-refresh
     */
    startAutoRefresh() {
        // Refresh every 5 minutes
        this.refreshInterval = setInterval(() => {
            this.loadStatistics();
            this.loadRecentActivities();
        }, 5 * 60 * 1000);
    }

    /**
     * Stop auto-refresh
     */
    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    /**
     * Get statistics data
     * @returns {Object} Statistics data
     */
    getStats() {
        return this.statsData;
    }

    /**
     * Destroy dashboard
     */
    destroy() {
        this.stopAutoRefresh();
        
        // Destroy charts
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
        
        this.charts = {};
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on dashboard page
    if (document.body.classList.contains('dashboard-page')) {
        window.dashboard = new Dashboard();
        console.log('Dashboard initialized');
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Dashboard;
}

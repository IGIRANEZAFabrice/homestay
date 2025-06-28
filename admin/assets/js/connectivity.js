/**
 * Connectivity Manager for Virunga Homestay Admin Dashboard
 * Handles online/offline status and queues requests when offline
 */

class ConnectivityManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.indicator = null;
        this.pendingRequests = [];
        this.checkInterval = null;
        this.retryAttempts = 3;
        this.retryDelay = 5000; // 5 seconds
        
        this.init();
    }

    /**
     * Initialize connectivity manager
     */
    init() {
        this.createIndicator();
        this.setupEventListeners();
        this.updateIndicator();
        this.startPeriodicCheck();
    }

    /**
     * Create connectivity indicator element
     */
    createIndicator() {
        this.indicator = document.getElementById('connectivityStatus');
        if (!this.indicator) {
            this.indicator = document.createElement('div');
            this.indicator.id = 'connectivityStatus';
            this.indicator.className = 'connectivity-indicator';
            this.indicator.innerHTML = `
                <i class="fas fa-wifi"></i>
                <span class="status-text">Online</span>
            `;
            document.body.appendChild(this.indicator);
        }
    }

    /**
     * Setup event listeners for connectivity changes
     */
    setupEventListeners() {
        window.addEventListener('online', () => {
            this.handleOnline();
        });
        
        window.addEventListener('offline', () => {
            this.handleOffline();
        });
        
        // Listen for visibility change to check connectivity when tab becomes active
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !this.isOnline) {
                this.checkConnectivity();
            }
        });
    }

    /**
     * Handle online event
     */
    handleOnline() {
        console.log('Connection restored');
        this.isOnline = true;
        this.updateIndicator();
        this.processPendingRequests();
        this.showToast('Connection restored', 'success');
    }

    /**
     * Handle offline event
     */
    handleOffline() {
        console.log('Connection lost');
        this.isOnline = false;
        this.updateIndicator();
        this.showToast('Connection lost. Changes will be saved when connection is restored.', 'warning');
    }

    /**
     * Update connectivity indicator
     */
    updateIndicator() {
        if (!this.indicator) return;

        const icon = this.indicator.querySelector('i');
        const text = this.indicator.querySelector('.status-text');
        
        // Remove all status classes
        this.indicator.classList.remove('online', 'offline', 'reconnecting');
        
        if (this.isOnline) {
            this.indicator.classList.add('online');
            icon.className = 'fas fa-wifi';
            text.textContent = 'Online';
        } else {
            this.indicator.classList.add('offline');
            icon.className = 'fas fa-wifi-slash';
            text.textContent = 'Offline';
        }
    }

    /**
     * Set reconnecting status
     */
    setReconnecting() {
        if (!this.indicator) return;

        const icon = this.indicator.querySelector('i');
        const text = this.indicator.querySelector('.status-text');
        
        this.indicator.classList.remove('online', 'offline');
        this.indicator.classList.add('reconnecting');
        icon.className = 'fas fa-sync fa-spin';
        text.textContent = 'Reconnecting...';
    }

    /**
     * Check connectivity by making a test request
     */
    async checkConnectivity() {
        try {
            // Make a lightweight request to check connectivity
            const response = await fetch('/homestay/admin/backend/api/ping.php', {
                method: 'HEAD',
                cache: 'no-cache',
                timeout: 5000
            });
            
            if (response.ok && !this.isOnline) {
                this.handleOnline();
            }
        } catch (error) {
            if (this.isOnline) {
                this.handleOffline();
            }
        }
    }

    /**
     * Start periodic connectivity check
     */
    startPeriodicCheck() {
        this.checkInterval = setInterval(() => {
            this.checkConnectivity();
        }, 30000); // Check every 30 seconds
    }

    /**
     * Stop periodic connectivity check
     */
    stopPeriodicCheck() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }

    /**
     * Queue request for offline handling
     * @param {Object} requestData - Request data to queue
     * @returns {boolean} - True if request was queued, false if online
     */
    queueRequest(requestData) {
        if (this.isOnline) {
            return false; // Don't queue if online
        }

        // Add timestamp and unique ID to request
        const queuedRequest = {
            id: Utils.generateId(),
            timestamp: Date.now(),
            attempts: 0,
            ...requestData
        };

        this.pendingRequests.push(queuedRequest);
        
        // Store in localStorage for persistence
        Utils.storage.set('pendingRequests', this.pendingRequests);
        
        console.log('Request queued for offline handling:', queuedRequest);
        return true;
    }

    /**
     * Process all pending requests when connection is restored
     */
    async processPendingRequests() {
        if (this.pendingRequests.length === 0) {
            // Check localStorage for persisted requests
            const storedRequests = Utils.storage.get('pendingRequests', []);
            this.pendingRequests = storedRequests;
        }

        if (this.pendingRequests.length === 0) return;

        this.setReconnecting();
        
        console.log(`Processing ${this.pendingRequests.length} pending requests`);
        
        const processedRequests = [];
        const failedRequests = [];

        for (const request of this.pendingRequests) {
            try {
                const success = await this.executeRequest(request);
                if (success) {
                    processedRequests.push(request);
                } else {
                    request.attempts++;
                    if (request.attempts < this.retryAttempts) {
                        failedRequests.push(request);
                    } else {
                        console.error('Request failed after maximum attempts:', request);
                    }
                }
            } catch (error) {
                console.error('Error processing request:', error);
                request.attempts++;
                if (request.attempts < this.retryAttempts) {
                    failedRequests.push(request);
                }
            }
        }

        // Update pending requests list
        this.pendingRequests = failedRequests;
        Utils.storage.set('pendingRequests', this.pendingRequests);

        // Update indicator
        this.updateIndicator();

        // Show results
        if (processedRequests.length > 0) {
            this.showToast(`${processedRequests.length} changes synchronized successfully`, 'success');
        }

        if (failedRequests.length > 0) {
            this.showToast(`${failedRequests.length} changes failed to sync. Will retry later.`, 'warning');
        }
    }

    /**
     * Execute a queued request
     * @param {Object} request - Request to execute
     * @returns {Promise<boolean>} - Success status
     */
    async executeRequest(request) {
        try {
            const { method = 'POST', url, data, headers = {} } = request;
            
            const fetchOptions = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    ...headers
                },
                body: method !== 'GET' ? JSON.stringify(data) : undefined
            };

            const response = await fetch(url, fetchOptions);
            
            if (response.ok) {
                console.log('Request executed successfully:', request.id);
                return true;
            } else {
                console.error('Request failed with status:', response.status);
                return false;
            }
        } catch (error) {
            console.error('Error executing request:', error);
            return false;
        }
    }

    /**
     * Show toast notification
     * @param {string} message - Message to show
     * @param {string} type - Toast type (success, warning, error)
     */
    showToast(message, type = 'info') {
        // Check if toast function exists (from modals.js)
        if (typeof showToast === 'function') {
            showToast(message, type);
        } else {
            // Fallback to console log
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

    /**
     * Get pending requests count
     * @returns {number} - Number of pending requests
     */
    getPendingRequestsCount() {
        return this.pendingRequests.length;
    }

    /**
     * Clear all pending requests
     */
    clearPendingRequests() {
        this.pendingRequests = [];
        Utils.storage.remove('pendingRequests');
    }

    /**
     * Destroy connectivity manager
     */
    destroy() {
        this.stopPeriodicCheck();
        
        window.removeEventListener('online', this.handleOnline);
        window.removeEventListener('offline', this.handleOffline);
        
        if (this.indicator && this.indicator.parentNode) {
            this.indicator.parentNode.removeChild(this.indicator);
        }
    }
}

// Initialize connectivity manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.connectivityManager = new ConnectivityManager();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConnectivityManager;
}

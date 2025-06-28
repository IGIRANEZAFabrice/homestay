/**
 * Modal Manager for Virunga Homestay Admin Dashboard
 * Handles modal dialogs, confirmations, and toast notifications
 */

class ModalManager {
    constructor() {
        this.activeModals = new Set();
        this.toastContainer = null;
        this.init();
    }

    /**
     * Initialize modal manager
     */
    init() {
        this.createToastContainer();
        this.setupEventListeners();
        this.initializeExistingModals();
    }

    /**
     * Create toast container
     */
    createToastContainer() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.className = 'toast-container';
        document.body.appendChild(this.toastContainer);
    }

    /**
     * Setup global event listeners
     */
    setupEventListeners() {
        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeTopModal();
            }
        });

        // Close modal on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target.id);
            }
        });
    }

    /**
     * Initialize existing modals in the DOM
     */
    initializeExistingModals() {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            this.setupModalEvents(modal);
        });
    }

    /**
     * Setup events for a specific modal
     * @param {Element} modal - Modal element
     */
    setupModalEvents(modal) {
        const closeButtons = modal.querySelectorAll('.modal-close, [data-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.closeModal(modal.id);
            });
        });
    }

    /**
     * Show modal
     * @param {string} modalId - Modal ID
     * @param {Object} options - Modal options
     */
    showModal(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`Modal with ID '${modalId}' not found`);
            return;
        }

        // Add to active modals
        this.activeModals.add(modalId);

        // Show modal
        modal.style.display = 'flex';
        modal.classList.add('show');

        // Focus management
        const firstFocusable = modal.querySelector('input, button, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            setTimeout(() => firstFocusable.focus(), 100);
        }

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:show', { detail: options }));
    }

    /**
     * Close modal
     * @param {string} modalId - Modal ID
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Remove from active modals
        this.activeModals.delete(modalId);

        // Hide modal
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);

        // Restore body scroll if no modals are active
        if (this.activeModals.size === 0) {
            document.body.style.overflow = '';
        }

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:close'));
    }

    /**
     * Close the topmost modal
     */
    closeTopModal() {
        if (this.activeModals.size > 0) {
            const lastModal = Array.from(this.activeModals).pop();
            this.closeModal(lastModal);
        }
    }

    /**
     * Create and show confirmation modal
     * @param {Object} options - Confirmation options
     * @returns {Promise<boolean>} User's choice
     */
    showConfirmation(options = {}) {
        const {
            title = 'Confirm Action',
            message = 'Are you sure you want to proceed?',
            confirmText = 'Confirm',
            cancelText = 'Cancel',
            type = 'warning',
            icon = 'fas fa-exclamation-triangle'
        } = options;

        return new Promise((resolve) => {
            const modalId = `confirmModal-${Utils.generateId()}`;
            
            const modalHtml = `
                <div id="${modalId}" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="${icon} text-${type}"></i> ${title}</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-action="cancel">${cancelText}</button>
                            <button class="btn btn-${type}" data-action="confirm">${confirmText}</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = document.getElementById(modalId);

            // Setup event listeners
            modal.addEventListener('click', (e) => {
                const action = e.target.getAttribute('data-action');
                if (action === 'confirm') {
                    resolve(true);
                    this.closeModal(modalId);
                    setTimeout(() => modal.remove(), 300);
                } else if (action === 'cancel' || e.target.classList.contains('modal-close')) {
                    resolve(false);
                    this.closeModal(modalId);
                    setTimeout(() => modal.remove(), 300);
                }
            });

            this.setupModalEvents(modal);
            this.showModal(modalId);
        });
    }

    /**
     * Show delete confirmation modal
     * @param {string} itemName - Name of item to delete
     * @param {string} itemType - Type of item
     * @returns {Promise<boolean>} User's choice
     */
    showDeleteConfirmation(itemName, itemType = 'item') {
        return this.showConfirmation({
            title: 'Confirm Deletion',
            message: `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
            confirmText: 'Delete',
            cancelText: 'Cancel',
            type: 'danger',
            icon: 'fas fa-trash'
        });
    }

    /**
     * Show toast notification
     * @param {string} message - Toast message
     * @param {string} type - Toast type (success, warning, error, info)
     * @param {number} duration - Duration in milliseconds (0 = no auto-hide)
     */
    showToast(message, type = 'info', duration = 5000) {
        const toastId = `toast-${Utils.generateId()}`;
        const icons = {
            success: 'fas fa-check-circle',
            warning: 'fas fa-exclamation-triangle',
            error: 'fas fa-times-circle',
            info: 'fas fa-info-circle'
        };

        const toastHtml = `
            <div id="${toastId}" class="toast ${type}">
                <div class="toast-icon">
                    <i class="${icons[type] || icons.info}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        this.toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toast = document.getElementById(toastId);

        // Setup close button
        const closeButton = toast.querySelector('.toast-close');
        closeButton.addEventListener('click', () => {
            this.removeToast(toastId);
        });

        // Auto-hide if duration is set
        if (duration > 0) {
            setTimeout(() => {
                this.removeToast(toastId);
            }, duration);
        }

        return toastId;
    }

    /**
     * Remove toast notification
     * @param {string} toastId - Toast ID
     */
    removeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.style.animation = 'toastSlideOut 0.3s ease forwards';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    }

    /**
     * Show loading modal
     * @param {string} message - Loading message
     * @returns {string} Modal ID for closing
     */
    showLoading(message = 'Loading...') {
        const modalId = `loadingModal-${Utils.generateId()}`;
        
        const modalHtml = `
            <div id="${modalId}" class="modal">
                <div class="modal-content progress-modal">
                    <div class="modal-body">
                        <div class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>${message}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.showModal(modalId);
        
        return modalId;
    }

    /**
     * Show progress modal
     * @param {string} title - Progress title
     * @param {number} progress - Progress percentage (0-100)
     * @returns {string} Modal ID for updating
     */
    showProgress(title = 'Processing...', progress = 0) {
        const modalId = `progressModal-${Utils.generateId()}`;
        
        const modalHtml = `
            <div id="${modalId}" class="modal">
                <div class="modal-content progress-modal">
                    <div class="modal-header">
                        <h3>${title}</h3>
                    </div>
                    <div class="modal-body">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progress}%"></div>
                        </div>
                        <div class="progress-text">${progress}% complete</div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.showModal(modalId);
        
        return modalId;
    }

    /**
     * Update progress modal
     * @param {string} modalId - Modal ID
     * @param {number} progress - Progress percentage (0-100)
     * @param {string} text - Progress text (optional)
     */
    updateProgress(modalId, progress, text = null) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const progressFill = modal.querySelector('.progress-fill');
        const progressText = modal.querySelector('.progress-text');

        if (progressFill) {
            progressFill.style.width = `${progress}%`;
        }

        if (progressText) {
            progressText.textContent = text || `${progress}% complete`;
        }
    }

    /**
     * Close and remove temporary modal
     * @param {string} modalId - Modal ID
     */
    closeAndRemove(modalId) {
        this.closeModal(modalId);
        setTimeout(() => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.remove();
            }
        }, 300);
    }

    /**
     * Get active modal count
     * @returns {number} Number of active modals
     */
    getActiveModalCount() {
        return this.activeModals.size;
    }

    /**
     * Check if modal is active
     * @param {string} modalId - Modal ID
     * @returns {boolean} Is modal active
     */
    isModalActive(modalId) {
        return this.activeModals.has(modalId);
    }
}

// Global functions for easy access
function showModal(modalId, options = {}) {
    return modalManager.showModal(modalId, options);
}

function closeModal(modalId) {
    return modalManager.closeModal(modalId);
}

function showToast(message, type = 'info', duration = 5000) {
    return modalManager.showToast(message, type, duration);
}

function showConfirmation(options = {}) {
    return modalManager.showConfirmation(options);
}

function showDeleteConfirmation(itemName, itemType = 'item') {
    return modalManager.showDeleteConfirmation(itemName, itemType);
}

function showLoading(message = 'Loading...') {
    return modalManager.showLoading(message);
}

function showProgress(title = 'Processing...', progress = 0) {
    return modalManager.showProgress(title, progress);
}

function updateProgress(modalId, progress, text = null) {
    return modalManager.updateProgress(modalId, progress, text);
}

// Initialize modal manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.modalManager = new ModalManager();
    
    console.log('Modal manager initialized');
});

// Add CSS animation for toast slide out
const style = document.createElement('style');
style.textContent = `
    @keyframes toastSlideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModalManager;
}

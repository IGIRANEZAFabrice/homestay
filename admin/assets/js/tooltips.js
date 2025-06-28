/**
 * Tooltip Manager for Virunga Homestay Admin Dashboard
 * Handles tooltip initialization and management
 */

class TooltipManager {
    constructor() {
        this.tooltips = new Map();
        this.init();
    }

    /**
     * Initialize tooltip manager
     */
    init() {
        this.initializeTooltips();
        this.setupEventListeners();
    }

    /**
     * Initialize all tooltips on the page
     */
    initializeTooltips() {
        const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
        tooltipTriggers.forEach(trigger => {
            this.createTooltip(trigger);
        });
    }

    /**
     * Create tooltip for an element
     * @param {Element} element - Element to add tooltip to
     */
    createTooltip(element) {
        const tooltipText = element.getAttribute('data-tooltip');
        const position = element.getAttribute('data-tooltip-position') || 'top';
        const variant = element.getAttribute('data-tooltip-variant') || 'default';
        
        if (!tooltipText) return;

        // Add tooltip classes
        element.classList.add('tooltip-trigger');
        
        if (position !== 'top') {
            element.classList.add(`tooltip-${position}`);
        }
        
        if (variant !== 'default') {
            element.classList.add(`tooltip-${variant}`);
        }

        // Store tooltip info
        this.tooltips.set(element, {
            text: tooltipText,
            position,
            variant
        });

        // Add accessibility attributes
        const tooltipId = `tooltip-${Utils.generateId()}`;
        element.setAttribute('aria-describedby', tooltipId);
        element.setAttribute('data-tooltip-id', tooltipId);
    }

    /**
     * Setup event listeners for dynamic content
     */
    setupEventListeners() {
        // Observer for dynamically added content
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        // Check if the added node has tooltip
                        if (node.hasAttribute && node.hasAttribute('data-tooltip')) {
                            this.createTooltip(node);
                        }
                        
                        // Check for tooltip elements within the added node
                        const tooltipElements = node.querySelectorAll ? node.querySelectorAll('[data-tooltip]') : [];
                        tooltipElements.forEach(element => {
                            this.createTooltip(element);
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideAllTooltips();
            }
        });
    }

    /**
     * Add tooltip to element programmatically
     * @param {Element|string} element - Element or selector
     * @param {string} text - Tooltip text
     * @param {Object} options - Tooltip options
     */
    addTooltip(element, text, options = {}) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return;

        const {
            position = 'top',
            variant = 'default',
            delay = 500
        } = options;

        el.setAttribute('data-tooltip', text);
        el.setAttribute('data-tooltip-position', position);
        el.setAttribute('data-tooltip-variant', variant);
        
        this.createTooltip(el);
    }

    /**
     * Update tooltip text
     * @param {Element|string} element - Element or selector
     * @param {string} newText - New tooltip text
     */
    updateTooltip(element, newText) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return;

        el.setAttribute('data-tooltip', newText);
        
        if (this.tooltips.has(el)) {
            const tooltipInfo = this.tooltips.get(el);
            tooltipInfo.text = newText;
            this.tooltips.set(el, tooltipInfo);
        }
    }

    /**
     * Remove tooltip from element
     * @param {Element|string} element - Element or selector
     */
    removeTooltip(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return;

        el.removeAttribute('data-tooltip');
        el.removeAttribute('data-tooltip-position');
        el.removeAttribute('data-tooltip-variant');
        el.removeAttribute('aria-describedby');
        el.removeAttribute('data-tooltip-id');
        
        el.classList.remove('tooltip-trigger');
        el.classList.remove('tooltip-top', 'tooltip-bottom', 'tooltip-left', 'tooltip-right');
        el.classList.remove('tooltip-success', 'tooltip-warning', 'tooltip-error', 'tooltip-large');
        
        this.tooltips.delete(el);
    }

    /**
     * Hide all visible tooltips
     */
    hideAllTooltips() {
        // CSS handles tooltip visibility, but we can trigger blur on focused elements
        const focusedElement = document.activeElement;
        if (focusedElement && this.tooltips.has(focusedElement)) {
            focusedElement.blur();
        }
    }

    /**
     * Show tooltip programmatically
     * @param {Element|string} element - Element or selector
     */
    showTooltip(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el || !this.tooltips.has(el)) return;

        // Focus the element to show tooltip
        el.focus();
    }

    /**
     * Hide tooltip programmatically
     * @param {Element|string} element - Element or selector
     */
    hideTooltip(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return;

        el.blur();
    }

    /**
     * Get tooltip info for element
     * @param {Element|string} element - Element or selector
     * @returns {Object|null} Tooltip information
     */
    getTooltipInfo(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return null;

        return this.tooltips.get(el) || null;
    }

    /**
     * Check if element has tooltip
     * @param {Element|string} element - Element or selector
     * @returns {boolean} Has tooltip
     */
    hasTooltip(element) {
        const el = typeof element === 'string' ? document.querySelector(element) : element;
        if (!el) return false;

        return this.tooltips.has(el);
    }

    /**
     * Get all elements with tooltips
     * @returns {Array} Array of elements with tooltips
     */
    getAllTooltipElements() {
        return Array.from(this.tooltips.keys());
    }

    /**
     * Refresh all tooltips (useful after content changes)
     */
    refresh() {
        this.tooltips.clear();
        this.initializeTooltips();
    }

    /**
     * Destroy tooltip manager
     */
    destroy() {
        this.tooltips.clear();
        
        // Remove all tooltip classes and attributes
        const tooltipElements = document.querySelectorAll('.tooltip-trigger');
        tooltipElements.forEach(element => {
            this.removeTooltip(element);
        });
    }
}

// Utility functions for common tooltip operations
const TooltipUtils = {
    /**
     * Add form field tooltip with validation info
     * @param {Element|string} field - Form field element or selector
     * @param {string} description - Field description
     * @param {string} validation - Validation rules
     */
    addFormTooltip(field, description, validation = '') {
        const el = typeof field === 'string' ? document.querySelector(field) : field;
        if (!el) return;

        let tooltipText = description;
        if (validation) {
            tooltipText += `\n\nValidation: ${validation}`;
        }

        tooltipManager.addTooltip(el, tooltipText, {
            position: 'right',
            variant: 'large'
        });
    },

    /**
     * Add action button tooltip
     * @param {Element|string} button - Button element or selector
     * @param {string} action - Action description
     * @param {string} shortcut - Keyboard shortcut (optional)
     */
    addActionTooltip(button, action, shortcut = '') {
        const el = typeof button === 'string' ? document.querySelector(button) : button;
        if (!el) return;

        let tooltipText = action;
        if (shortcut) {
            tooltipText += ` (${shortcut})`;
        }

        tooltipManager.addTooltip(el, tooltipText, {
            position: 'bottom'
        });
    },

    /**
     * Add status indicator tooltip
     * @param {Element|string} indicator - Status indicator element or selector
     * @param {string} status - Status description
     * @param {string} variant - Tooltip variant (success, warning, error)
     */
    addStatusTooltip(indicator, status, variant = 'default') {
        const el = typeof indicator === 'string' ? document.querySelector(indicator) : indicator;
        if (!el) return;

        tooltipManager.addTooltip(el, status, {
            position: 'top',
            variant
        });
    }
};

// Initialize tooltip manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.tooltipManager = new TooltipManager();
    window.TooltipUtils = TooltipUtils;
    
    console.log('Tooltip manager initialized');
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { TooltipManager, TooltipUtils };
}

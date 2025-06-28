/**
 * Utility Functions for Virunga Homestay Admin Dashboard
 * Professional utility functions for common operations
 */

// Utility object to hold all utility functions
const Utils = {
    
    /**
     * Format date to readable string
     * @param {Date|string} date - Date to format
     * @param {string} format - Format type ('short', 'long', 'time')
     * @returns {string} Formatted date string
     */
    formatDate(date, format = 'short') {
        const d = new Date(date);
        const options = {
            short: { year: 'numeric', month: 'short', day: 'numeric' },
            long: { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' },
            time: { hour: '2-digit', minute: '2-digit', hour12: true },
            datetime: { 
                year: 'numeric', month: 'short', day: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true 
            }
        };
        
        return d.toLocaleDateString('en-US', options[format] || options.short);
    },

    /**
     * Format file size to human readable format
     * @param {number} bytes - File size in bytes
     * @returns {string} Formatted file size
     */
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    /**
     * Debounce function to limit function calls
     * @param {Function} func - Function to debounce
     * @param {number} wait - Wait time in milliseconds
     * @returns {Function} Debounced function
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle function to limit function calls
     * @param {Function} func - Function to throttle
     * @param {number} limit - Time limit in milliseconds
     * @returns {Function} Throttled function
     */
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Generate unique ID
     * @returns {string} Unique ID
     */
    generateId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    },

    /**
     * Sanitize HTML to prevent XSS
     * @param {string} str - String to sanitize
     * @returns {string} Sanitized string
     */
    sanitizeHtml(str) {
        const temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    },

    /**
     * Validate email format
     * @param {string} email - Email to validate
     * @returns {boolean} Is valid email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validate URL format
     * @param {string} url - URL to validate
     * @returns {boolean} Is valid URL
     */
    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    },

    /**
     * Copy text to clipboard
     * @param {string} text - Text to copy
     * @returns {Promise<boolean>} Success status
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            const success = document.execCommand('copy');
            document.body.removeChild(textArea);
            return success;
        }
    },

    /**
     * Get query parameter from URL
     * @param {string} param - Parameter name
     * @returns {string|null} Parameter value
     */
    getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    },

    /**
     * Set query parameter in URL
     * @param {string} param - Parameter name
     * @param {string} value - Parameter value
     */
    setQueryParam(param, value) {
        const url = new URL(window.location);
        url.searchParams.set(param, value);
        window.history.pushState({}, '', url);
    },

    /**
     * Remove query parameter from URL
     * @param {string} param - Parameter name
     */
    removeQueryParam(param) {
        const url = new URL(window.location);
        url.searchParams.delete(param);
        window.history.pushState({}, '', url);
    },

    /**
     * Format number with commas
     * @param {number} num - Number to format
     * @returns {string} Formatted number
     */
    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },

    /**
     * Truncate text to specified length
     * @param {string} text - Text to truncate
     * @param {number} length - Maximum length
     * @param {string} suffix - Suffix to add (default: '...')
     * @returns {string} Truncated text
     */
    truncateText(text, length, suffix = '...') {
        if (text.length <= length) return text;
        return text.substring(0, length) + suffix;
    },

    /**
     * Convert string to slug format
     * @param {string} str - String to convert
     * @returns {string} Slug format string
     */
    slugify(str) {
        return str
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    },

    /**
     * Check if element is in viewport
     * @param {Element} element - Element to check
     * @returns {boolean} Is in viewport
     */
    isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    },

    /**
     * Smooth scroll to element
     * @param {Element|string} target - Element or selector to scroll to
     * @param {number} offset - Offset from top (default: 0)
     */
    scrollToElement(target, offset = 0) {
        const element = typeof target === 'string' ? document.querySelector(target) : target;
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;
            
            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    },

    /**
     * Local storage helper with JSON support
     */
    storage: {
        set(key, value) {
            try {
                // Validate the value before storing
                if (value === undefined) {
                    console.warn(`localStorage.set('${key}'): Attempting to store undefined value, this will be stored as string "undefined"`);
                }
                if (value === null) {
                    console.warn(`localStorage.set('${key}'): Storing null value`);
                }

                const stringified = JSON.stringify(value);
                localStorage.setItem(key, stringified);

                // Verify the storage was successful
                const verification = localStorage.getItem(key);
                if (verification !== stringified) {
                    console.error(`localStorage.set('${key}'): Storage verification failed`);
                    return false;
                }

                console.log(`localStorage.set('${key}'): Successfully stored:`, typeof value === 'string' && value.length > 50 ? value.substring(0, 50) + '...' : value);
                return true;
            } catch (e) {
                console.error(`Error saving to localStorage key '${key}':`, e);
                console.error('Value being stored:', value);
                return false;
            }
        },

        get(key, defaultValue = null) {
            try {
                const item = localStorage.getItem(key);
                // Check if item is null, undefined, or the string "undefined" or "null"
                if (item === null || item === undefined || item === 'undefined' || item === 'null' || item === '') {
                    console.log(`localStorage.get('${key}'): Item is null/undefined/empty, returning default:`, defaultValue);
                    return defaultValue;
                }

                const parsed = JSON.parse(item);
                // Additional check for parsed values
                if (parsed === null || parsed === undefined) {
                    console.log(`localStorage.get('${key}'): Parsed value is null/undefined, returning default:`, defaultValue);
                    return defaultValue;
                }

                console.log(`localStorage.get('${key}'): Successfully retrieved:`, typeof parsed === 'string' && parsed.length > 50 ? parsed.substring(0, 50) + '...' : parsed);
                return parsed;
            } catch (e) {
                console.error(`Error reading from localStorage key '${key}':`, e);
                console.error('Raw item value:', localStorage.getItem(key));
                return defaultValue;
            }
        },

        remove(key) {
            try {
                localStorage.removeItem(key);
                return true;
            } catch (e) {
                console.error('Error removing from localStorage:', e);
                return false;
            }
        },

        clear() {
            try {
                localStorage.clear();
                return true;
            } catch (e) {
                console.error('Error clearing localStorage:', e);
                return false;
            }
        }
    },

    /**
     * Session storage helper with JSON support
     */
    sessionStorage: {
        set(key, value) {
            try {
                sessionStorage.setItem(key, JSON.stringify(value));
                return true;
            } catch (e) {
                console.error('Error saving to sessionStorage:', e);
                return false;
            }
        },

        get(key, defaultValue = null) {
            try {
                const item = sessionStorage.getItem(key);
                return item ? JSON.parse(item) : defaultValue;
            } catch (e) {
                console.error('Error reading from sessionStorage:', e);
                return defaultValue;
            }
        },

        remove(key) {
            try {
                sessionStorage.removeItem(key);
                return true;
            } catch (e) {
                console.error('Error removing from sessionStorage:', e);
                return false;
            }
        },

        clear() {
            try {
                sessionStorage.clear();
                return true;
            } catch (e) {
                console.error('Error clearing sessionStorage:', e);
                return false;
            }
        }
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Utils;
}

/**
 * Main JavaScript for Virunga Homestay Admin Dashboard
 * Handles authentication, navigation, and core functionality
 */

console.log('🔥 MAIN.JS FILE LOADED 🔥');

// Test if code execution continues
console.log('🧪 Testing code execution...');

// Global application state
const App = {
    isAuthenticated: false,
    currentUser: null,
    apiBaseUrl: '/homestay/admin/backend/api',

    // Debug helper function
    debugStorage() {
        console.log('=== STORAGE DEBUG ===');
        console.log('localStorage length:', localStorage.length);
        console.log('All localStorage keys:', Object.keys(localStorage));

        // Check specific keys
        const keys = ['authToken', 'currentUser', 'rememberLogin'];
        keys.forEach(key => {
            const raw = localStorage.getItem(key);
            const parsed = Utils.storage.get(key);
            console.log(`${key}:`);
            console.log(`  Raw: ${raw}`);
            console.log(`  Raw type: ${typeof raw}`);
            console.log(`  Parsed: ${parsed}`);
            console.log(`  Parsed type: ${typeof parsed}`);
        });
        console.log('=== END STORAGE DEBUG ===');
    },
    
    // Initialize application
    init() {
        console.log('🔧 App.init() started');

        try {
            console.log('1. Calling checkAuthentication()...');
            this.checkAuthentication();
            console.log('2. checkAuthentication() completed');

            console.log('3. Calling setupEventListeners()...');
            this.setupEventListeners();
            console.log('4. setupEventListeners() completed');

            console.log('5. Calling initializeComponents()...');
            this.initializeComponents();
            console.log('6. initializeComponents() completed');

            console.log('✅ App.init() completed successfully');
        } catch (error) {
            console.error('❌ Error in App.init():', error);
            console.error('Error stack:', error.stack);
            throw error;
        }
    },

    // Check if user is authenticated (disabled for session-based auth)
    checkAuthentication() {
        console.log('=== AUTHENTICATION CHECK DISABLED ===');
        console.log('Using session-based authentication instead of JWT');
        console.log('Authentication is handled by individual pages');

        // For pages that need authentication, they should check session directly
        // This method is kept for compatibility but does nothing
        return;
    },

    // Verify authentication token
    async verifyToken(token) {
        console.log('Starting token verification...');
        console.log('Token:', token);
        console.log('Verify URL:', `${this.apiBaseUrl}/auth/verify.php`);

        try {
            const response = await fetch(`${this.apiBaseUrl}/auth/verify.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log('Verify response status:', response.status);
            console.log('Verify response ok:', response.ok);

            if (!response.ok) {
                const errorText = await response.text();
                console.log('Verify response error text:', errorText);
                throw new Error(`Token verification failed: ${response.status}`);
            }

            const data = await response.json();
            console.log('Verify response data:', data);

            if (!data.success) {
                throw new Error(`Invalid token: ${data.message}`);
            }

            console.log('Token verification successful!');
        } catch (error) {
            console.error('Token verification failed:', error);
            console.log('Logging out due to verification failure...');
            this.logout();
        }
    },

    // Setup global event listeners
    setupEventListeners() {
        console.log('🔗 Setting up event listeners...');

        // Login form submission
        const loginForm = document.getElementById('loginForm');
        console.log('Login form element:', loginForm);

        if (loginForm) {
            console.log('✅ Login form found, adding event listener');

            // Create bound function for form submission
            const boundHandleLogin = this.handleLogin.bind(this);

            // Add form submit event listener
            loginForm.addEventListener('submit', (e) => {
                console.log('📋 FORM SUBMIT EVENT TRIGGERED!');
                console.log('Form submit event:', e);
                boundHandleLogin(e);
            });

            console.log('✅ Login form event listener added');

            // Also add click listener to submit button as backup
            const submitButton = loginForm.querySelector('button[type="submit"]');
            console.log('Submit button:', submitButton);
            if (submitButton) {
                const self = this; // Store reference to 'this'
                submitButton.addEventListener('click', (e) => {
                    console.log('🔘 SUBMIT BUTTON CLICKED!');
                    console.log('Event:', e);
                    e.preventDefault();

                    // Create a proper form submit event
                    const fakeEvent = {
                        preventDefault: () => {
                            console.log('✅ Default form submission prevented');
                        },
                        target: loginForm
                    };

                    console.log('🚀 Calling handleLogin from button click...');
                    self.handleLogin(fakeEvent);
                });
                console.log('✅ Submit button click listener added');
            }
        } else {
            console.log('❌ Login form not found');
        }

        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', this.toggleSidebar.bind(this));
        }

        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', this.toggleMobileSidebar.bind(this));
        }

        // Password toggle
        const passwordToggles = document.querySelectorAll('.password-toggle');
        passwordToggles.forEach(toggle => {
            toggle.addEventListener('click', this.togglePassword.bind(this));
        });

        // Form auto-save
        this.setupAutoSave();

        // Prevent data loss on page unload
        this.setupUnloadWarning();
    },

    // Initialize components
    initializeComponents() {
        // Initialize tooltips for form fields
        this.initializeFormTooltips();
        
        // Initialize data tables
        this.initializeDataTables();
        
        // Setup AJAX error handling
        this.setupAjaxErrorHandling();
    },

    // Handle login form submission (disabled for session-based auth)
    async handleLogin(event) {
        console.log('🚀 LOGIN FORM SUBMITTED - BUT DISABLED');
        console.log('Using session-based authentication instead');
        console.log('Login is handled by login.php page');

        // Prevent default form submission
        event.preventDefault();

        // Show message that this is disabled
        console.log('JWT-based login is disabled. Please use pages/login.php');

        return;
    },

    // Logout user (updated for session-based auth)
    async logout() {
        console.log('=== LOGOUT TRIGGERED ===');
        console.log('Using session-based logout');

        try {
            // Call session logout endpoint
            const response = await fetch(`${this.apiBaseUrl}/auth/session_logout.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Logout response:', data);

        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            console.log('Redirecting to login page...');

            // Clear any old JWT data that might still be stored
            Utils.storage.remove('authToken');
            Utils.storage.remove('currentUser');
            Utils.storage.remove('rememberLogin');

            // Update app state
            this.isAuthenticated = false;
            this.currentUser = null;

            // Redirect to login
            window.location.href = '/homestay/admin/pages/login.php';
        }
    },

    // Toggle sidebar
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
            
            // Save preference
            const isCollapsed = sidebar.classList.contains('collapsed');
            Utils.storage.set('sidebarCollapsed', isCollapsed);
        }
    },

    // Toggle mobile sidebar
    toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    },

    // Toggle password visibility
    togglePassword(event) {
        const button = event.currentTarget;
        const input = button.parentElement.querySelector('input[type="password"], input[type="text"]');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    },

    // Setup auto-save for forms
    setupAutoSave() {
        const forms = document.querySelectorAll('form[data-autosave]');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', Utils.debounce(() => {
                    this.autoSaveForm(form);
                }, 2000));
            });
        });
    },

    // Auto-save form data
    autoSaveForm(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const formId = form.id || form.getAttribute('data-form-id');
        
        if (formId) {
            Utils.storage.set(`autosave_${formId}`, {
                data,
                timestamp: Date.now()
            });
            
            // Show subtle indication
            showToast('Draft saved', 'info', 2000);
        }
    },

    // Restore auto-saved form data
    restoreAutoSavedData(formId) {
        const saved = Utils.storage.get(`autosave_${formId}`);
        if (saved && saved.data) {
            const form = document.getElementById(formId);
            if (form) {
                Object.entries(saved.data).forEach(([name, value]) => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input) {
                        input.value = value;
                    }
                });
                
                showToast('Draft restored', 'info', 3000);
            }
        }
    },

    // Setup unload warning for unsaved changes
    setupUnloadWarning() {
        let hasUnsavedChanges = false;

        // Track form changes
        document.addEventListener('input', (e) => {
            if (e.target.form && !e.target.form.hasAttribute('data-no-warning')) {
                hasUnsavedChanges = true;
            }
        });

        // Reset on form submission
        document.addEventListener('submit', () => {
            hasUnsavedChanges = false;
        });

        // Store the unload handler reference
        this.unloadHandler = (e) => {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        };

        // Warn on page unload
        window.addEventListener('beforeunload', this.unloadHandler);
    },

    // Initialize form tooltips
    initializeFormTooltips() {
        // Add tooltips to form fields with validation requirements
        const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
        requiredFields.forEach(field => {
            if (!field.hasAttribute('data-tooltip')) {
                TooltipUtils.addFormTooltip(field, 'This field is required', 'Required field');
            }
        });

        // Add tooltips to email fields
        const emailFields = document.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            if (!field.hasAttribute('data-tooltip')) {
                TooltipUtils.addFormTooltip(field, 'Enter a valid email address', 'Valid email format required');
            }
        });

        // Add tooltips to password fields
        const passwordFields = document.querySelectorAll('input[type="password"]');
        passwordFields.forEach(field => {
            if (!field.hasAttribute('data-tooltip')) {
                TooltipUtils.addFormTooltip(field, 'Enter your password', 'Minimum 8 characters recommended');
            }
        });
    },

    // Initialize data tables
    initializeDataTables() {
        const tables = document.querySelectorAll('.data-table');
        tables.forEach(table => {
            this.enhanceTable(table);
        });
    },

    // Enhance table with sorting and filtering
    enhanceTable(table) {
        // Add sorting to headers
        const headers = table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(table, header);
            });
        });
    },

    // Sort table by column
    sortTable(table, header) {
        // TODO: Implement table sorting logic
        console.log('Sorting table by:', header.textContent);
    },

    // Setup AJAX error handling
    setupAjaxErrorHandling() {
        // Global fetch wrapper with error handling
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response;
            } catch (error) {
                // Handle network errors
                if (!navigator.onLine) {
                    showToast('No internet connection. Changes will be saved when connection is restored.', 'warning');
                } else {
                    console.error('Fetch error:', error);
                    showToast('Network error occurred. Please try again.', 'error');
                }
                throw error;
            }
        };
    }
};

// Global logout function
function logout() {
    showConfirmation({
        title: 'Confirm Logout',
        message: 'Are you sure you want to logout?',
        confirmText: 'Logout',
        type: 'warning'
    }).then(confirmed => {
        if (confirmed) {
            App.logout();
        }
    });
}

// Global password toggle function
function togglePassword() {
    App.togglePassword(event);
}

// Global debug functions for browser console
window.debugAuth = function() {
    console.log('=== MANUAL AUTH DEBUG ===');
    App.debugStorage();
    App.checkAuthentication();
};

window.clearAuthData = function() {
    console.log('=== CLEARING AUTH DATA ===');
    Utils.storage.remove('authToken');
    Utils.storage.remove('currentUser');
    Utils.storage.remove('rememberLogin');
    console.log('Auth data cleared');
};

window.setDebugFlag = function(value = true) {
    localStorage.setItem('debugDisableRedirect', value.toString());
    console.log('Debug redirect disable flag set to:', value);
};

// Debug: Simple redirect logging
const originalAssign = window.location.assign;
window.location.assign = function(url) {
    console.log('🚨 REDIRECT VIA ASSIGN:', url);
    console.log('Stack trace:', new Error().stack);
    return originalAssign.call(this, url);
};

const originalReplace = window.location.replace;
window.location.replace = function(url) {
    console.log('🚨 REDIRECT VIA REPLACE:', url);
    console.log('Stack trace:', new Error().stack);
    return originalReplace.call(this, url);
};

console.log('🎯 About to add DOMContentLoaded listener...');

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('🚀 DOM LOADED - INITIALIZING APP');

    try {
        console.log('About to call App.init()...');
        App.init();
        console.log('App.init() completed successfully');

        // Restore sidebar state
        console.log('Restoring sidebar state...');
        const sidebarCollapsed = Utils.storage.get('sidebarCollapsed', false);
        if (sidebarCollapsed) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.add('collapsed');
            }
        }
        console.log('Sidebar state restored');

        console.log('✅ Admin dashboard initialized successfully');
    } catch (error) {
        console.error('❌ Error during app initialization:', error);
        console.error('Error stack:', error.stack);
    }
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}

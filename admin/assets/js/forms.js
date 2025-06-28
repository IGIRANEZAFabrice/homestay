/**
 * Professional Form Handling JavaScript
 * For Virunga Homestay Admin Dashboard
 */

const FormHandler = {
    // Configuration
    config: {
        apiBaseUrl: '/admin/backend/api/',
        maxFileSize: 20 * 1024 * 1024, // 20MB
        allowedImageTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
        validationDelay: 300 // ms
    },

    // Initialize form handling
    init() {
        this.setupFormValidation();
        this.setupFileUploads();
        this.setupRichTextEditors();
        this.setupFormSubmissions();
        this.setupDynamicFields();
        console.log('Form handler initialized');
    },

    // Setup form validation
    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate="true"]');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                // Real-time validation on input
                input.addEventListener('input', () => {
                    clearTimeout(input.validationTimeout);
                    input.validationTimeout = setTimeout(() => {
                        this.validateField(input);
                    }, this.config.validationDelay);
                });

                // Validation on blur
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });

            // Form submission validation
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });
        });
    },

    // Validate individual field
    validateField(field) {
        const rules = this.getValidationRules(field);
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Required validation
        if (rules.required && !value) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} is required.`;
        }

        // Length validation
        if (isValid && rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} must be at least ${rules.minLength} characters.`;
        }

        if (isValid && rules.maxLength && value.length > rules.maxLength) {
            isValid = false;
            errorMessage = `${this.getFieldLabel(field)} must not exceed ${rules.maxLength} characters.`;
        }

        // Email validation
        if (isValid && rules.email && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
        }

        // Number validation
        if (isValid && rules.number && value) {
            if (isNaN(value)) {
                isValid = false;
                errorMessage = `${this.getFieldLabel(field)} must be a number.`;
            } else {
                const numValue = parseFloat(value);
                if (rules.min !== undefined && numValue < rules.min) {
                    isValid = false;
                    errorMessage = `${this.getFieldLabel(field)} must be at least ${rules.min}.`;
                }
                if (rules.max !== undefined && numValue > rules.max) {
                    isValid = false;
                    errorMessage = `${this.getFieldLabel(field)} must not exceed ${rules.max}.`;
                }
            }
        }

        // URL validation
        if (isValid && rules.url && value) {
            try {
                new URL(value);
            } catch {
                isValid = false;
                errorMessage = 'Please enter a valid URL.';
            }
        }

        this.setFieldValidation(field, isValid, errorMessage);
        return isValid;
    },

    // Get validation rules from field attributes
    getValidationRules(field) {
        return {
            required: field.hasAttribute('required'),
            minLength: field.getAttribute('data-min-length'),
            maxLength: field.getAttribute('data-max-length'),
            email: field.type === 'email',
            number: field.type === 'number',
            url: field.type === 'url',
            min: field.getAttribute('min'),
            max: field.getAttribute('max')
        };
    },

    // Get field label
    getFieldLabel(field) {
        const label = field.closest('.form-group')?.querySelector('label');
        return label ? label.textContent.replace('*', '').trim() : field.name;
    },

    // Set field validation state
    setFieldValidation(field, isValid, errorMessage) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;

        // Remove existing validation classes
        field.classList.remove('is-valid', 'is-invalid');
        
        // Remove existing feedback
        const existingFeedback = formGroup.querySelector('.invalid-feedback, .valid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        if (isValid) {
            field.classList.add('is-valid');
        } else {
            field.classList.add('is-invalid');
            
            // Add error message
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = errorMessage;
            formGroup.appendChild(feedback);
        }
    },

    // Validate entire form
    validateForm(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    },

    // Show form errors
    showFormErrors(form) {
        const firstInvalidField = form.querySelector('.is-invalid');
        if (firstInvalidField) {
            firstInvalidField.focus();
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },

    // Setup file uploads
    setupFileUploads() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e.target);
            });

            // Setup drag and drop if container exists
            const container = input.closest('.file-upload-container');
            if (container) {
                this.setupDragAndDrop(container, input);
            }
        });
    },

    // Handle file upload
    handleFileUpload(input) {
        const files = Array.from(input.files);
        const container = input.closest('.file-upload-container');
        
        files.forEach(file => {
            if (this.validateFile(file)) {
                this.previewFile(file, container);
            } else {
                input.value = ''; // Clear invalid file
            }
        });
    },

    // Validate file
    validateFile(file) {
        // Check file size
        if (file.size > this.config.maxFileSize) {
            alert(`File size must not exceed ${this.formatFileSize(this.config.maxFileSize)}`);
            return false;
        }

        // Check file type for images
        if (file.type.startsWith('image/') && !this.config.allowedImageTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, GIF, WebP)');
            return false;
        }

        return true;
    },

    // Preview file
    previewFile(file, container) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                let preview = container.querySelector('.image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'image-preview';
                    container.appendChild(preview);
                }

                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="preview-image">
                    <div class="preview-info">
                        <p><strong>${file.name}</strong></p>
                        <p>${this.formatFileSize(file.size)}</p>
                    </div>
                `;
                preview.classList.add('show');
            };
            reader.readAsDataURL(file);
        }
    },

    // Setup drag and drop
    setupDragAndDrop(container, input) {
        const label = container.querySelector('.file-upload-label');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            container.addEventListener(eventName, () => {
                label.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, () => {
                label.classList.remove('drag-over');
            });
        });

        container.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            input.files = files;
            this.handleFileUpload(input);
        });
    },

    // Setup rich text editors
    setupRichTextEditors() {
        const textareas = document.querySelectorAll('textarea[data-rich-text="true"]');
        
        textareas.forEach(textarea => {
            // Simple rich text editor implementation
            this.createRichTextEditor(textarea);
        });
    },

    // Create simple rich text editor
    createRichTextEditor(textarea) {
        const wrapper = document.createElement('div');
        wrapper.className = 'rich-text-editor';
        
        const toolbar = document.createElement('div');
        toolbar.className = 'rich-text-toolbar';
        toolbar.innerHTML = `
            <button type="button" data-command="bold"><i class="fas fa-bold"></i></button>
            <button type="button" data-command="italic"><i class="fas fa-italic"></i></button>
            <button type="button" data-command="underline"><i class="fas fa-underline"></i></button>
            <button type="button" data-command="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
            <button type="button" data-command="insertOrderedList"><i class="fas fa-list-ol"></i></button>
        `;

        const editor = document.createElement('div');
        editor.className = 'rich-text-content';
        editor.contentEditable = true;
        editor.innerHTML = textarea.value;

        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(toolbar);
        wrapper.appendChild(editor);
        wrapper.appendChild(textarea);

        // Toolbar events
        toolbar.addEventListener('click', (e) => {
            if (e.target.closest('button')) {
                e.preventDefault();
                const command = e.target.closest('button').dataset.command;
                document.execCommand(command, false, null);
                editor.focus();
            }
        });

        // Update textarea on content change
        editor.addEventListener('input', () => {
            textarea.value = editor.innerHTML;
        });
    },

    // Setup form submissions
    setupFormSubmissions() {
        const ajaxForms = document.querySelectorAll('form[data-ajax="true"]');
        
        ajaxForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitFormAjax(form);
            });
        });
    },

    // Submit form via AJAX
    async submitFormAjax(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.textContent : '';

        try {
            // Add loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else if (data.reset) {
                    form.reset();
                    this.clearValidation(form);
                }
            } else {
                this.showNotification(data.message, 'danger');
                
                // Show field-specific errors
                if (data.errors) {
                    this.showFieldErrors(form, data.errors);
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.showNotification('An error occurred. Please try again.', 'danger');
        } finally {
            // Remove loading state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = originalText;
            }
        }
    },

    // Show field-specific errors
    showFieldErrors(form, errors) {
        Object.entries(errors).forEach(([fieldName, errorMessage]) => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.setFieldValidation(field, false, errorMessage);
            }
        });
    },

    // Clear form validation
    clearValidation(form) {
        const fields = form.querySelectorAll('.is-valid, .is-invalid');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });

        const feedbacks = form.querySelectorAll('.invalid-feedback, .valid-feedback');
        feedbacks.forEach(feedback => feedback.remove());
    },

    // Setup dynamic fields
    setupDynamicFields() {
        // Auto-generate slug from title
        const titleFields = document.querySelectorAll('input[name="title"]');
        titleFields.forEach(titleField => {
            const slugField = document.querySelector('input[name="slug"]');
            if (slugField) {
                titleField.addEventListener('input', () => {
                    if (!slugField.value || slugField.dataset.autoGenerate !== 'false') {
                        slugField.value = this.generateSlug(titleField.value);
                    }
                });
            }
        });

        // Character counters
        const textareas = document.querySelectorAll('textarea[data-max-length]');
        textareas.forEach(textarea => {
            this.addCharacterCounter(textarea);
        });
    },

    // Generate slug from text
    generateSlug(text) {
        return text
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    },

    // Add character counter
    addCharacterCounter(textarea) {
        const maxLength = parseInt(textarea.getAttribute('data-max-length'));
        const counter = document.createElement('div');
        counter.className = 'character-counter';
        
        const updateCounter = () => {
            const remaining = maxLength - textarea.value.length;
            counter.textContent = `${remaining} characters remaining`;
            counter.className = `character-counter ${remaining < 0 ? 'text-danger' : ''}`;
        };

        textarea.parentNode.appendChild(counter);
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    },

    // Show notification
    showNotification(message, type = 'info') {
        // Use Dashboard notification if available, otherwise create simple alert
        if (window.Dashboard && window.Dashboard.showNotification) {
            window.Dashboard.showNotification(message, type);
        } else {
            alert(message);
        }
    },

    // Format file size
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    FormHandler.init();
});

// Make FormHandler available globally
window.FormHandler = FormHandler;

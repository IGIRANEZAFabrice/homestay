/**
 * Professional Image Upload Handler
 * For Virunga Homestay Admin Dashboard
 */

const ImageUpload = {
    // Configuration
    config: {
        maxFileSize: 20 * 1024 * 1024, // 20MB
        allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'],
        maxWidth: 2000,
        maxHeight: 2000,
        quality: 0.85,
        thumbnailSize: 300
    },

    // Initialize image upload functionality
    init() {
        this.setupImageInputs();
        this.setupDragAndDrop();
        this.setupImagePreview();
        console.log('Image upload handler initialized');
    },

    // Setup image input fields
    setupImageInputs() {
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleImageSelection(e.target);
            });

            // Create upload area if it doesn't exist
            this.createUploadArea(input);
        });
    },

    // Create upload area for image input
    createUploadArea(input) {
        if (input.closest('.image-upload-area')) return; // Already created

        const wrapper = document.createElement('div');
        wrapper.className = 'image-upload-area';

        const uploadZone = document.createElement('div');
        uploadZone.className = 'upload-zone';
        uploadZone.innerHTML = `
            <div class="upload-content">
                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                <p class="upload-text">
                    <strong>Click to upload</strong> or drag and drop<br>
                    <small>PNG, JPG, GIF, WebP up to ${this.formatFileSize(this.config.maxFileSize)}</small>
                </p>
            </div>
        `;

        const previewArea = document.createElement('div');
        previewArea.className = 'image-preview-area';

        // Hide original input
        input.style.display = 'none';

        // Insert wrapper before input
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(uploadZone);
        wrapper.appendChild(previewArea);
        wrapper.appendChild(input);

        // Make upload zone clickable
        uploadZone.addEventListener('click', () => {
            input.click();
        });

        // Setup drag and drop for this zone
        this.setupDragAndDropForZone(uploadZone, input);
    },

    // Handle image selection
    async handleImageSelection(input) {
        const files = Array.from(input.files);
        const wrapper = input.closest('.image-upload-area');
        const previewArea = wrapper.querySelector('.image-preview-area');

        if (files.length === 0) return;

        // Clear previous previews
        previewArea.innerHTML = '';

        for (const file of files) {
            if (this.validateImage(file)) {
                await this.processAndPreviewImage(file, previewArea, input);
            }
        }
    },

    // Validate image file
    validateImage(file) {
        // Check file type
        if (!this.config.allowedTypes.includes(file.type)) {
            this.showError('Please select a valid image file (JPEG, PNG, GIF, WebP)');
            return false;
        }

        // Check file size
        if (file.size > this.config.maxFileSize) {
            this.showError(`File size must not exceed ${this.formatFileSize(this.config.maxFileSize)}`);
            return false;
        }

        return true;
    },

    // Process and preview image
    async processAndPreviewImage(file, previewArea, input) {
        try {
            // Show loading state
            const loadingDiv = this.createLoadingPreview(file.name);
            previewArea.appendChild(loadingDiv);

            // Read file
            const imageData = await this.readFileAsDataURL(file);
            
            // Create image element to get dimensions
            const img = await this.createImageElement(imageData);
            
            // Check dimensions
            if (img.width > this.config.maxWidth || img.height > this.config.maxHeight) {
                // Resize image
                const resizedData = await this.resizeImage(img, this.config.maxWidth, this.config.maxHeight);
                this.createImagePreview(resizedData, file, previewArea, input, true);
            } else {
                this.createImagePreview(imageData, file, previewArea, input, false);
            }

            // Remove loading state
            loadingDiv.remove();

        } catch (error) {
            console.error('Error processing image:', error);
            this.showError('Error processing image. Please try again.');
            previewArea.innerHTML = '';
        }
    },

    // Create loading preview
    createLoadingPreview(fileName) {
        const div = document.createElement('div');
        div.className = 'image-preview loading';
        div.innerHTML = `
            <div class="preview-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Processing ${fileName}...</p>
            </div>
        `;
        return div;
    },

    // Read file as data URL
    readFileAsDataURL(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    },

    // Create image element from data URL
    createImageElement(dataURL) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = dataURL;
        });
    },

    // Resize image
    resizeImage(img, maxWidth, maxHeight) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // Calculate new dimensions
            let { width, height } = img;
            const ratio = Math.min(maxWidth / width, maxHeight / height);
            
            if (ratio < 1) {
                width *= ratio;
                height *= ratio;
            }

            canvas.width = width;
            canvas.height = height;

            // Draw resized image
            ctx.drawImage(img, 0, 0, width, height);

            // Convert to data URL
            const resizedDataURL = canvas.toDataURL('image/jpeg', this.config.quality);
            resolve(resizedDataURL);
        });
    },

    // Create image preview
    createImagePreview(dataURL, file, previewArea, input, wasResized) {
        const preview = document.createElement('div');
        preview.className = 'image-preview';

        const img = document.createElement('img');
        img.src = dataURL;
        img.alt = 'Preview';
        img.className = 'preview-image';

        const info = document.createElement('div');
        info.className = 'preview-info';
        info.innerHTML = `
            <div class="file-name">${file.name}</div>
            <div class="file-size">${this.formatFileSize(file.size)}</div>
            ${wasResized ? '<div class="resize-notice"><i class="fas fa-info-circle"></i> Image was resized</div>' : ''}
            <div class="preview-actions">
                <button type="button" class="btn btn-sm btn-outline-danger remove-image">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;

        preview.appendChild(img);
        preview.appendChild(info);
        previewArea.appendChild(preview);

        // Handle remove button
        const removeBtn = preview.querySelector('.remove-image');
        removeBtn.addEventListener('click', () => {
            preview.remove();
            input.value = '';
            this.showUploadZone(input);
        });

        // Hide upload zone when image is selected
        this.hideUploadZone(input);
    },

    // Setup drag and drop functionality
    setupDragAndDrop() {
        const uploadAreas = document.querySelectorAll('.image-upload-area');
        
        uploadAreas.forEach(area => {
            const input = area.querySelector('input[type="file"]');
            const uploadZone = area.querySelector('.upload-zone');
            
            if (input && uploadZone) {
                this.setupDragAndDropForZone(uploadZone, input);
            }
        });
    },

    // Setup drag and drop for specific zone
    setupDragAndDropForZone(zone, input) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            zone.addEventListener(eventName, () => {
                zone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            zone.addEventListener(eventName, () => {
                zone.classList.remove('drag-over');
            });
        });

        zone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                // Create a new FileList-like object
                const dt = new DataTransfer();
                Array.from(files).forEach(file => {
                    if (this.config.allowedTypes.includes(file.type)) {
                        dt.items.add(file);
                    }
                });
                input.files = dt.files;
                this.handleImageSelection(input);
            }
        });
    },

    // Setup image preview for existing images
    setupImagePreview() {
        const existingPreviews = document.querySelectorAll('.existing-image-preview');
        
        existingPreviews.forEach(preview => {
            const deleteBtn = preview.querySelector('.delete-existing-image');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.confirmDeleteExistingImage(preview, deleteBtn);
                });
            }
        });
    },

    // Confirm delete existing image
    confirmDeleteExistingImage(preview, deleteBtn) {
        const imageName = deleteBtn.dataset.imageName || 'this image';
        
        if (confirm(`Are you sure you want to delete ${imageName}?`)) {
            // Add hidden input to mark for deletion
            const form = preview.closest('form');
            if (form) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'delete_existing_image';
                hiddenInput.value = deleteBtn.dataset.imageId || deleteBtn.dataset.imagePath;
                form.appendChild(hiddenInput);
            }

            // Hide preview
            preview.style.display = 'none';
            
            // Show upload zone if it exists
            const uploadArea = preview.closest('.image-upload-area');
            if (uploadArea) {
                const input = uploadArea.querySelector('input[type="file"]');
                this.showUploadZone(input);
            }
        }
    },

    // Show upload zone
    showUploadZone(input) {
        const wrapper = input.closest('.image-upload-area');
        const uploadZone = wrapper.querySelector('.upload-zone');
        if (uploadZone) {
            uploadZone.style.display = 'block';
        }
    },

    // Hide upload zone
    hideUploadZone(input) {
        const wrapper = input.closest('.image-upload-area');
        const uploadZone = wrapper.querySelector('.upload-zone');
        if (uploadZone) {
            uploadZone.style.display = 'none';
        }
    },

    // Show error message
    showError(message) {
        if (window.Dashboard && window.Dashboard.showNotification) {
            window.Dashboard.showNotification(message, 'danger');
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
    },

    // Utility: Convert data URL to blob
    dataURLToBlob(dataURL) {
        const arr = dataURL.split(',');
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);
        
        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }
        
        return new Blob([u8arr], { type: mime });
    },

    // Utility: Create thumbnail
    createThumbnail(img, size = this.config.thumbnailSize) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        canvas.width = size;
        canvas.height = size;
        
        // Calculate crop dimensions for square thumbnail
        const minDimension = Math.min(img.width, img.height);
        const sx = (img.width - minDimension) / 2;
        const sy = (img.height - minDimension) / 2;
        
        ctx.drawImage(img, sx, sy, minDimension, minDimension, 0, 0, size, size);
        
        return canvas.toDataURL('image/jpeg', this.config.quality);
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    ImageUpload.init();
});

// Make ImageUpload available globally
window.ImageUpload = ImageUpload;

/**
 * Professional Table Actions Handler
 * For Virunga Homestay Admin Dashboard
 */

const TableActions = {
    // Configuration
    config: {
        apiBaseUrl: '/admin/backend/api/',
        itemsPerPage: 10,
        searchDelay: 300
    },

    // Initialize table functionality
    init() {
        this.setupTableSearch();
        this.setupTableSorting();
        this.setupTablePagination();
        this.setupBulkActions();
        this.setupRowActions();
        this.setupTableFilters();
        console.log('Table actions initialized');
    },

    // Setup table search functionality
    setupTableSearch() {
        const searchInputs = document.querySelectorAll('.table-search input');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(input.searchTimeout);
                input.searchTimeout = setTimeout(() => {
                    this.performSearch(input);
                }, this.config.searchDelay);
            });
        });
    },

    // Perform search
    performSearch(input) {
        const searchTerm = input.value.toLowerCase().trim();
        const table = input.closest('.table-container').querySelector('.data-table');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const shouldShow = searchTerm === '' || text.includes(searchTerm);
            row.style.display = shouldShow ? '' : 'none';
        });

        // Update pagination after search
        this.updatePaginationAfterFilter(table);
    },

    // Setup table sorting
    setupTableSorting() {
        const sortableHeaders = document.querySelectorAll('.data-table th.sortable');
        
        sortableHeaders.forEach(header => {
            header.addEventListener('click', () => {
                this.sortTable(header);
            });
        });
    },

    // Sort table
    sortTable(header) {
        const table = header.closest('.data-table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        
        // Determine sort direction
        const currentSort = header.classList.contains('sort-asc') ? 'asc' : 
                           header.classList.contains('sort-desc') ? 'desc' : 'none';
        
        const newSort = currentSort === 'asc' ? 'desc' : 'asc';

        // Clear all sort classes
        header.parentNode.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });

        // Add new sort class
        header.classList.add(`sort-${newSort}`);

        // Sort rows
        rows.sort((a, b) => {
            const aValue = this.getCellValue(a, columnIndex);
            const bValue = this.getCellValue(b, columnIndex);
            
            const comparison = this.compareValues(aValue, bValue);
            return newSort === 'asc' ? comparison : -comparison;
        });

        // Reorder rows in DOM
        rows.forEach(row => tbody.appendChild(row));
    },

    // Get cell value for sorting
    getCellValue(row, columnIndex) {
        const cell = row.children[columnIndex];
        if (!cell) return '';

        // Check for data-sort attribute first
        if (cell.hasAttribute('data-sort')) {
            return cell.getAttribute('data-sort');
        }

        // Get text content, excluding action buttons
        const actionButtons = cell.querySelector('.action-buttons');
        if (actionButtons) {
            const clone = cell.cloneNode(true);
            const cloneActions = clone.querySelector('.action-buttons');
            if (cloneActions) cloneActions.remove();
            return clone.textContent.trim();
        }

        return cell.textContent.trim();
    },

    // Compare values for sorting
    compareValues(a, b) {
        // Try to parse as numbers
        const numA = parseFloat(a);
        const numB = parseFloat(b);
        
        if (!isNaN(numA) && !isNaN(numB)) {
            return numA - numB;
        }

        // Try to parse as dates
        const dateA = new Date(a);
        const dateB = new Date(b);
        
        if (!isNaN(dateA.getTime()) && !isNaN(dateB.getTime())) {
            return dateA - dateB;
        }

        // String comparison
        return a.localeCompare(b);
    },

    // Setup table pagination
    setupTablePagination() {
        const paginationContainers = document.querySelectorAll('.table-pagination');
        
        paginationContainers.forEach(container => {
            const pageLinks = container.querySelectorAll('.page-link');
            pageLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (!link.closest('.page-item').classList.contains('disabled')) {
                        this.changePage(link);
                    }
                });
            });
        });
    },

    // Change page
    changePage(link) {
        const container = link.closest('.table-container');
        const table = container.querySelector('.data-table');
        const currentPage = parseInt(link.textContent) || 1;
        
        // This would typically make an AJAX request to load new data
        // For now, we'll just update the pagination display
        this.updatePaginationDisplay(container, currentPage);
    },

    // Update pagination display
    updatePaginationDisplay(container, currentPage) {
        const pagination = container.querySelector('.pagination');
        const pageItems = pagination.querySelectorAll('.page-item');
        
        pageItems.forEach(item => {
            item.classList.remove('active');
            const link = item.querySelector('.page-link');
            if (link && parseInt(link.textContent) === currentPage) {
                item.classList.add('active');
            }
        });
    },

    // Update pagination after filtering
    updatePaginationAfterFilter(table) {
        const visibleRows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
        const totalVisible = visibleRows.length;
        const container = table.closest('.table-container');
        const paginationInfo = container.querySelector('.pagination-info');
        
        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${totalVisible} entries`;
        }
    },

    // Setup bulk actions
    setupBulkActions() {
        const selectAllCheckboxes = document.querySelectorAll('.bulk-select-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        
        selectAllCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                this.toggleAllRows(e.target);
            });
        });

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateBulkActions();
            });
        });

        // Setup bulk action buttons
        const bulkActionBtns = document.querySelectorAll('.bulk-action-btn');
        bulkActionBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.performBulkAction(e.target);
            });
        });
    },

    // Toggle all rows
    toggleAllRows(selectAllCheckbox) {
        const table = selectAllCheckbox.closest('.table-container');
        const rowCheckboxes = table.querySelectorAll('.row-checkbox');
        
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });

        this.updateBulkActions();
    },

    // Update bulk actions display
    updateBulkActions() {
        const tables = document.querySelectorAll('.table-container');
        
        tables.forEach(table => {
            const selectedCheckboxes = table.querySelectorAll('.row-checkbox:checked');
            const bulkActions = table.querySelector('.bulk-actions');
            const selectedCount = table.querySelector('.selected-count');
            
            if (selectedCheckboxes.length > 0) {
                if (bulkActions) bulkActions.classList.add('show');
                if (selectedCount) selectedCount.textContent = `${selectedCheckboxes.length} selected`;
            } else {
                if (bulkActions) bulkActions.classList.remove('show');
            }
        });
    },

    // Perform bulk action
    performBulkAction(button) {
        const action = button.dataset.action;
        const table = button.closest('.table-container');
        const selectedCheckboxes = table.querySelectorAll('.row-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            this.showNotification('Please select items to perform this action.', 'warning');
            return;
        }

        if (action === 'delete') {
            this.confirmBulkDelete(selectedIds, table);
        } else {
            this.performBulkActionRequest(action, selectedIds, table);
        }
    },

    // Confirm bulk delete
    confirmBulkDelete(ids, table) {
        const count = ids.length;
        const itemType = table.dataset.itemType || 'items';
        
        if (confirm(`Are you sure you want to delete ${count} ${itemType}? This action cannot be undone.`)) {
            this.performBulkActionRequest('delete', ids, table);
        }
    },

    // Perform bulk action request
    async performBulkActionRequest(action, ids, table) {
        const endpoint = table.dataset.bulkEndpoint;
        if (!endpoint) {
            this.showNotification('Bulk action endpoint not configured.', 'danger');
            return;
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: action,
                    ids: ids
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                // Reload table or remove rows
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                this.showNotification(data.message, 'danger');
            }
        } catch (error) {
            console.error('Bulk action error:', error);
            this.showNotification('An error occurred. Please try again.', 'danger');
        }
    },

    // Setup row actions
    setupRowActions() {
        // Delete buttons
        const deleteButtons = document.querySelectorAll('.action-btn.delete, .delete-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.confirmRowDelete(btn);
            });
        });

        // Status toggle buttons
        const statusButtons = document.querySelectorAll('.status-toggle');
        statusButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleRowStatus(btn);
            });
        });
    },

    // Confirm row delete
    confirmRowDelete(button) {
        const itemName = button.dataset.itemName || 'this item';
        const deleteUrl = button.href || button.dataset.deleteUrl;

        this.showDeleteModal(itemName, deleteUrl, button);
    },

    // Show delete confirmation modal
    showDeleteModal(itemName, deleteUrl, button) {
        // Create modal if it doesn't exist
        let modal = document.getElementById('deleteConfirmModal');
        if (!modal) {
            modal = this.createDeleteModal();
            document.body.appendChild(modal);
        }

        // Update modal content
        const itemNameSpan = modal.querySelector('.delete-item-name');
        const confirmBtn = modal.querySelector('.confirm-delete-btn');

        if (itemNameSpan) {
            itemNameSpan.textContent = itemName;
        }

        // Set up confirm button
        confirmBtn.onclick = () => {
            this.hideDeleteModal();
            this.performRowDelete(deleteUrl, button);
        };

        // Show modal
        modal.classList.add('show');
    },

    // Create delete confirmation modal
    createDeleteModal() {
        const modal = document.createElement('div');
        modal.id = 'deleteConfirmModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content delete-modal">
                <div class="modal-header">
                    <div class="modal-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="modal-title">Confirm Deletion</h3>
                    <button type="button" class="modal-close" onclick="TableActions.hideDeleteModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="delete-warning">
                        <div class="warning-content">
                            <h4>Are you sure you want to delete this item?</h4>
                            <p class="item-name">
                                <strong class="delete-item-name">this item</strong>
                            </p>
                            <div class="warning-message">
                                <i class="fas fa-info-circle"></i>
                                This action cannot be undone. The item will be permanently removed from your system.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="TableActions.hideDeleteModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-danger confirm-delete-btn">
                        <i class="fas fa-trash-alt"></i> Delete Permanently
                    </button>
                </div>
            </div>
        `;

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                this.hideDeleteModal();
            }
        });

        return modal;
    },

    // Hide delete modal
    hideDeleteModal() {
        const modal = document.getElementById('deleteConfirmModal');
        if (modal) {
            modal.classList.remove('show');
        }
    },

    // Perform row delete
    async performRowDelete(url, button) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'delete' })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                
                // Remove row from table
                const row = button.closest('tr');
                if (row) {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        this.updatePaginationAfterFilter(button.closest('.data-table'));
                    }, 300);
                }
            } else {
                this.showNotification(data.message, 'danger');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showNotification('An error occurred while deleting.', 'danger');
        }
    },

    // Toggle row status
    async toggleRowStatus(button) {
        const statusUrl = button.dataset.statusUrl;
        const currentStatus = button.dataset.currentStatus;
        const newStatus = currentStatus === '1' ? '0' : '1';

        try {
            const response = await fetch(statusUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    action: 'toggle_status',
                    status: newStatus
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update button and status display
                button.dataset.currentStatus = newStatus;
                const statusBadge = button.closest('tr').querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = `status-badge ${newStatus === '1' ? 'active' : 'inactive'}`;
                    statusBadge.textContent = newStatus === '1' ? 'Active' : 'Inactive';
                }
                
                this.showNotification(data.message, 'success');
            } else {
                this.showNotification(data.message, 'danger');
            }
        } catch (error) {
            console.error('Status toggle error:', error);
            this.showNotification('An error occurred while updating status.', 'danger');
        }
    },

    // Setup table filters
    setupTableFilters() {
        const filterSelects = document.querySelectorAll('.filter-select');
        
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyTableFilter(select);
            });
        });
    },

    // Apply table filter
    applyTableFilter(select) {
        const filterValue = select.value.toLowerCase();
        const filterColumn = select.dataset.filterColumn;
        const table = select.closest('.table-container').querySelector('.data-table');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (filterValue === '' || filterValue === 'all') {
                row.style.display = '';
            } else {
                const cell = row.children[filterColumn];
                const cellText = cell ? cell.textContent.toLowerCase() : '';
                row.style.display = cellText.includes(filterValue) ? '' : 'none';
            }
        });

        // Update pagination after filter
        this.updatePaginationAfterFilter(table);
    },

    // Show notification
    showNotification(message, type = 'info') {
        if (window.Dashboard && window.Dashboard.showNotification) {
            window.Dashboard.showNotification(message, type);
        } else {
            this.showToast(message, type);
        }
    },

    // Show toast notification
    showToast(message, type = 'info') {
        // Create toast container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        // Set icon based on type
        let icon = 'fas fa-info-circle';
        if (type === 'success') icon = 'fas fa-check-circle';
        else if (type === 'danger' || type === 'error') icon = 'fas fa-exclamation-circle';
        else if (type === 'warning') icon = 'fas fa-exclamation-triangle';

        toast.innerHTML = `
            <div class="toast-icon">
                <i class="${icon}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Add toast to container
        container.appendChild(toast);

        // Auto remove after 4 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'toastSlideOut 0.3s ease forwards';
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }
        }, 4000);
    },

    // Utility: Get selected row IDs
    getSelectedRowIds(table) {
        const selectedCheckboxes = table.querySelectorAll('.row-checkbox:checked');
        return Array.from(selectedCheckboxes).map(cb => cb.value);
    },

    // Utility: Clear all selections
    clearAllSelections(table) {
        const checkboxes = table.querySelectorAll('.row-checkbox, .bulk-select-all');
        checkboxes.forEach(cb => cb.checked = false);
        this.updateBulkActions();
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    TableActions.init();
});

// Make TableActions available globally
window.TableActions = TableActions;

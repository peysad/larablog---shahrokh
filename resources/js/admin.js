// Admin bulk actions
window.toggleBulkActions = function() {
    const form = document.getElementById('bulk-action-form');
    form.classList.toggle('d-none');
    // Update count when toggling
    if (!form.classList.contains('d-none')) {
        updateBulkCount();
    }
};

window.setBulkAction = function(action) {
    const checked = document.querySelectorAll('.post-checkbox:checked');
    
    if (checked.length === 0) {
        alert('Please select at least one item to perform bulk action.');
        return;
    }
    
    document.getElementById('bulk-action-value').value = action;
    const confirmMsg = `Are you sure you want to ${action} ${checked.length} selected item(s)?`;
    if (confirm(confirmMsg)) {
        document.getElementById('bulk-action-form').submit();
    }
};

window.toggleSelectAll = function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.post-checkbox'); // Fixed: was item-checkbox
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkCount();
};

// Updated function to match the template
function updateBulkCount() {
    const checked = document.querySelectorAll('.post-checkbox:checked');
    const countSpan = document.getElementById('bulk-selected-count');
    const selectAll = document.getElementById('select-all');
    
    if (countSpan) {
        countSpan.textContent = checked.length + ' items selected';
    }
    
    // Update select-all checkbox state
    const totalCheckboxes = document.querySelectorAll('.post-checkbox').length;
    selectAll.checked = checked.length === totalCheckboxes && totalCheckboxes > 0;
    selectAll.indeterminate = checked.length > 0 && checked.length < totalCheckboxes;
    
    // Update bulk action form with selected IDs
    const bulkForm = document.getElementById('bulk-action-form');
    // Remove existing hidden inputs
    const existingInputs = bulkForm.querySelectorAll('input[name="ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    // Add current selected IDs as hidden inputs
    checked.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'ids[]';
        hiddenInput.value = checkbox.value;
        bulkForm.appendChild(hiddenInput);
    });
}

// Admin search debounce
document.querySelectorAll('input[name="search"]').forEach(input => {
    let timeout;
    input.addEventListener('input', function(e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            // Submit parent form
            this.form.submit();
        }, 500);
    });
});

// Admin confirm delete
document.querySelectorAll('.delete-confirm').forEach(button => {
    button.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
});

// Admin tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Sidebar Responsive Logic
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const body = document.body;

    // Function to toggle sidebar
    function toggleSidebar() {
        body.classList.toggle('sidebar-open');
    }

    // Function to close sidebar
    function closeSidebar() {
        body.classList.remove('sidebar-open');
    }

    // 1. Toggle Sidebar Button Click (Hamburger)
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }

    // 2. Close Sidebar when clicking on Overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // 3. Close Sidebar when clicking the X Button inside sidebar
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }

    // 4. Close Sidebar when screen resizes to desktop (lg breakpoint)
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });
    
    // 5. Optional: Close sidebar when clicking a link inside it (for mobile UX)
    const sidebarLinks = document.querySelectorAll('.admin-sidebar .nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });
});
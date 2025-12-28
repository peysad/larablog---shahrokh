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
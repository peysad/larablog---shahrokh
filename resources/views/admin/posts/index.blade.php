@extends('layouts.admin')

@section('title', 'Manage Posts')

@section('page-title', 'Posts Management')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Post
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
            <i class="bi bi-list-check"></i> Bulk Actions
        </button>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            height: 65vh;
            overflow-y: scroll;
        }
        /* Style for Trashed Rows */
        tr.table-light td {
            text-decoration: line-through;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
<div class="card admin-card">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" action="{{ route('admin.posts.index') }}" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>
                        Published
                    </option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>
                    <option value="trashed" {{ request('status') === 'trashed' ? 'selected' : '' }}>
                        Trashed
                    </option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="author" class="form-select" onchange="this.form.submit()">
                    <option value="">All Authors</option>
                    @foreach($authors as $id => $name)
                        <option value="{{ $id }}" {{ request('author') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="search" name="search" class="form-control" 
                       placeholder="Search posts..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>

        <!-- Hidden Form for Bulk Actions -->
        <form id="bulk-action-form" method="POST" action="{{ route('admin.posts.bulk-action') }}" style="display: none;">
            @csrf
            <input type="hidden" name="action" id="bulk-action-value">
        </form>

        <!-- Posts Table -->
        <div class="table-responsive">
            <table class="table admin-table mb-0" id="posts-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" onclick="toggleSelectAll()">
                        </th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Categories</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Comments</th>
                        <th>Created</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr class="{{ $post->trashed() ? 'table-light' : '' }}">
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $post->id }}" 
                                       class="post-checkbox">
                            </td>
                            <td class="fw-bold">
                                <a href="{{ route('posts.show', $post) }}" target="_blank">
                                    {{ Str::limit($post->title, 50) }}
                                </a>
                                @if($post->featured_image)
                                    <i class="bi bi-image text-success ms-1" title="Has image"></i>
                                @endif
                            </td>
                            <td>
                                @if($post->author)
                                    <a href="{{ route('author.show', $post->author) }}">
                                        {{ $post->author->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                @forelse($post->categories->take(2) as $category)
                                    <span class="badge bg-light text-dark">{{ $category->name }}</span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                                @if($post->categories->count() > 2)
                                    <small class="text-muted">+{{ $post->categories->count() - 2 }}</small>
                                @endif
                            </td>
                            <td>
                                @if($post->trashed())
                                    <span class="badge bg-danger">Trashed</span>
                                @else
                                    <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ number_format($post->views) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $post->comments()->count() }}</span>
                            </td>
                            <td>{{ $post->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if($post->trashed())
                                    <!-- Actions for Trashed Posts (Matched with Users style) -->
                                    <div class="btn-group" role="group">
                                        <!-- Restore Button (Direct Form, No Confirm) -->
                                        <form action="{{ route('admin.posts.restore', $post->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    title="Restore Post">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>

                                        <!-- Force Delete Button (Modal Trigger) -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#forceDeleteModal{{ $post->id }}">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>

                                    <!-- Force Delete Modal -->
                                    <div class="modal fade" id="forceDeleteModal{{ $post->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.posts.forceDelete', $post->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Permanent Delete</h5>
                                                        <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-danger"><strong>Warning:</strong> This will permanently delete <strong>{{ $post->title }}</strong> and all data. This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Forever</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <!-- Actions for Active Posts -->
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('posts.edit', $post) }}" 
                                           class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('posts.show', $post) }}" 
                                           class="btn btn-outline-success" title="View" target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <!-- Delete Button (Modal Trigger) -->
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $post->id }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal (Soft Delete) -->
                                    <div class="modal fade" id="deleteModal{{ $post->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Move to Trash</h5>
                                                        <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>This will delete <strong>{{ $post->title }}</strong>. It can be restored later.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">Move to Trash</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox display-4 text-muted"></i>
                                <h5 class="mt-2">No posts found</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Bulk Action Modal (Styled exactly like Force Delete Modal) -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="bulkActionModalLabel">
                    <i class="bi bi-list-check"></i> Bulk Action
                </h5>
                <button type="button" class="btn-close btn-close-white" style="margin-right: 0;" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border">
                    <p class="mb-0">Are you sure you want to perform a bulk action on selected items?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="executeBulkAction('publish')">Publish</button>
                <button type="button" class="btn btn-secondary" onclick="executeBulkAction('draft')">Move to Draft</button>
                <button type="button" class="btn btn-danger" onclick="executeBulkAction('delete')">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.post-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
        // Trigger change event to update bulk action form inputs
        cb.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

function updateBulkCount() {
    const checked = document.querySelectorAll('.post-checkbox:checked');
    const selectAll = document.getElementById('select-all');
    
    // Update select-all checkbox state
    const totalCheckboxes = document.querySelectorAll('.post-checkbox').length;
    selectAll.checked = checked.length === totalCheckboxes && totalCheckboxes > 0;
    selectAll.indeterminate = checked.length > 0 && checked.length < totalCheckboxes;
    
    // Update bulk action form with selected IDs
    updateBulkActionFormInputs(checked);
}

function updateBulkActionFormInputs(checkedCheckboxes) {
    const bulkForm = document.getElementById('bulk-action-form');
    // Remove existing hidden inputs
    const existingInputs = bulkForm.querySelectorAll('input[name="ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    // Add current selected IDs as hidden inputs
    checkedCheckboxes.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'ids[]';
        hiddenInput.value = checkbox.value;
        bulkForm.appendChild(hiddenInput);
    });
}

// Function to handle Bulk Action execution via Modal
function executeBulkAction(action) {
    const checked = document.querySelectorAll('.post-checkbox:checked');
    
    if (checked.length === 0) {
        alert('Please select at least one post to perform bulk action.');
        return;
    }
    
    document.getElementById('bulk-action-value').value = action;
    
    // Update hidden inputs for IDs
    updateBulkActionFormInputs(checked);
    
    // Hide Modal
    const modalEl = document.getElementById('bulkActionModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
        modalInstance.hide();
    }
    
    // Submit hidden form
    document.getElementById('bulk-action-form').submit();
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to checkboxes
    document.querySelectorAll('.post-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkCount();
            
            // Also update bulk action form inputs when individual checkboxes change
            const checked = document.querySelectorAll('.post-checkbox:checked');
            updateBulkActionFormInputs(checked);
        });
    });
    
    // Add change event to select-all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        toggleSelectAll();
    });
});
</script>
@endpush
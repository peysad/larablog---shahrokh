@extends('layouts.admin')

@section('title', 'Manage Posts')

@section('page-title', 'Posts Management')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('posts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Post
        </a>
        <button type="button" class="btn btn-outline-secondary" onclick="toggleBulkActions()">
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

        <!-- Bulk Actions Form -->
        <form id="bulk-action-form" method="POST" action="{{ route('admin.posts.bulk-action') }}" class="d-none">
            @csrf
            <input type="hidden" name="action" id="bulk-action-value">
            <div class="alert alert-warning mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span id="bulk-selected-count">0 items selected</span>
                    <div>
                        <button type="button" class="btn btn-sm btn-success" 
                                onclick="setBulkAction('publish')">Publish</button>
                        <button type="button" class="btn btn-sm btn-secondary" 
                                onclick="setBulkAction('draft')">Move to Draft</button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="setBulkAction('delete')">Delete</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                onclick="toggleBulkActions()">Cancel</button>
                    </div>
                </div>
            </div>
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
                        <tr>
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
                                <a href="{{ route('author.show', $post->author) }}">
                                    {{ $post->author->name }}
                                </a>
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
                                <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($post->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($post->views) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $post->comments()->count() }}</span>
                            </td>
                            <td>{{ $post->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('posts.edit', $post) }}" 
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('posts.show', $post) }}" 
                                       class="btn btn-outline-success" title="View" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
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
@endsection

@push('scripts')
<script>
let bulkActionsVisible = false;

function toggleBulkActions() {
    bulkActionsVisible = !bulkActionsVisible;
    const form = document.getElementById('bulk-action-form');
    form.classList.toggle('d-none', !bulkActionsVisible);
    updateBulkCount(); // Update count when toggling
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.post-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
        // Trigger change event to update the bulk action form inputs
        cb.dispatchEvent(new Event('change', { bubbles: true }));
    });
    updateBulkCount();
}

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

function setBulkAction(action) {
    const checked = document.querySelectorAll('.post-checkbox:checked');
    
    if (checked.length === 0) {
        alert('Please select at least one post to perform bulk action.');
        return;
    }
    
    document.getElementById('bulk-action-value').value = action;
    const confirmMsg = `Are you sure you want to ${action} ${checked.length} selected post(s)?`;
    
    if (confirm(confirmMsg)) {
        document.getElementById('bulk-action-form').submit();
    }
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to checkboxes
    document.querySelectorAll('.post-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkCount();
            
            // Also update the bulk action form inputs when individual checkboxes change
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
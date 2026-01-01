@extends('layouts.admin')

@section('title', 'Manage Tags')

@push('styles')
    <style>
        .card {
            height: 60vh;
            overflow-y: scroll;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-tags"></i> Tags Management
            </h3>
            @can('create', \App\Models\Tag::class)
                <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Tag
                </a>
            @endcan
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if($tags->count())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Posts</th>
                                    <th scope="col">Created</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tags as $tag)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $tag->name }}</strong>
                                            <br>
                                            <span class="text-muted">
                                                <i class="bi bi-tag"></i> #{{ $tag->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $tag->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $tag->posts_count }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $tag->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                @can('update', $tag)
                                                    <a href="{{ route('admin.tags.edit', $tag) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete', $tag)
                                                    <!-- Delete Button (Modal Trigger) -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $tag->id }}"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            <!-- Delete Modal (Force Delete Style - Red Header) -->
                                            <div class="modal fade" id="deleteModal{{ $tag->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">Permanent Delete</h5>
                                                                <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-danger"><strong>Warning:</strong> This will permanently delete <strong>{{ $tag->name }}</strong> and all associated posts. This action cannot be undone.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Delete Forever</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-tag-x display-1 text-muted"></i>
                        <h4 class="mt-3">No tags found</h4>
                        <p class="text-muted">Create your first tag to organize your content.</p>
                        @can('create', \App\Models\Tag::class)
                            <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Tag
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $tags->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
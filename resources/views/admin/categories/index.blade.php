@extends('layouts.admin')

@section('title', 'Manage Categories')
<style>
    .card {
        height: 60vh;
        overflow-y: scroll;
    }
</style>
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-folder"></i> Categories Management
            </h3>
            @can('create', \App\Models\Category::class)
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Category
                </a>
            @endcan
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if($categories->count())
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
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $category->slug }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $category->posts_count }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $category->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="text-end">
                                            @can('update', $category)
                                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $category)
                                                <form action="{{ route('admin.categories.destroy', $category) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-folder-x display-1 text-muted"></i>
                        <h4 class="mt-3">No categories found</h4>
                        <p class="text-muted">Create your first category to get started.</p>
                        @can('create', \App\Models\Category::class)
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Category
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
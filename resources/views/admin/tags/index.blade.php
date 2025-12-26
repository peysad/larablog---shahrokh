@extends('layouts.app')

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
                                            @can('update', $tag)
                                                <a href="{{ route('admin.tags.edit', $tag) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $tag)
                                                <form action="{{ route('admin.tags.destroy', $tag) }}" 
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
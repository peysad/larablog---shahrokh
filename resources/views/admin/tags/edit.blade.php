@extends('layouts.app')

@section('title', 'Edit Tag: ' . $tag->name)
@push('styles')
<style>
    .card-body {
        height: 70vh;
    }

</style>
@endpush
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Tag
                    </h4>
                    <span class="badge bg-secondary">
                        {{ $tag->posts_count }} posts
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Tag Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name', $tag->name) }}" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Use lowercase, relevant keywords
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-bold">
                            URL Slug <small class="text-muted">(auto-generated)</small>
                        </label>
                        <input type="text" id="slug" name="slug" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug', $tag->slug) }}">
                        <div class="form-text">
                            <i class="bi bi-link-45deg"></i> /tags/<span id="slug-preview">{{ $tag->slug }}</span>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between pt-4 border-top">
                        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Tag
                        </button>
                    </div>
                </form>

                <!-- Danger Zone -->
                @can('delete', $tag)
                    <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" 
                          class="mt-4 p-3 border rounded bg-danger bg-opacity-10">
                        @csrf
                        @method('DELETE')
                        <h6 class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Danger Zone
                        </h6>
                        <p class="text-muted small mb-2">
                            Deleting this tag will remove it from all associated posts.
                        </p>
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this tag?')">
                            <i class="bi bi-trash"></i> Delete Tag
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
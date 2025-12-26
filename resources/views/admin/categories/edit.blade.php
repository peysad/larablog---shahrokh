@extends('layouts.app')

@section('title', 'Edit Category: ' . $category->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square"></i> Edit Category
                    </h4>
                    <span class="badge bg-primary">
                        {{ $category->posts_count }} posts
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-bold">
                            URL Slug <small class="text-muted">(auto-generated if empty)</small>
                        </label>
                        <input type="text" id="slug" name="slug" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug', $category->slug) }}">
                        <div class="form-text">
                            <i class="bi bi-link-45deg"></i> URL: /categories/<span id="slug-preview">{{ $category->slug }}</span>
                        </div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            Description <small class="text-muted">(optional)</small>
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                        <div class="form-text"><span id="desc-count">{{ strlen($category->description) }}</span>/1000 characters</div>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between pt-4 border-top">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Category
                        </button>
                    </div>
                </form>

                <!-- Danger Zone -->
                @can('delete', $category)
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                          class="mt-4 p-3 border rounded bg-danger bg-opacity-10">
                        @csrf
                        @method('DELETE')
                        <h6 class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Danger Zone
                        </h6>
                        <p class="text-muted small mb-2">
                            Deleting this category will remove it from all associated posts.
                        </p>
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this category?')">
                            <i class="bi bi-trash"></i> Delete Category
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
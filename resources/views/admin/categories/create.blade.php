@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-folder-plus"></i> Create New Category
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., Technology, Travel, Lifestyle" required autofocus>
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
                               value="{{ old('slug') }}" 
                               placeholder="technology">
                        <div class="form-text">
                            <i class="bi bi-link-45deg"></i> URL: /categories/<span id="slug-preview">your-slug</span>
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
                                  class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Brief description of this category...">{{ old('description') }}</textarea>
                        <div class="form-text"><span id="desc-count">0</span>/1000 characters</div>
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
                            <i class="bi bi-save"></i> Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
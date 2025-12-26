@extends('layouts.app')

@section('title', 'Create Tag')
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
                <h4 class="mb-0 fw-bold">
                    <i class="bi bi-tag-plus"></i> Create New Tag
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.tags.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Tag Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="form-control form-control-lg @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="e.g., laravel, php, javascript" required autofocus>
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
                               value="{{ old('slug') }}" 
                               placeholder="laravel">
                        <div class="form-text">
                            <i class="bi bi-link-45deg"></i> /tags/<span id="slug-preview">your-slug</span>
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
                            <i class="bi bi-save"></i> Create Tag
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">
            <i class="bi bi-shield-check"></i> Admin Dashboard
        </h2>
        <p class="text-muted">Manage your blog content and settings</p>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="card text-white h-100" style="background-color: var(--primary);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Posts</h6>
                        <h2 class="mb-0 fw-bold">{{ \App\Models\Post::count() }}</h2>
                    </div>
                    <i class="bi bi-file-text fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white h-100" style="background-color: var(--secondary);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Categories</h6>
                        <h2 class="mb-0 fw-bold">{{ \App\Models\Category::count() }}</h2>
                    </div>
                    <i class="bi bi-folder fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.categories.index') }}" class="text-white text-decoration-none small">
                    Manage <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white h-100" style="background-color: var(--accent); color: var(--text-main) !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Tags</h6>
                        <h2 class="mb-0 fw-bold">{{ \App\Models\Tag::count() }}</h2>
                    </div>
                    <i class="bi bi-tags fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.tags.index') }}" class="text-decoration-none small" style="color: var(--text-main);">
                    Manage <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white h-100" style="background-color: var(--sidebar);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Users</h6>
                        <h2 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <span class="text-white-50 small">Manage users</span>
            </div>
        </div>
    </div>
</div>
@endsection
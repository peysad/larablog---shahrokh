@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                             class="rounded-circle" width="80" height="80" 
                             style="object-fit: cover; border: 3px solid var(--primary);">
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h1 class="h3 mb-1 fw-bold">Welcome, {{ auth()->user()->name }}!</h1>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope"></i> {{ auth()->user()->email }}
                        </p>
                        <p class="mb-0">
                            <span class="badge bg-primary">
                                <i class="bi bi-person-badge"></i> 
                                {{ auth()->user()->roles->pluck('name')->join(', ') }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Statistics Cards -->
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card text-white h-100" style="background-color: var(--primary);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">My Posts</h6>
                                        <h2 class="mb-0 fw-bold">{{ $stats['posts_count'] }}</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-file-text fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="#" class="text-white text-decoration-none small">
                                    View All Posts <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-white h-100" style="background-color: var(--secondary);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Published</h6>
                                        <h2 class="mb-0 fw-bold">{{ $stats['published_posts'] }}</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-check-circle fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <span class="text-white-50 small">Live on the blog</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-white h-100" style="background-color: var(--accent); color: var(--text-main) !important;">
                            <div class="card-body" style="color: var(--text-main);">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Drafts</h6>
                                        <h2 class="mb-0 fw-bold">{{ $stats['draft_posts'] }}</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-pencil-square fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <span class="small" style="color: var(--text-main);">Work in progress</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-white h-100" style="background-color: var(--sidebar);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-1">Member Since</h6>
                                        <h5 class="mb-0 fw-bold">{{ auth()->user()->created_at->diffForHumans() }}</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-calendar-check fs-1 opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <span class="text-white-50 small">{{ auth()->user()->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-lightning"></i> Quick Actions
                        </h5>
                        <div class="btn-group flex-wrap gap-2">
                            @can('create posts')
                                <a href="#" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Write New Post
                                </a>
                            @endcan
                            
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="bi bi-person"></i> Edit Profile
                            </a>

                            @can('view-admin-panel')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning text-dark">
                                    <i class="bi bi-shield-check"></i> Admin Panel
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Admin Notice -->
                @can('view-admin-panel')
                    <div class="alert alert-warning mt-5" role="alert">
                        <h6 class="alert-heading">
                            <i class="bi bi-shield-exclamation"></i> Administrative Access
                        </h6>
                        <p class="mb-0">You have administrative privileges. Please use them responsibly.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
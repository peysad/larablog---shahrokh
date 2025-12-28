<!-- About Widget -->
<div class="sidebar-card about-widget">
    <div class="sidebar-card-header">
        <i class="bi bi-info-circle"></i> About LaraBlog
    </div>
    <div class="sidebar-card-body">
        <p class="mb-0">
            A modern blogging platform built with Laravel 12, featuring role-based access,
            flat thread comments, and image management.
        </p>
    </div>
</div>

<!-- Admin Quick Access -->
@can('view-admin-panel')
    <div class="sidebar-card admin-quick-access">
        <div class="sidebar-card-header">
            <i class="bi bi-shield-check"></i> Admin Quick Access
        </div>
        <div class="list-group list-group-flush">
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action animated-list-item">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action animated-list-item">
                <i class="bi bi-folder"></i> Manage Categories
                <span class="badge bg-primary rounded-pill float-start">{{ \App\Models\Category::count() }}</span>
            </a>
            <a href="{{ route('admin.tags.index') }}" class="list-group-item list-group-item-action animated-list-item">
                <i class="bi bi-tags"></i> Manage Tags
                <span class="badge bg-secondary rounded-pill float-start">{{ \App\Models\Tag::count() }}</span>
            </a>
        </div>
    </div>
@endcan

<!-- Pending Comments Alert -->
@can('approve', \App\Models\Comment::class)
    @php $pendingComments = \App\Models\Comment::where('approved', false)->count(); @endphp
    @if($pendingComments > 0)
        <div class="pending-comments-alert">
            <i class="bi bi-exclamation-triangle"></i>
            <a href="{{ route('admin.comments.pending') }}" class="text-decoration-none">
                {{ $pendingComments }} comments pending approval
            </a>
        </div>
    @endif
@endcan

<!-- Categories Widget -->
@php
    $sidebarCategories = \App\Models\Category::withCount('posts')->get();
@endphp
@if($sidebarCategories->count())
    <div class="sidebar-card categories-widget">
        <div class="sidebar-card-header">
            <i class="bi bi-folder"></i> Categories
        </div>
        <div class="list-group list-group-flush">
            @foreach($sidebarCategories as $category)
                <a href="{{ route('categories.show', $category->slug) }}" class="list-group-item list-group-item-action animated-list-item d-flex justify-content-between">
                    {{ $category->name }}
                    <span class="badge bg-primary rounded-pill">{{ $category->posts_count }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Tags Widget -->
@php
    $sidebarTags = \App\Models\Tag::withCount('posts')->get();
@endphp
@if($sidebarTags->count())
    <div class="sidebar-card tags-widget">
        <div class="sidebar-card-header">
            <i class="bi bi-tags"></i> Popular Tags
        </div>
        <div class="sidebar-card-body">
            <div class="d-flex flex-wrap gap-2">
                @foreach($sidebarTags as $tag)
                    <a href="{{ route('tags.show', $tag->slug) }}" class="enhanced-badge bg-light text-dark text-decoration-none">
                        #{{ $tag->name }} ({{ $tag->posts_count }})
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Stats Widget -->
<div class="sidebar-card stats-widget">
    <div class="sidebar-card-header">
        <i class="bi bi-graph-up"></i> Blog Stats
    </div>
    <div class="sidebar-card-body">
        <div class="d-flex justify-content-between mb-2">
            <span>Total Posts:</span>
            <strong>{{ \App\Models\Post::published()->count() }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span>Categories:</span>
            <strong>{{ \App\Models\Category::count() }}</strong>
        </div>
        <div class="d-flex justify-content-between">
            <span>Tags:</span>
            <strong>{{ \App\Models\Tag::count() }}</strong>
        </div>
    </div>
</div>
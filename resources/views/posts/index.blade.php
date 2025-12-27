@extends('layouts.app')

@section('title', 'Blog Posts')

@push('styles')
<style>
    /* ----------------------------------------------------
       Custom Modern Blog Styles
       ---------------------------------------------------- */
    
    /* Container Layout */
    .posts-grid-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .posts-grid-container {
            grid-template-columns: repeat(2, 1fr); /* 2 columns on tablet/desktop */
        }
    }

    /* Card Structure */
    .modern-post-card {
        background: #fff;
        border-radius: 1rem;
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .modern-post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }

    /* Image Area */
    .card-image-link {
        display: block;
        width: 100%;
        position: relative;
    }

    .img-wrapper {
        width: 100%;
        aspect-ratio: 16/9; /* Enforce 16:9 ratio */
        overflow: hidden;
        background-color: #f1f3f5;
    }

    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .modern-post-card:hover .card-img {
        transform: scale(1.08);
    }

    /* Floating Category Badge */
    .category-badge.floating {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        color: #fff;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 10;
    }

    /* Content Styling */
    .card-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .post-meta-top {
        font-size: 0.85rem;
        color: #868e96;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        font-weight: 500;
    }

    .post-meta-top .author i {
        color: var(--primary); /* Use your primary color */
        margin-right: 4px;
    }

    .post-meta-top .separator {
        margin: 0 8px;
        color: #dee2e6;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }

    .card-title a {
        transition: color 0.2s;
    }

    .modern-post-card:hover .card-title a {
        color: var(--primary);
    }

    .card-excerpt {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    /* Card Footer */
    .card-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #f1f3f5;
        padding-top: 1rem;
    }

    .tags-wrapper {
        display: flex;
        gap: 8px;
    }

    .tag-pill {
        background-color: #f8f9fa;
        color: #495057;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .action-area {
        display: flex;
        align-items: center;
    }

    .views-count {
        font-size: 0.85rem;
        color: #adb5bd;
    }

    .btn-delete {
        border: none;
        background: transparent;
        color: #fa5252;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .modern-post-card:hover .btn-delete {
        opacity: 1;
    }

    /* Empty State Style */
    .empty-state-card {
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 2px dashed #dee2e6;
        grid-column: 1 / -1;
    }
    
    /* Sidebar Wrapper */
    .sidebar-wrapper {
        position: sticky;
        top: 100px; /* Sticky sidebar */
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Left Column: Posts Grid -->
        <div class="col-lg-8">
            <!-- Create Button -->
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAuthor()))
                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('posts.create') }}" class="btn btn-primary btn-lg shadow-sm px-4">
                        <i class="bi bi-plus-lg me-2"></i> New Post
                    </a>
                </div>
            @endif

            <!-- Posts Grid Container -->
            <div class="posts-grid-container">
                @forelse($posts as $post)
                    @include('partials._post_card', ['post' => $post])
                @empty
                    <div class="empty-state-card">
                        <i class="bi bi-inbox display-1 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold">No posts found</h4>
                        <p class="text-muted">There are currently no posts to display.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-wrapper">
                @include('partials._sidebar')
            </div>
        </div>
    </div>
</div>
@endsection
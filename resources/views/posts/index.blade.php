@extends('layouts.app')

@section('title', 'Blog Posts')
@push('styles')
<style>
    .col-lg-8 {
        overflow-y: scroll;
        height: 70rem;
    }
</style>
@endpush
@section('content')
<div class="container-fluid py-4">
    <!-- Search Bar -->
    <div class="search-container mb-4">
        <form class="search-form" action="{{ route('posts.index') }}" method="GET">
            <div class="input-group input-group-lg">
                <input type="search" class="form-control search-input" name="search" 
                       placeholder="Search posts..." aria-label="Search" value="{{ request('search') }}">
                <button class="btn btn-primary search-button" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <!-- Left Column: Posts Grid -->
        <div class="col-lg-8">

            <!-- Posts Grid Container -->
            <div class="posts-grid-container">
                @forelse($posts as $post)
                    @include('partials._post_card', ['post' => $post])
                @empty
                    <div class="empty-state-card">
                        <i class="bi bi-inbox display-1 text-muted mb-3 d-block"></i>
                        <h4 class="fw-bold">No posts found</h4>
                        <p class="text-muted">There are currently no posts to display.</p>
                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAuthor()))
                            <a href="{{ route('posts.create') }}" class="btn btn-primary mt-3">
                                <i class="bi bi-plus-lg me-2"></i> Create First Post
                            </a>
                        @endif
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
            <div class="sidebar-wrapper" id="sidebarWrapper">
                @include('partials._sidebar')
            </div>
        </div>
    </div>

    <!-- Sidebar Toggle Button (for mobile) -->
    <button class="sidebar-toggle-button collapsed" id="sidebarToggle">
        <i class="bi bi-chevron-right"></i>
    </button>
</div>

<!-- JavaScript for Sidebar Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarWrapper');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            // Toggle sidebar visibility
            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('show');
                this.classList.toggle('collapsed');
                
                // Change icon based on state
                const icon = this.querySelector('i');
                if (sidebar.classList.contains('show')) {
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-left');
                } else {
                    icon.classList.remove('bi-chevron-left');
                    icon.classList.add('bi-chevron-right');
                }
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                sidebarToggle.classList.add('collapsed');
                const icon = sidebarToggle.querySelector('i');
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            }
        });
    }
});
</script>
@endsection
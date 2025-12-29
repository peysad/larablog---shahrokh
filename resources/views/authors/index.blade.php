@extends('layouts.app')

@section('title', 'Our Authors')
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
    <!-- Search Bar (Identical to posts index) -->
    <div class="search-container mb-4">
        <form class="search-form" action="{{ route('authors.index') }}" method="GET">
            <div class="input-group input-group-lg">
                <input type="search" class="form-control search-input" name="search" 
                       placeholder="Search authors..." aria-label="Search" value="{{ request('search') }}">
                <button class="btn btn-primary search-button" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="row">
        <!-- Left Column: Authors Grid -->
        <div class="col-lg-8">

            <!-- Authors Grid Container -->
            <div class="row g-4">
                @forelse($authors as $author)
                    <div class="col-md-6 col-lg-6">
                        <div class="author-card card h-100">
                            <div class="card-body text-center p-4">
                                <!-- Avatar -->
                                <div class="author-avatar-wrapper mb-3">
                                    <img src="{{ $author->avatar_url }}" 
                                         alt="{{ $author->name }}" 
                                         class="author-avatar rounded-circle shadow">
                                </div>

                                <!-- Name & Role -->
                                <h4 class="author-name fw-bold mb-2">{{ $author->name }}</h4>
                                
                                <div class="author-roles mb-3">
                                    @foreach($author->roles as $role)
                                        @if($role->name === 'Admin')
                                            <span class="badge bg-danger">{{ $role->name }}</span>
                                        @elseif($role->name === 'Editor')
                                            <span class="badge bg-warning text-dark">{{ $role->name }}</span>
                                        @else
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Bio (Short) -->
                                @if($author->bio)
                                    <p class="author-bio text-muted small mb-3">
                                        {{ Str::limit(strip_tags($author->bio), 80) }}
                                    </p>
                                @else
                                    <p class="author-bio text-muted small mb-3">
                                        Content Creator on LaraBlog.
                                    </p>
                                @endif

                                <!-- Social Links -->
                                @if(!empty($author->social_links) && is_array($author->social_links))
                                    <div class="author-social-links mb-3 d-flex justify-content-center gap-2">
                                        @if(isset($author->social_links['github']))
                                            <a href="{{ $author->social_links['github'] }}" target="_blank" class="social-link-icon text-dark" title="GitHub">
                                                <i class="bi bi-github fs-5"></i>
                                            </a>
                                        @endif
                                        @if(isset($author->social_links['linkedin']))
                                            <a href="{{ $author->social_links['linkedin'] }}" target="_blank" class="social-link-icon text-primary" title="LinkedIn">
                                                <i class="bi bi-linkedin fs-5"></i>
                                            </a>
                                        @endif
                                        @if(isset($author->social_links['twitter']))
                                            <a href="{{ $author->social_links['twitter'] }}" target="_blank" class="social-link-icon text-info" title="Twitter">
                                                <i class="bi bi-twitter fs-5"></i>
                                            </a>
                                        @endif
                                        @if(isset($author->social_links['website']))
                                            <a href="{{ $author->social_links['website'] }}" target="_blank" class="social-link-icon text-secondary" title="Website">
                                                <i class="bi bi-globe fs-5"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif

                                <!-- View Profile Button -->
                                <a href="{{ route('author.show', $author) }}" 
                                   class="btn btn-primary w-100 author-btn">
                                    <i class="bi bi-person-lines-fill me-2"></i> View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state-card text-center py-5">
                            <i class="bi bi-people display-1 text-muted mb-3 d-block"></i>
                            <h4 class="fw-bold">No authors found</h4>
                            <p class="text-muted">Try adjusting your search criteria.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($authors->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $authors->links('pagination::bootstrap-5') }}
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

<!-- JavaScript for Sidebar Toggle (Same as posts index) -->
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
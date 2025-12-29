@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container-fluid">
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
        <div class="col-lg-8">
            <!-- Main Post Content -->
            <div class="post-header">
                 <!-- Access Controls -->
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                    <div class="d-flex justify-content-end mb-2">
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-sm text-white">
                            <i class="bi bi-pencil-square"></i> Edit Post
                        </a>
                    </div>
                @endif

                 <!-- Hero Image Display -->
                @if($post->featured_image)
                    <img src="{{ $post->getImage('social') }}" 
                         alt="{{ $post->title }}" 
                         class="post-hero-image">
                @endif

                <!-- Post Meta -->
                <div class="post-meta">
                    <div class="post-meta-item">
                        <img src="{{ $post->author->avatar_url }}" 
                            alt="{{ $post->author->name }}" 
                            class="rounded-circle me-1" 
                            width="24" height="24" 
                            style="object-fit: cover;">
                        <span>Written by : {{ $post->author->name }}</span>
                    </div>
                    
                    {{-- START: Social Links Implementation --}}
                    {{-- Check if social_links array exists and is not empty --}}
                    @if(!empty($post->author->social_links) && is_array($post->author->social_links))
                        <div class="author-social-links ms-3 d-flex gap-2 align-items-center">
                            
                            {{-- GitHub --}}
                            @if(isset($post->author->social_links['github']) && !empty($post->author->social_links['github']))
                                <a href="{{ $post->author->social_links['github'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="text-secondary"
                                   title="GitHub Profile">
                                    <i class="bi bi-github fs-5"></i>
                                </a>
                            @endif

                            {{-- LinkedIn --}}
                            @if(isset($post->author->social_links['linkedin']) && !empty($post->author->social_links['linkedin']))
                                <a href="{{ $post->author->social_links['linkedin'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="text-primary"
                                   title="LinkedIn Profile">
                                    <i class="bi bi-linkedin fs-5"></i>
                                </a>
                            @endif

                            {{-- Twitter --}}
                            @if(isset($post->author->social_links['twitter']) && !empty($post->author->social_links['twitter']))
                                <a href="{{ $post->author->social_links['twitter'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="text-info"
                                   title="Twitter Profile">
                                    <i class="bi bi-twitter fs-5"></i>
                                </a>
                            @endif

                            {{-- Website --}}
                            @if(isset($post->author->social_links['website']) && !empty($post->author->social_links['website']))
                                <a href="{{ $post->author->social_links['website'] }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="text-dark"
                                   title="Website">
                                    <i class="bi bi-globe fs-5"></i>
                                </a>
                            @endif

                        </div>
                    @endif
                    {{-- END: Social Links Implementation --}}

                    @if($post->updated_by && $post->updated_by !== $post->user_id)
                        <div class="post-meta-item text-primary">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edited by : {{ $post->updater->name }}</span>
                        </div>
                    @endif
                    <div class="post-meta-item">
                        <i class="bi bi-calendar"></i>
                        <span>{{ $post->published_at?->format('M d, Y') }}</span>
                    </div>
                    <div class="post-meta-item">
                        <i class="bi bi-eye"></i>
                        <span>{{ number_format($post->views) }} views</span>
                    </div>
                    <div class="post-meta-item">
                        <i class="bi bi-clock"></i>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>
                </div>
                
                <!-- Post Title -->
                <h1 class="post-title">{{ $post->title }}</h1>
                
                <!-- Post Excerpt -->
                @if($post->excerpt)
                    <div class="post-excerpt">
                        {{ $post->excerpt }}
                    </div>
                @endif
                
                <!-- Categories -->
                @if($post->categories->count())
                    <div class="post-categories">
                        @foreach($post->categories as $category)
                            <span class="category-badge">{{ $category->name }}</span>
                        @endforeach
                    </div>
                @endif
                
                <!-- Tags -->
                @if($post->tags->count())
                    <div class="post-tags">
                        @foreach($post->tags as $tag)
                            <span class="tag-badge">#{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
                
                <!-- Post Content -->
                <div class="post-content">
                    {!! nl2br(e($post->body)) !!}
                </div>
                
                <!-- Social Share -->
                <div class="social-share">
                    <span class="social-share-label">Share this post:</span>
                    <a href="https://x.com" class="social-share-btn">
                        <i class="bi bi-twitter"></i> Twitter
                    </a>
                    <a href="https://www.facebook.com/" class="social-share-btn">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <a href="https://www.linkedin.com/" class="social-share-btn">
                        <i class="bi bi-linkedin"></i> LinkedIn
                    </a>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div id="comments-section" class="comments-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold">
                        <i class="bi bi-chat-dots"></i> 
                        Comments ({{ $post->comments()->approved()->count() }})
                        @can('approve', \App\Models\Comment::class)
                            @php $pending = $post->comments()->where('approved', false)->count(); @endphp
                            @if($pending > 0)
                                <span class="badge bg-warning text-dark ms-2">
                                    {{ $pending }} pending
                                </span>
                            @endif
                        @endcan
                    </h4>
                </div>

                <!-- Comment Form -->
                @if($post->allow_comments ?? true)
                    @include('partials._comment_form', ['post' => $post])
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Comments are closed for this post.
                    </div>
                @endif

                <!-- Comments List -->
                @if($comments->count())
                    @include('partials._comments', ['comments' => $comments, 'depth' => 0, 'maxDepth' => 3])
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-chat-heart display-1 text-muted"></i>
                        <h5 class="mt-3">No comments yet</h5>
                        <p class="text-muted">Be the first to share your thoughts!</p>
                    </div>
                @endif
            </div>

            <!-- Related Posts -->
            @if($post->categories->count())
                @php
                    $relatedPosts = \App\Models\Post::published()
                        ->where('id', '!=', $post->id)
                        ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $post->categories->pluck('id')))
                        ->limit(3)
                        ->get();
                @endphp

                @if($relatedPosts->count())
                    <div class="related-posts">
                        <div class="related-posts-header">
                            <i class="bi bi-journal-bookmark"></i>
                            <span>Related Posts</span>
                        </div>
                        @foreach($relatedPosts as $related)
                            <div class="related-post-card">
                                <div class="related-post-info">
                                    <div class="related-post-author">
                                        <i class="bi bi-person"></i> {{ $related->author->name }}
                                    </div>
                                    <h3 class="related-post-title">
                                        <a href="{{ route('posts.show', $related) }}" class="text-decoration-none text-dark">
                                            {{ $related->title }}
                                        </a>
                                    </h3>
                                    <p class="related-post-excerpt">
                                        {{ Str::limit(strip_tags($related->body), 100) }}
                                    </p>
                                    <div class="related-post-meta">
                                        <div class="related-post-stats">
                                            <i class="bi bi-eye"></i> {{ number_format($related->views) }}
                                            <i class="bi bi-chat-dots ms-2"></i> 0
                                        </div>
                                        <span class="text-muted">{{ $related->published_at?->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('posts.show', $related) }}" class="read-more-btn">
                                    Read More →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
        
        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- FIXED: Changed class to 'sidebar-wrapper' to match post.css styles -->
            <div class="sidebar-wrapper" id="sidebarWrapper">
                @include('partials._sidebar')
            </div>
        </div>
    </div>

    <!-- Sidebar Toggle Button (for mobile) -->
    <button class="sidebar-toggle-button collapsed" id="sidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-chevron-right"></i>
    </button>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Sidebar Toggle Functionality ---
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarWrapper');
    
    function toggleSidebarState() {
        if (window.innerWidth <= 992) {
            sidebar.classList.toggle('show');
            sidebarToggle.classList.toggle('collapsed');
            
            const icon = sidebarToggle.querySelector('i');
            if (sidebar.classList.contains('show')) {
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
            } else {
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
            }
        }
    }

    function closeSidebarIfOutside(event) {
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
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', toggleSidebarState);
        document.addEventListener('click', closeSidebarIfOutside);
    }

    // --- 2. Scroll to Comment from URL Hash ---
    const hash = window.location.hash;
    if (hash && hash.startsWith('#comment-')) {
        const element = document.querySelector(hash);
        if (element) {
            // Small delay to ensure rendering
            setTimeout(() => {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.classList.add('border', 'border-primary');
            }, 100);
        }
    }

    // --- 3. Character Count for Comment Form ---
    const commentBody = document.getElementById('body');
    if (commentBody) {
        commentBody.addEventListener('input', function(e) {
            const count = e.target.value.length;
            const counter = document.getElementById('comment-char-count');
            if (counter) counter.textContent = count;
            
            if (count > 1000) {
                e.target.classList.add('is-invalid');
            } else {
                e.target.classList.remove('is-invalid');
            }
        });
    }
});

// --- 4. Global Toggle Reply Function (Needs to be on window) ---
window.toggleReplyForm = function(commentId) {
    const form = document.getElementById(`reply-form-${commentId}`);
    if (form) {
        const isHidden = form.style.display === 'none' || form.style.display === '';
        form.style.display = isHidden ? 'block' : 'none';
        
        if (isHidden) {
            const textarea = form.querySelector('textarea');
            if (textarea) textarea.focus();
        }
    }
};
</script>
@endpush
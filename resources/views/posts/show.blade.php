@extends('layouts.app')

@section('title', $post->title)

@push('styles')
<style>
    /* Post Page Specific Styles */
    .post-header {
        padding: 2rem;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .post-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .post-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .post-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.2;
        color: #1f2937;
    }
    
    .post-excerpt {
        border-left: 4px solid var(--primary);
        padding: 0.75rem 1rem;
        margin: 1rem 0;
        background-color: rgba(var(--primary-rgb), 0.05);
        font-style: italic;
    }
    
    .post-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .category-badge {
        background-color: #e5e7eb;
        color: #374151;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .post-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .tag-badge {
        background-color: #f3f4f6;
        color: #4b5563;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
    }
    
    .post-content {
        font-size: 1rem;
        line-height: 1.6;
        color: #374151;
        margin-bottom: 2rem;
    }
    
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .post-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        border: 1px solid var(--border-color);
        margin: 1.5rem 0;
    }
    
    .post-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
        border: 1px solid var(--border-color);
    }
    
    .social-share {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        margin-top: 1.5rem;
    }
    
    .social-share-label {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .social-share-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        background-color: white;
        color: #374151;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .social-share-btn:hover {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    
    .comments-section {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .comments-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .comments-placeholder {
        background-color: #d1fae5;
        border-left: 4px solid #10b981;
        padding: 1rem;
        border-radius: 0.375rem;
        margin: 1rem 0;
        font-size: 0.875rem;
        color: #065f46;
    }
    
    .related-posts {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
    }
    
    .related-posts-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .related-post-card {
        background-color: white;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .related-post-info {
        flex: 1;
        margin-right: 1rem;
    }
    
    .related-post-author {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    
    .related-post-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }
    
    .related-post-excerpt {
        font-size: 0.875rem;
        color: #4b5563;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }
    
    .related-post-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .related-post-stats {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .read-more-btn {
        background-color: var(--primary);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }
    
    .read-more-btn:hover {
        background-color: #e8291c;
    }

    /* Comment Section Styles */
    .comment-item {
        transition: all 0.3s ease;
    }
    .comment-item:hover {
        transform: translateY(-2px);
    }
    .replies {
        border-left: 3px solid var(--primary);
        padding-left: 1rem;
    }
    
    @media (max-width: 768px) {
        .post-header {
            padding: 1rem;
        }
        
        .post-title {
            font-size: 2rem;
        }
        
        .post-meta {
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }
        
        .related-post-card {
            flex-direction: column;
        }
        
        .related-post-info {
            margin-right: 0;
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Post Content -->
            <div class="post-header">
                 <!-- Accesess -->
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                    <div class="d-flex justify-content-end mb-2">
                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning btn-sm text-white">
                            <i class="bi bi-pencil-square"></i> Edit Post
                        </a>
                    </div>
                @endif
                <!-- Post Meta -->
                <div class="post-meta">
                    <div class="post-meta-item">
                        <i class="bi bi-person"></i>
                        <span>Written by : {{ $post->author->name }}</span>
                    </div>
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
                    <a href="#" class="social-share-btn">
                        <i class="bi bi-twitter"></i> Twitter
                    </a>
                    <a href="#" class="social-share-btn">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <a href="#" class="social-share-btn">
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
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-container">
                @include('partials._sidebar')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle reply form visibility
function toggleReplyForm(commentId) {
    const form = document.getElementById(`reply-form-${commentId}`);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    // Focus the textarea
    if (form.style.display === 'block') {
        form.querySelector('textarea').focus();
    }
}

// Character count for comment form
document.getElementById('body')?.addEventListener('input', function(e) {
    const count = e.target.value.length;
    const counter = document.getElementById('comment-char-count');
    if (counter) counter.textContent = count;
    
    if (count > 1000) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});

// Scroll to comment from URL hash
window.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash.startsWith('#comment-')) {
        const element = document.querySelector(hash);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('border', 'border-primary');
        }
    }
});
</script>
@endpush
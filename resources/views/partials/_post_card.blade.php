@props(['post', 'compact' => false])

<div class="card shadow-sm border-0 mb-4 {{ $compact ? 'card-compact' : 'h-100 post-card' }}">
    @if($post->featured_image)
        <img src="{{ $post->getImage('card') }}" 
             alt="{{ $post->title }}" 
             class="card-img-top post-card-image" 
             style="height: {{ $compact ? '120px' : '200px' }}; object-fit: cover;">
    @endif

    <div class="card-body d-flex flex-column post-card-body">
        <!-- Meta -->
        <div class="d-flex justify-content-between align-items-center text-muted small mb-2">
            <span class="d-flex align-items-center">
                <!-- Fix: Added Author Avatar with Fallback Logic -->
                <img src="{{ $post->author->avatar_url }}" 
                     alt="{{ $post->author->name }}" 
                     class="rounded-circle me-1 post-card-author-img" 
                     width="24" height="24" 
                     style="object-fit: cover;">
                
                <span class="fw-bold text-dark" style="margin-left: 3px;">Written by : {{ $post->author->name }}</span>
            </span>
            
            @if($post->updater && $post->updater->id !== $post->user_id)
            <span class="d-flex align-items-center">
                <!-- Optional: You could add avatar here too if needed -->
                <i class="bi bi-pencil-square ms-1"></i>
                <span>Edited by : {{ $post->updater->name }}</span>
            </span>
            @endif
            
            <span class="post-card-date">
                <i class="bi bi-calendar"></i> {{ $post->published_at?->format('M d, Y') }}
            </span>
        </div>

        <!-- Title -->
        <h5 class="card-title fw-bold mb-2 post-card-title">
            <a href="{{ route('posts.show', $post) }}" class="text-decoration-none text-dark">
                {{ $post->title }}
            </a>
        </h5>

        <!-- Excerpt -->
        @if($post->excerpt)
            <p class="card-text text-muted flex-grow-1 {{ $compact ? 'small' : 'post-card-content' }}">
                {{ Str::limit($post->excerpt, $compact ? 80 : 150) }}
            </p>
        @else
            <p class="card-text text-muted flex-grow-1 {{ $compact ? 'small' : 'post-card-content' }}">
                {{ Str::limit(strip_tags($post->body), $compact ? 80 : 150) }}
            </p>
        @endif

        <!-- Categories & Tags -->
        <div class="mb-3">
            @if($post->categories->count())
                @foreach($post->categories->take(2) as $category)
                    <span class="badge bg-secondary me-1 {{ $compact ? 'small' : 'post-card-tag' }}">{{ $category->name }}</span>
                @endforeach
            @endif
            @if($post->tags->count())
                @foreach($post->tags->take(3) as $tag)
                    <span class="badge bg-light text-dark me-1 {{ $compact ? 'small' : 'post-card-tag' }}">#{{ $tag->name }}</span>
                @endforeach
            @endif
        </div>

        <!-- Footer Meta -->
        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
            <div class="text-muted small">
                <i class="bi bi-eye"></i> {{ number_format($post->views) }}
                <span class="ms-2">
                    <i class="bi bi-chat-dots"></i> 
                    {{ $post->comments_count ?? 0 }}
                </span>
                <span>
                    <i class="bi bi-clock"></i>
                    {{ $post->reading_time }} min read
                </span>
            </div>
            
            <!-- Actions -->
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
                <button type="button" 
                        class="btn btn-sm btn-outline-danger border-0" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deletePostModal{{ $post->id }}"
                        title="Delete Post">
                    <i class="bi bi-trash"></i>
                </button>
            @endif

            <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-primary post-card-button post-card-button-primary">
                Read More <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Delete Post Modal -->
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor()))
<div class="modal fade" id="deletePostModal{{ $post->id }}" tabindex="-1" aria-labelledby="deletePostModalLabel{{ $post->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('posts.destroy', $post) }}" method="POST">
                @csrf @method('DELETE')
                
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deletePostModalLabel{{ $post->id }}">
                        <i class="bi bi-exclamation-triangle-fill"></i> Delete Post
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p>Are you sure you want to move the following post to the trash?</p>
                    <div class="alert alert-light border">
                        <strong class="text-primary">{{ $post->title }}</strong>
                    </div>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-info-circle"></i> This post can be restored from the admin panel later.
                    </p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light text-dark" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Move to Trash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@props([
    'comments',
    'depth' => 0,
    'maxDepth' => 3, 
])

@foreach($comments as $comment)
    @if($comment->approved || auth()->user()?->can('approve', $comment))
        <div id="comment-{{ $comment->id }}" class="comment-item mb-4">
            
            <!-- Main Comment Card -->
            <div class="card border-0 shadow-sm {{ !$comment->approved ? 'opacity-75 border-start border-warning' : '' }}">
                <div class="card-body p-3">
                    <!-- Comment Header -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            <img src="{{ $comment->avatar_url }}" 
                                 alt="{{ $comment->display_name }}" 
                                 class="rounded-circle me-2" 
                                 width="40" height="40" 
                                 style="object-fit: cover;">
                            <div>
                                <div class="d-flex align-items-center">
                                    <strong class="mb-0 text-truncate" style="max-width: 150px;">{{ $comment->display_name }}</strong>
                                    @if(!$comment->user_id)
                                        <span class="badge bg-light text-dark ms-1">Guest</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ $comment->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        <!-- Approval Badge -->
                        @if(!$comment->approved)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-hourglass-split"></i> Pending
                            </span>
                        @endif
                    </div>

                    <!-- Comment Body -->
                    <p class="mb-2">{{ $comment->body }}</p>

                    <!-- Comment Actions -->
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="toggleReplyForm({{ $comment->id }})">
                            <i class="bi bi-reply"></i> Reply
                        </button>

                        @can('approve', $comment)
                            @if(!$comment->approved)
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.comments.reject', $comment) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('delete', $comment)
                            <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this comment?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endcan
                    </div>

                    <!-- Main Reply Form -->
                    <div id="reply-form-{{ $comment->id }}" class="mt-3" style="display: none;">
                        <div class="card border-0 bg-light">
                            <div class="card-body p-3">
                                <form action="{{ route('comments.reply', $comment) }}" method="POST">
                                    @csrf
                                    @guest
                                        <div class="row mb-2">
                                            <div class="col-md-6">
                                                <input type="text" name="guest_name" class="form-control" 
                                                       placeholder="Your Name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" name="guest_email" class="form-control" 
                                                       placeholder="Your Email" required>
                                            </div>
                                        </div>
                                    @endguest
                                    <textarea name="body" class="form-control mb-2" rows="3" 
                                              placeholder="Write your reply..." required></textarea>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-send"></i> Post Reply
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" 
                                            onclick="toggleReplyForm({{ $comment->id }})">
                                        Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- === (Flat Thread) === -->
                    @if($comment->getAllReplies()->count())
                        <div class="mt-4">
                            
                            <!-- Button to show and hide replies -->
                            <button id="toggle-replies-{{ $comment->id }}" 
                                    data-count="{{ $comment->getAllReplies()->count() }}"
                                    class="btn btn-sm btn-link text-decoration-none text-muted mb-2 p-0" 
                                    onclick="toggleFlatReplies({{ $comment->id }})">
                                <i class="bi bi-chevron-down me-1"></i> 
                                <span id="replies-text-{{ $comment->id }}">Show {{ $comment->getAllReplies()->count() }} replies</span>
                            </button>

                            <!-- Replies container lists -->
                            <div id="flat-replies-{{ $comment->id }}" style="display: none;">
                                @foreach($comment->getAllReplies() as $reply)
                                    <div class="d-flex mb-3 border-start border-2 border-light ps-3">
                                        <!-- Avatar -->
                                        <img src="{{ $reply->avatar_url }}" 
                                             alt="{{ $reply->display_name }}" 
                                             class="rounded-circle me-2" 
                                             width="32" height="32" 
                                             style="object-fit: cover;">
                                        
                                        <!-- Content -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong class="text-dark">{{ $reply->display_name }}</strong>
                                                    
                                                    @if($reply->parent_id !== $comment->id && $reply->parent)
                                                        <span class="text-muted small ms-1">
                                                            <i class="bi bi-reply-fill"></i> 
                                                            to {{ $reply->parent->display_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <small class="text-muted" style="font-size: 0.8rem;">
                                                    {{ $reply->created_at->diffForHumans() }}
                                                </small>
                                            </div>

                                            <div class="text-dark mt-1" style="font-size: 0.95rem;">
                                                {{ $reply->body }}
                                            </div>

                                            <!-- Actions for Reply -->
                                            <div class="mt-1">
                                                <button class="btn btn-sm btn-link text-decoration-none text-muted p-0 small" 
                                                        onclick="toggleReplyForm({{ $reply->id }})">
                                                    Reply
                                                </button>

                                                @can('delete', $reply)
                                                    <span class="mx-1">•</span>
                                                    <form action="{{ route('admin.comments.destroy', $reply) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none small" onclick="return confirm('Delete this reply?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>

                                            <!-- Reply Form for Nested Comments (Hidden) -->
                                            <div id="reply-form-{{ $reply->id }}" class="mt-2" style="display: none;">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body p-2">
                                                        <form action="{{ route('comments.reply', $reply) }}" method="POST">
                                                            @csrf
                                                            @guest
                                                                <div class="row mb-2">
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="guest_name" class="form-control form-control-sm" placeholder="Name" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="email" name="guest_email" class="form-control form-control-sm" placeholder="Email" required>
                                                                    </div>
                                                                </div>
                                                            @endguest
                                                            <textarea name="body" class="form-control form-control-sm mb-1" rows="2" placeholder="Write a reply..." required></textarea>
                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    @endif
@endforeach

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

function toggleFlatReplies(commentId) {
    const repliesContainer = document.getElementById(`flat-replies-${commentId}`);
    const toggleButton = document.getElementById(`toggle-replies-${commentId}`);
    const repliesText = document.getElementById(`replies-text-${commentId}`);
    const icon = toggleButton.querySelector('i');
    
    const replyCount = toggleButton.getAttribute('data-count');
    
    if (repliesContainer.style.display === 'none') {
        repliesContainer.style.display = 'block';
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        repliesText.textContent = 'Hide replies';
    } else {
        repliesContainer.style.display = 'none';
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        repliesText.textContent = `Show ${replyCount} replies`;
    }
}
</script>
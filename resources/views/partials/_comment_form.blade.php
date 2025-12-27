@props(['post'])

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="card-title mb-3">
            <i class="bi bi-chat-dots"></i> Leave a Comment
        </h5>

        <form action="{{ route('posts.comments.store', $post) }}" method="POST">
            @csrf

            @guest
                @if(config('blog.allow_guest_comments', true))
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="guest_name" class="form-label">Name *</label>
                            <input type="text" id="guest_name" name="guest_name" 
                                   class="form-control @error('guest_name') is-invalid @enderror" 
                                   value="{{ old('guest_name') }}" required>
                            @error('guest_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="guest_email" class="form-label">Email *</label>
                            <input type="email" id="guest_email" name="guest_email" 
                                   class="form-control @error('guest_email') is-invalid @enderror" 
                                   value="{{ old('guest_email') }}" required>
                            @error('guest_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Please <a href="{{ route('login') }}">login</a> to leave a comment.
                    </div>
                @endif
            @endguest

            <div class="mb-3">
                <label for="body" class="form-label">Comment *</label>
                <textarea id="body" name="body" rows="5" 
                          class="form-control @error('body') is-invalid @enderror" 
                          placeholder="Share your thoughts..." required>{{ old('body') }}</textarea>
                <div class="form-text">
                    <span id="comment-char-count">0</span>/1000 characters
                </div>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="form-text text-muted">
                    Your email address will not be published. Required fields are marked *
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Post Comment
                </button>
            </div>
        </form>
    </div>
</div>
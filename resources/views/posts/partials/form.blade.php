@php
    $isPublished = $post?->status === 'published';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @method($method)

    <!-- Title -->
    <div class="mb-4">
        <label for="title" class="form-label fw-bold">
            Post Title <span class="text-danger">*</span>
        </label>
        <input type="text" id="title" name="title" 
               class="form-control form-control-lg @error('title') is-invalid @enderror" 
               value="{{ old('title', $post?->title) }}" 
               placeholder="Enter a compelling title" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Slug -->
    <div class="mb-4">
        <label for="slug" class="form-label fw-bold">
            URL Slug <small class="text-muted">(auto-generated if empty)</small>
        </label>
        <input type="text" id="slug" name="slug" 
               class="form-control @error('slug') is-invalid @enderror" 
               value="{{ old('slug', $post?->slug) }}" 
               placeholder="my-awesome-post">
        <div class="form-text">
            <i class="bi bi-link-45deg"></i> 
            Will be: {{ config('app.url') }}/posts/<span id="slug-preview">{{ $post?->slug ?? 'your-slug' }}</span>
        </div>
        @error('slug')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Featured Image Section -->
    <div class="mb-4">
        <label class="form-label fw-bold">Featured Image</label>
        
        <!-- Current Image Preview -->
        @if($post?->featured_image)
            <div class="current-image-preview mb-3 p-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . str_replace('original', 'thumb', $post->featured_image)) }}" 
                             alt="Current featured image" 
                             class="img-fluid rounded" id="current-image">
                    </div>
                    <div class="col-md-8">
                        <p class="mb-1">
                            <strong>Current Image:</strong> {{ basename($post->featured_image) }}
                        </p>
                        <p class="text-muted small mb-2">
                            Size: {{ Storage::size('public/' . $post->featured_image) }} bytes
                        </p>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="delete_image" name="delete_image" 
                                   value="1" {{ old('delete_image') ? 'checked' : '' }}>
                            <label class="form-check-label text-danger" for="delete_image">
                                <i class="bi bi-trash"></i> Delete this image
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- New Image Upload -->
        <input type="file" id="featured_image" name="featured_image" accept="image/*"
               class="form-control @error('featured_image') is-invalid @enderror"
               onchange="previewImage(event, 'image-preview-new')">
        
        @error('featured_image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        
        <!-- New Image Preview -->
        <div class="mt-3">
            <img id="image-preview-new" style="display: none; max-height: 200px;" 
                 class="img-fluid rounded border">
        </div>

        <div class="form-text mt-2">
            <i class="bi bi-info-circle"></i> 
            Recommended: 1920x1080px or larger. Max file size: 5MB. 
            Supported: JPG, PNG, GIF, WebP.
        </div>
    </div>

    <!-- Excerpt -->
    <div class="mb-4">
        <label for="excerpt" class="form-label fw-bold">
            Excerpt <small class="text-muted">(Optional summary)</small>
        </label>
        <textarea id="excerpt" name="excerpt" rows="3"
                  class="form-control @error('excerpt') is-invalid @enderror" 
                  placeholder="Brief summary of your post...">{{ old('excerpt', $post?->excerpt) }}</textarea>
        <div class="form-text"><span id="excerpt-count">0</span>/500 characters</div>
        @error('excerpt')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Body -->
    <div class="mb-4">
        <label for="body" class="form-label fw-bold">
            Post Content <span class="text-danger">*</span>
        </label>
        <textarea id="body" name="body" rows="15"
                  class="form-control @error('body') is-invalid @enderror" 
                  placeholder="Write your amazing content here...">{{ old('body', $post?->body) }}</textarea>
        <div class="form-text">
            <i class="bi bi-info-circle"></i> 
            Estimated reading time: <span id="reading-time">2</span> min
        </div>
        @error('body')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Categories -->
    <div class="mb-4">
        <label class="form-label fw-bold">Categories</label>
        <div class="row g-2">
            @forelse($categories as $category)
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                               class="form-check-input" id="category-{{ $category->id }}"
                               {{ in_array($category->id, old('categories', $post?->categories->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="category-{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    </div>
                </div>
            @empty
                <p class="text-muted">No categories available.</p>
            @endforelse
        </div>
        @error('categories')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Tags -->
    <div class="mb-4">
        <label for="tags" class="form-label fw-bold">Tags</label>
        <div class="row g-2">
            @forelse($tags as $tag)
                <div class="col-md-3">
                    <div class="form-check">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                               class="form-check-input" id="tag-{{ $tag->id }}"
                               {{ in_array($tag->id, old('tags', $post?->tags->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tag-{{ $tag->id }}">
                            #{{ $tag->name }}
                        </label>
                    </div>
                </div>
            @empty
                <p class="text-muted">No tags available.</p>
            @endforelse
        </div>
        @error('tags')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Status -->
    <div class="mb-4">
        <label for="status" class="form-label fw-bold">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="draft" {{ old('status', $post?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>
                Draft
            </option>
            @can('publish posts')
                <option value="published" {{ old('status', $post?->status) === 'published' ? 'selected' : '' }}>
                    Published
                </option>
            @endcan
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Allow Comments Settings -->
    <div class="mb-4 p-3 bg-light rounded-border">
        <label class="form-label fw-bold d-block mb-2">
            <i class="bi bi-chat-square-text"></i> Discussion Settings
        </label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="allow_comments" id="allow_comments" value="1"
                   @if(old('allow_comments', $post?->allow_comments ?? true)) checked @endif>
            <label class="form-check-label" for="allow_comments">
                Allow users to comment on this post
            </label>
        </div>
        <div class="form-text text-muted">
            Uncheck this to disable the comment form on the post page.
        </div>
    </div>
    <!-- Action Buttons -->
    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
        <div>
            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
        <div class="btn-group">
            @if($post && !$isPublished)
                @can('publish posts')
                    <button type="submit" name="status" value="published" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Publish Now
                    </button>
                @endcan
            @endif
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ $buttonText }}
            </button>
        </div>
    </div>
</form>
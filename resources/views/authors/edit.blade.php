@extends('layouts.admin')

@section('title', 'Edit Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('author.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Avatar -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold d-block">Profile Picture</label>
                        <div class="mb-3">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle" 
                                 width="120" height="120" 
                                 style="object-fit: cover; border: 3px solid var(--primary);">
                        </div>
                        <input type="file" name="avatar" accept="image/*" 
                               class="form-control @error('avatar') is-invalid @enderror">
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if(auth()->user()->avatar)
                            <div class="form-check mt-2">
                                <input type="checkbox" name="delete_avatar" value="1" 
                                       class="form-check-input" id="delete_avatar">
                                <label class="form-check-label text-danger" for="delete_avatar">
                                    Delete current avatar
                                </label>
                            </div>
                        @endif
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" id="name" name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" id="email" name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <label for="bio" class="form-label fw-bold">
                            Biography <small class="text-muted">(optional)</small>
                        </label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="form-control @error('bio') is-invalid @enderror" 
                                  placeholder="Tell us about yourself...">{{ old('bio', auth()->user()->bio) }}</textarea>
                        <div class="form-text"><span id="bio-count">{{ strlen(old('bio', auth()->user()->bio)) }}</span>/1000 characters</div>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Social Links -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Social Links</label>
                        
                        <div class="input-group mb-2">
                            <span class="input-group-text">
                                <i class="bi bi-twitter"></i>
                            </span>
                            <input type="url" name="social_links[twitter]" 
                                   class="form-control @error('social_links.twitter') is-invalid @enderror" 
                                   value="{{ old('social_links.twitter', auth()->user()->social_links['twitter'] ?? '') }}" 
                                   placeholder="https://twitter.com/username">
                            @error('social_links.twitter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group mb-2">
                            <span class="input-group-text">
                                <i class="bi bi-github"></i>
                            </span>
                            <input type="url" name="social_links[github]" 
                                   class="form-control @error('social_links.github') is-invalid @enderror" 
                                   value="{{ old('social_links.github', auth()->user()->social_links['github'] ?? '') }}" 
                                   placeholder="https://github.com/username">
                            @error('social_links.github')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group mb-2">
                            <span class="input-group-text">
                                <i class="bi bi-linkedin"></i>
                            </span>
                            <input type="url" name="social_links[linkedin]" 
                                   class="form-control @error('social_links.linkedin') is-invalid @enderror" 
                                   value="{{ old('social_links.linkedin', auth()->user()->social_links['linkedin'] ?? '') }}" 
                                   placeholder="https://linkedin.com/in/username">
                            @error('social_links.linkedin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-globe"></i>
                            </span>
                            <input type="url" name="social_links[website]" 
                                   class="form-control @error('social_links.website') is-invalid @enderror" 
                                   value="{{ old('social_links.website', auth()->user()->social_links['website'] ?? '') }}" 
                                   placeholder="https://your-website.com">
                            @error('social_links.website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between pt-4 border-top">
                        <a href="{{ route('author.show', auth()->user()) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Character counter for bio
document.getElementById('bio')?.addEventListener('input', function(e) {
    const count = e.target.value.length;
    document.getElementById('bio-count').textContent = count;
    
    if (count > 1000) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});

// Avatar preview
document.querySelector('input[name="avatar"]')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.card-body img.rounded-circle').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
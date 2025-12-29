@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Left Column: Profile & Stats -->
    <div class="dashboard-left-column">
        <!-- Profile Card -->
        <div class="dashboard-card profile-card">
            <div class="profile-card-header">
                <div class="profile-avatar-container">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                         class="profile-avatar">
                    <div class="avatar-overlay">
                        <i class="bi bi-camera"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h3 class="profile-name">{{ auth()->user()->name }}</h3>
                    <p class="profile-email">{{ auth()->user()->email }}</p>
                    <div class="profile-role">
                        <i class="bi bi-person-badge"></i>
                        <span>{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Regular User' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="profile-card-body">
                <div class="profile-details">
                    <div class="detail-item">
                        <span class="detail-label">Member Since</span>
                        <span class="detail-value">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Account Status</span>
                        <span class="detail-value status-active">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </span>
                    </div>
                </div>
                
                @if(auth()->user()->bio)
                    <div class="profile-bio">
                        <h6>About Me</h6>
                        <p>{{ auth()->user()->bio }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Card (for content creators) -->
        @if(auth()->user()->hasRole(['Admin', 'Editor', 'Author']) && $stats['published_posts'] > 0)
            <div class="dashboard-card stats-card">
                <h4 class="card-title">
                    <i class="bi bi-graph-up"></i> My Content Statistics
                </h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value primary">{{ $stats['published_posts'] }}</div>
                        <div class="stat-label">Published Posts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value success">{{ $stats['total_comments'] }}</div>
                        <div class="stat-label">Total Comments</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value info">{{ $stats['total_views'] }}</div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions Card -->
        <div class="dashboard-card actions-card">
            <h4 class="card-title">
                <i class="bi bi-lightning"></i> Quick Actions
            </h4>
            <div class="actions-grid">
                @can('create', App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="action-btn primary">
                        <i class="bi bi-plus-circle"></i>
                        <span>Create Post</span>
                    </a>
                @endcan
                
                @can('view-admin-panel')
                    <a href="{{ route('admin.dashboard') }}" class="action-btn warning">
                        <i class="bi bi-shield-check"></i>
                        <span>Admin Panel</span>
                    </a>
                @endcan
                
                <a href="{{ route('posts.index') }}" class="action-btn secondary">
                    <i class="bi bi-file-text"></i>
                    <span>Browse Posts</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Form -->
    <div class="dashboard-right-column">
        <div class="dashboard-card edit-card">
            <h4 class="card-title">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </h4>
            <form method="POST" action="{{ route('dashboard.update') }}" enctype="multipart/form-data" class="edit-form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" id="name" name="name" 
                           class="form-control" 
                           value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" 
                           class="form-control" 
                           value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Biography (optional)</label>
                    <textarea id="bio" name="bio" rows="4"
                              class="form-control" 
                              maxlength="1000"
                              placeholder="Tell us about yourself...">{{ old('bio', auth()->user()->bio) }}</textarea>
                    <div class="char-counter">
                        <span id="bio-count">{{ strlen(old('bio', auth()->user()->bio ?? '')) }}</span>/1000
                    </div>
                    @error('bio')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="avatar" class="form-label">Profile Picture</label>
                    <div class="avatar-upload-container">
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="file-input">
                        <label for="avatar" class="file-label">
                            <i class="bi bi-upload"></i>
                            <span>Choose File</span>
                        </label>
                    </div>
                    
                    @if(auth()->user()->avatar)
                        <div class="form-check mt-2">
                            <input type="checkbox" name="delete_avatar" value="1" 
                                   class="form-check-input" id="delete_avatar">
                            <label class="form-check-label" for="delete_avatar">
                                Delete current avatar
                            </label>
                        </div>
                    @endif
                    
                    @error('avatar')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="password-info">
                    <i class="bi bi-info-circle"></i>
                    To change your password, use the 
                    <a href="{{ route('password.request') }}">password reset</a> feature.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i>
                        <span>Update Profile</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for bio
    const bioInput = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-count');
    
    if (bioInput && bioCounter) {
        bioInput.addEventListener('input', function() {
            const count = this.value.length;
            bioCounter.textContent = count;
            
            if (count > 1000) {
                bioInput.classList.add('char-limit-exceeded');
            } else {
                bioInput.classList.remove('char-limit-exceeded');
            }
        });
    }

    // Avatar preview
    const avatarInput = document.querySelector('input[name="avatar"]');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarImg = document.querySelector('.profile-avatar');
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
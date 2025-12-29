@extends('layouts.app')

@section('title', $user->name . ' - Author Profile')

@push('styles')
    <style>
        
        .profile-container {
            display: flex;
            flex-direction: row;
            gap: 1.5rem;
        }

        .profile-sidebar {
            position: sticky;
            top: 2rem;
            align-self: start;
            z-index: 10;
        }

        .profile-card {
            background: var(--post-card-bg);
            border: 1px solid var(--post-border-color);
            border-radius: 0.75rem;
            box-shadow: var(--post-shadow-md);
            overflow: hidden;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--post-primary), #E8291C);
            padding: 3rem 1.5rem 5rem;
            position: relative;
        }

        .profile-avatar-container {
            position: absolute;
            bottom: -3.5rem;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid var(--post-card-bg);
            object-fit: cover;
            box-shadow: var(--post-shadow-lg);
            background-color: #fff;
            transition: var(--post-transition-normal);
        }

        .profile-avatar:hover {
            transform: scale(1.05);
        }

        .profile-info {
            padding: 4rem 1.5rem 1.5rem;
        }

        .profile-name {
            color: var(--post-text-main);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .profile-email {
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .profile-bio {
            color: var(--post-text-main);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--post-border-color);
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-value {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--post-primary);
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #718096;
        }

        .social-links-row {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .social-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #EDF2F7;
            color: var(--post-text-main);
            transition: var(--post-transition-fast);
            text-decoration: none;
            font-size: 1rem;
        }

        .social-icon:hover {
            background-color: var(--post-primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--post-shadow-md);
        }

        @media (max-width: 991px) {
            .profile-sidebar {
                position: static;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        
        <div class="col-lg-4 col-xl-3">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar-container">
                            <img src="{{ $user->avatar_url }}" 
                                 alt="{{ $user->name }}" 
                                 class="profile-avatar">
                        </div>
                    </div>
                    
                    <div class="profile-info">
                        <h2 class="profile-name">{{ $user->name }}</h2>
                        <p class="profile-email">
                            <i class="bi bi-envelope-fill me-1"></i> {{ $user->email }}
                        </p>
                        
                        <div class="mb-3">
                            @foreach($user->roles as $role)
                                <span class="badge me-1 
                                    {{ $role->name === 'Admin' ? 'bg-danger' : ($role->name === 'Editor' ? 'bg-warning text-dark' : 'bg-primary') }}">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </div>

                        @if($user->bio)
                            <p class="profile-bio">{{ $user->bio }}</p>
                        @endif

                        @if(!empty($user->social_links) && is_array($user->social_links))
                            <div class="social-links-row">
                                @if(isset($user->social_links['github']))
                                    <a href="{{ $user->social_links['github'] }}" target="_blank" class="social-icon" title="GitHub">
                                        <i class="bi bi-github"></i>
                                    </a>
                                @endif
                                @if(isset($user->social_links['linkedin']))
                                    <a href="{{ $user->social_links['linkedin'] }}" target="_blank" class="social-icon" title="LinkedIn">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                @endif
                                @if(isset($user->social_links['twitter']))
                                    <a href="{{ $user->social_links['twitter'] }}" target="_blank" class="social-icon" title="Twitter">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                @endif
                                @if(isset($user->social_links['website']))
                                    <a href="{{ $user->social_links['website'] }}" target="_blank" class="social-icon" title="Website">
                                        <i class="bi bi-globe"></i>
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if(auth()->check() && auth()->id() === $user->id)
                            <a href="{{ route('author.edit') }}" class="btn btn-primary w-100 mb-3" style="background-color: var(--post-primary); border: none;">
                                <i class="bi bi-pencil-square me-2"></i> Edit Profile
                            </a>
                        @endif

                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-value">{{ $stats['total_posts'] }}</span>
                                <span class="stat-label">Posts</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">{{ number_format($stats['total_views']) }}</span>
                                <span class="stat-label">Views</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">{{ number_format($stats['total_comments']) }}</span>
                                <span class="stat-label">Comments</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value text-truncate" style="font-size: 1rem; line-height: 1.5rem;">
                                    {{ $stats['member_since'] }}
                                </span>
                                <span class="stat-label">Since</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9 post-grid-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0" style="color: var(--post-text-main);">
                    <i class="bi bi-file-earmark-text me-2"></i> Posts by {{ $user->name }}
                </h3>
                <span class="text-muted small">{{ $stats['total_posts'] }} published posts</span>
            </div>

            @if($posts->count())
                <div class="row g-4">
                    @foreach($posts as $post)
                        <div class="col-md-6">
                            @include('partials._post_card', ['post' => $post])
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $posts->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="card border-0 shadow-sm p-5 text-center" style="background-color: var(--post-card-bg);">
                    <i class="bi bi-clipboard-data display-1 text-muted mb-3" style="color: var(--post-text-main); opacity: 0.5;"></i>
                    <h4 class="fw-bold mb-2">No posts yet</h4>
                    <p class="text-muted">{{ $user->name }} hasn't published any posts.</p>
                    @if(auth()->check() && auth()->id() === $user->id)
                        <a href="{{ route('posts.create') }}" class="btn btn-outline-primary mt-2" style="border-color: var(--post-primary); color: var(--post-primary);">
                            Write your first post
                        </a>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
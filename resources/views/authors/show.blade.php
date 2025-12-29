@extends('layouts.admin')

@section('title', $user->name . ' - Author Profile')
@push('styles')
    <style>
        .col-lg-8{
            overflow-y: scroll;
            height: 42rem;
        }
    </style>
@endpush
@section('content')
<div class="row">
    <div class="col-lg-4 col-xl-3">
        <!-- Author Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center">
                <img src="{{ $user->avatar_url }}" 
                     alt="{{ $user->name }}" 
                     class="rounded-circle mb-3" 
                     width="150" height="150" 
                     style="object-fit: cover; border: 4px solid var(--primary);">
                
                <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
                <p class="text-muted mb-2">
                    <i class="bi bi-envelope"></i> {{ $user->email }}
                </p>
                
                <div class="mb-3">
                    <span class="badge bg-primary">{{ $user->roles->pluck('name')->join(', ') }}</span>
                </div>

                @if($user->bio)
                    <p class="text-center">{{ $user->bio }}</p>
                @endif

                <!-- Social Links -->
                @if($user->social_links)
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @foreach($user->social_links as $platform => $url)
                            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-{{ $platform }}"></i> {{ ucfirst($platform) }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Edit Button -->
                {{-- CORRECTED: Only show the button if the logged-in user is viewing their own profile --}}
                @if(auth()->check() && auth()->id() === $user->id)
                    <a href="{{ route('author.edit') }}" class="btn btn-primary mt-3 w-100">
                        <i class="bi bi-pencil"></i> Edit Profile
                    </a>
                @endif
            </div>
        </div>

        <!-- Author Stats -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart"></i> Author Stats
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Published Posts:</span>
                    <strong>{{ $stats['total_posts'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Views:</span>
                    <strong>{{ number_format($stats['total_views']) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Comments:</span>
                    <strong>{{ number_format($stats['total_comments']) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Member Since:</span>
                    <strong>{{ $stats['member_since'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-xl-9">
        <!-- Author Posts -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">
                <i class="bi bi-file-text"></i> Posts by {{ $user->name }}
            </h2>
        </div>

        @if($posts->count())
            <div class="row g-4">
                @foreach($posts as $post)
                    <div class="col-md-6">
                        @include('partials._post_card', ['post' => $post])
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <h5 class="mt-3">No posts yet</h5>
                <p class="text-muted">{{ $user->name }} hasn't published any posts yet.</p>
            </div>
        @endif
    </div>
</div>
@endsection
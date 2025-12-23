@extends('layouts.app')

@section('title', 'Blog Posts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Accessess -->
            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isEditor() || auth()->user()->isAuthor()))
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('posts.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle"></i> Create New Post
                    </a>
                </div>
            @endif
            <!-- Posts Container with Scroll -->
            <div class="posts-container">
                @forelse($posts as $post)
                    @include('partials._post_card', ['post' => $post, 'compact' => true])
                @empty
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="mt-3">No posts found</h4>
                            <p class="text-muted">Try adjusting your search or filters.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
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
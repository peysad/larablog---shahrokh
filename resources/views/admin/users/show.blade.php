@extends('layouts.admin')

@section('title', "User: {$user->name}")

@section('breadcrumbs')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>
@endsection

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        
        @unless($user->trashed())
            @can('ban', $user)
                @if(!$user->isBanned())
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#banModal">
                        <i class="bi bi-ban"></i> Ban
                    </button>
                @else
                    <form action="{{ route('admin.users.unban', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-person-check"></i> Unban
                        </button>
                    </form>
                @endif
            @endcan
            
            @can('delete', $user)
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Delete
                </button>
            @endcan
        @else
            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                </button>
            </form>
        @endunless
    </div>
@endsection

@section('content')
<div class="user-details">
    <!-- User Profile Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="{{ $user->avatar_url }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle border" 
                         width="120" height="120" 
                         style="object-fit: cover;">
                </div>
                <div class="col-md-7">
                    <h3 class="mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="role-badge role-{{ strtolower($user->roles->first()?->name ?? 'user') }}">
                            {{ $user->roles->first()?->name ?? 'User' }}
                        </span>
                        @if($user->isBanned())
                            <span class="status-badge status-banned">Banned</span>
                        @endif
                        @if($user->trashed())
                            <span class="status-badge status-deleted">Deleted</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2">
                        <small class="text-muted">Member since: {{ $user->created_at->format('M d, Y') }}</small>
                        <small class="text-muted">Last activity: {{ $activity['last_post']?->diffForHumans() ?? 'Never' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ban Info Alert -->
    @if($user->isBanned())
        <div class="alert alert-danger border-0 shadow-sm">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Banned User</h5>
            <p class="mb-2"><strong>Reason:</strong> {{ $user->ban_reason }}</p>
            <p class="mb-0"><strong>Banned by:</strong> {{ $user->banner?->name ?? 'System' }} 
            on {{ $user->banned_at->format('M d, Y H:i') }}</p>
        </div>
    @endif

    <!-- User Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary display-6">{{ $activity['posts_count'] }}</div>
                    <div class="text-muted small">Total Posts</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success display-6">{{ $activity['published_count'] }}</div>
                    <div class="text-muted small">Published</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning display-6">{{ $activity['draft_count'] }}</div>
                    <div class="text-muted small">Drafts</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info display-6">{{ $user->roles->first()?->permissions->count() ?? 0 }}</div>
                    <div class="text-muted small">Permissions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Posts -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-file-text"></i> Recent Posts</h5>
            <a href="{{ route('author.show', $user) }}" class="btn btn-sm btn-outline-primary">
                View All
            </a>
        </div>
        <div class="card-body p-0">
            @if($user->posts->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Title</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->posts()->latest()->limit(10)->get() as $post)
                                <tr>
                                    <td class="px-4">{{ Str::limit($post->title, 50) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $post->published_at ? 'success' : 'secondary' }}">
                                            {{ $post->published_at ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>{{ $post->published_at?->format('M d, Y') ?? '-' }}</td>
                                    <td>{{ $post->views ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox display-4"></i>
                    <p class="mt-2">No posts yet</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Ban Modal -->
@can('ban', $user)
    <div class="modal fade" id="banModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Ban User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to ban <strong>{{ $user->name }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Reason (required)</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Ban User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan

<!-- Delete Modal -->
@can('delete', $user)
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Move to Trash</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>This will delete <strong>{{ $user->name }}</strong> and all their posts. They can be restored later.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Move to Trash</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan

<style>
.user-details .role-badge {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>
@endsection
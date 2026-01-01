@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="user-management">
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-card--primary">
                <div class="stat-card__header">
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Total Users</h3>
                        <h2 class="stat-card__value">{{ $stats['total'] }}</h2>
                    </div>
                    <div class="stat-card__icon"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card--success">
                <div class="stat-card__header">
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Active</h3>
                        <h2 class="stat-card__value">{{ $stats['active'] }}</h2>
                    </div>
                    <div class="stat-card__icon"><i class="bi bi-person-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card--danger">
                <div class="stat-card__header">
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Banned</h3>
                        <h2 class="stat-card__value">{{ $stats['banned'] }}</h2>
                    </div>
                    <div class="stat-card__icon"><i class="bi bi-person-dash"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card--warning">
                <div class="stat-card__header">
                    <div class="stat-card__content">
                        <h3 class="stat-card__title">Deleted</h3>
                        <h2 class="stat-card__value">{{ $stats['deleted'] }}</h2>
                    </div>
                    <div class="stat-card__icon"><i class="bi bi-trash"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or Email" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive user-table-container">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Posts</th>
                            <th>Last Activity</th>
                            <th>Joined</th>
                            <th class="px-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="{{ $user->trashed() ? 'table-danger' : ($user->isBanned() ? 'table-warning' : '') }}">
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->avatar_url }}" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="40" height="40" 
                                             style="object-fit: cover;">
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge role-{{ strtolower($user->roles->first()?->name ?? 'user') }}">
                                        {{ $user->roles->first()?->name ?? 'User' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->trashed())
                                        <span class="status-badge status-deleted">Deleted</span>
                                    @elseif($user->isBanned())
                                        <span class="status-badge status-banned">Banned</span>
                                    @else
                                        <span class="status-badge status-active">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $user->published_posts_count }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $user->last_activity ?? 'No activity' }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group" role="group">
                                        @unless($user->trashed())
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            @can('ban', $user)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#banModal{{ $user->id }}">
                                                    <i class="bi bi-ban"></i>
                                                </button>
                                            @endcan
                                            
                                            @can('unban', $user)
                                                <form action="{{ route('admin.users.unban', $user) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-success" 
                                                            title="Unban User">
                                                        <i class="bi bi-person-check"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                            
                                            @can('delete', $user)
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $user->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endcan
                                        @else
                                            <form action="{{ route('admin.users.restore', $user->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-warning" 
                                                        title="Restore User">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#forceDeleteModal{{ $user->id }}">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endunless
                                    </div>

                                    <!-- Ban Modal -->
                                    @can('ban', $user)
                                        <div class="modal fade" id="banModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title">Ban User</h5>
                                                            <button type="button" class="btn-close" style="margin-right : 0;" data-bs-dismiss="modal"></button>
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
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning">
                                                            <h5 class="modal-title">Move to Trash</h5>
                                                            <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal"></button>
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

                                    <!-- Force Delete Modal -->
                                    <div class="modal fade" id="forceDeleteModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Permanent Delete</h5>
                                                        <button type="button" class="btn-close" style="margin-right: 0;" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-danger"><strong>Warning:</strong> This will permanently delete <strong>{{ $user->name }}</strong> and all data. This action cannot be undone.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Forever</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox display-4 text-muted"></i>
                                    <p class="mt-2 text-muted">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.user-management .role-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    display: inline-block;
}
.role-admin { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
.role-editor { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
.role-author { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
.role-user { background: linear-gradient(135deg, #64748b, #475569); color: white; }

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}
.status-active { background: #dcfce7; color: #166534; }
.status-banned { background: #fee2e2; color: #991b1b; }
.status-deleted { background: #e5e7eb; color: #374151; }

/* Table container with fixed height and vertical scroll */
.user-table-container {
    max-height: 500px;
    overflow-y: auto;
    /* Optional: Add a subtle border to indicate the scrollable area */
    border-bottom: 1px solid #dee2e6;
}

/* Optional: Style the scrollbar for Webkit browsers */
.user-table-container::-webkit-scrollbar {
    width: 8px;
}

.user-table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.user-table-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.user-table-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Ensure the table header stays visible when scrolling */
.user-table-container thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}
</style>
@endpush
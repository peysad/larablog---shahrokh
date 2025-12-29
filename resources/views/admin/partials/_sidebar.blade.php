<div class="admin-sidebar">
    <h5 class="text-white mb-3">
        <i class="bi bi-menu-button-wide"></i> Navigation
    </h5>
    
    <ul class="nav nav-pills flex-column">
        
        {{-- Dashboard: Admin Only --}}
        @role('Admin')
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        @endrole

        {{-- Posts: Admin & Editor --}}
        <li class="nav-item">
            <a href="{{ route('admin.posts.index') }}" 
               class="nav-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                <i class="bi bi-file-text"></i> Posts
                <span class="badge bg-light text-dark ms-auto">
                    {{ \App\Models\Post::count() }}
                </span>
            </a>
        </li>

        {{-- Categories: Admin Only --}}
        @role('Admin')
        <li class="nav-item">
            <a href="{{ route('admin.categories.index') }}" 
               class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="bi bi-folder"></i> Categories
                <span class="badge bg-light text-dark ms-auto">
                    {{ \App\Models\Category::count() }}
                </span>
            </a>
        </li>
        @endrole

        {{-- Tags: Admin Only --}}
        @role('Admin')
        <li class="nav-item">
            <a href="{{ route('admin.tags.index') }}" 
               class="nav-link {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Tags
                <span class="badge bg-light text-dark ms-auto">
                    {{ \App\Models\Tag::count() }}
                </span>
            </a>
        </li>
        @endrole

        {{-- Comments: Admin Only --}}
        @role('Admin')
        <li class="nav-item">
            <a href="{{ route('admin.comments.pending') }}" 
               class="nav-link {{ request()->routeIs('admin.comments.pending') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Comments
                @php $pending = \App\Models\Comment::where('approved', false)->count(); @endphp
                @if($pending > 0)
                    <span class="badge bg-warning text-dark ms-auto">
                        {{ $pending }}
                    </span>
                @endif
            </a>
        </li>
        @endrole

        {{-- Users: Admin Only --}}
        @role('Admin')
        @can('manage users')
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" 
               class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Users
                <span class="badge bg-light text-dark ms-auto">
                    {{ \App\Models\User::count() }}
                </span>
            </a>
        </li>
        @endcan
        @endrole

        <li class="nav-item mt-3">
            <hr class="bg-white-50">
            <a href="{{ route('home') }}" class="nav-link">
                <i class="bi bi-box-arrow-up-right"></i> View Site
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link text-end w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
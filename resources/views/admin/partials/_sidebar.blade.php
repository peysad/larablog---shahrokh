<div class="admin-sidebar h-100">
    
    <!-- Mobile Close Button (Only visible on mobile/tablet) -->
    <button class="btn btn-link text-white d-lg-none w-100 text-start mb-3 p-0" id="sidebarCloseBtn" style="font-size: 1.5rem;">
        <i class="bi bi-x-lg"></i>
    </button>

    {{-- 
        Mobile & Tablet Only User Profile Section 
        Displayed here since the header profile was removed.
    --}}
    <div class="d-lg-none sidebar-mobile-profile mb-4 p-3 rounded-3" style="background-color: rgba(255,255,255,0.05);">
        <div class="d-flex align-items-center">
            <img src="{{ auth()->user()->avatar_url }}" 
                 alt="{{ auth()->user()->name }}" 
                 class="rounded-circle border border-2 border-light"
                 width="48" height="48" 
                 style="object-fit: cover;">
            
            <div class="ms-3 flex-grow-1">
                <h6 class="text-white m-0 fw-bold">{{ auth()->user()->name }}</h6>
                <small class="text-white-50">{{ auth()->user()->email }}</small>
            </div>
        </div>
        
        <div class="mt-3 d-grid gap-2">
            <a href="{{ route('author.show', auth()->user()) }}" class="btn btn-outline-light btn-sm text-start">
                <i class="bi bi-person me-2"></i> My Profile
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm text-start">
                <i class="bi bi-speedometer2 me-2"></i> User Dashboard
            </a>
        </div>
    </div>

    <h5 class="text-white mb-3 d-none d-lg-block">
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
            
            {{-- Logout Form --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link text-start w-100">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>
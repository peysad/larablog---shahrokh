<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    
    <title>@yield('title', 'LaraBlog') - Laravel Blog Platform</title>
    
    <!-- Bootstrap 5.3 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/sidebar.css', 'resources/css/post.css', 'resources/css/dashboard.css'])
    
    <!-- Additional Styles Stack -->
    @stack('styles')
</head>
<body style="background-color: var(--background);">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: var(--sidebar);">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-laptop"></i> LaraBlog
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Navigation Links -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-medium {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                            <i class="bi bi-file-text"></i> Posts
                        </a>
                    </li>
                    
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        {{-- FIXED: Split Admin/Editor links based on strict routing --}}
                        
                        {{-- Admins go to full dashboard --}}
                        @role('Admin')
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-check"></i> Admin Panel
                                </a>
                            </li>
                        @endrole

                        {{-- Editors go directly to Post Manager since dashboard is restricted --}}
                        @role('Editor')
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold {{ request()->routeIs('admin.posts*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                                    <i class="bi bi-pencil-square"></i> Post Manager
                                </a>
                            </li>
                        @endrole
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                                <span class="me-2" style="margin-right: 8px;">{{ auth()->user()->name }}</span>
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="rounded-circle border border-2 border-white" 
                                     width="35" height="35" 
                                     style="object-fit: cover;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-start">
                                {{-- Role-conditional navigation --}}
                                @if(auth()->user()->hasRole(['Admin', 'Editor', 'Author']))
                                    {{-- Content creators get full profile access --}}
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('author.show') ? 'active' : '' }}" 
                                           href="{{ route('author.show', auth()->user()) }}">
                                            <i class="bi bi-person-circle"></i> Public Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('author.edit') ? 'active' : '' }}" 
                                           href="{{ route('author.edit') }}">
                                            <i class="bi bi-gear"></i> Advanced Settings
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                @else
                                    {{-- Regular users get simplified dashboard link --}}
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                                           href="{{ route('dashboard') }}">
                                            <i class="bi bi-person-circle"></i> My Account
                                        </a>
                                    </li>
                                @endif
                                
                                @can('create', App\Models\Post::class)
                                    <li>
                                        <a class="dropdown-item {{ request()->routeIs('posts.create') ? 'active' : '' }}" 
                                           href="{{ route('posts.create') }}">
                                            <i class="bi bi-plus-circle"></i> Create Post
                                        </a>
                                    </li>
                                @endcan
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid px-4 py-4">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5 shadow-sm" style="background-color: var(--sidebar); color: white;">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-c-circle me-1"></i> 2025 LaraBlog. All rights reserved. 
                Built with <i class="bi bi-heart-fill text-danger mx-1"></i> and Laravel
            </p>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>
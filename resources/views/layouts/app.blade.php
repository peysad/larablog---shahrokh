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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
                    aria-controls="navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Form -->
                <!-- FIX: Changed action to route('posts.index') and name to 'search' -->
                <form class="d-flex ms-auto me-3" action="{{ route('posts.index') }}" method="GET" style="width: 300px;">
                    <div class="input-group">
                        <input type="search" class="form-control form-control-sm" name="search" 
                               placeholder="Search posts..." aria-label="Search" value="{{ request('search') }}">
                        <button class="btn btn-outline-light btn-sm" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

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
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        @can('view-admin-panel')
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-shield-check"></i> Admin Panel
                                </a>
                            </li>
                        @endcan

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-gear"></i> Profile Settings
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
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
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5 shadow-sm" style="background-color: var(--sidebar); color: white;">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-c-circle"></i> 2025 LaraBlog. All rights reserved. 
                Built with <i class="bi bi-heart-fill text-danger"></i> and Laravel
            </p>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    @stack('scripts')
</body>
</html>
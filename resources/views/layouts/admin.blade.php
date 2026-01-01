<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - LaraBlog Admin</title>
    
    <!-- Bootstrap 5.3 Standard (LTR) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    @vite('resources/css/admin.css')
    
    <!-- Additional Styles Stack -->
    @stack('styles')
</head>
<body class="admin-body" style="background-color: var(--admin-bg);">

    <!-- Sidebar Overlay (Mobile Background) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Admin Topbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--admin-topbar); z-index: 1020;">
        <div class="container-fluid px-4">
            
            <!-- Sidebar Toggle Button (Visible ONLY on Mobile/Tablet - Hidden on Desktop) -->
            <button class="btn btn-link text-white order-lg-1 me-2 d-lg-none" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-4"></i>
            </button>

            {{-- Conditional Logo Link based on Role --}}
            @if(auth()->user()->hasRole('Admin'))
                <a class="navbar-brand fw-bold order-lg-2" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-shield-check"></i> LaraBlog Admin
                </a>
            @elseif(auth()->user()->hasRole('Editor'))
                <a class="navbar-brand fw-bold order-lg-2" href="{{ route('admin.posts.index') }}">
                    <i class="bi bi-pencil-square"></i> Post Manager
                </a>
            @else
                <a class="navbar-brand fw-bold order-lg-2" href="{{ route('dashboard') }}">
                    <i class="bi bi-person-circle"></i> User Panel
                </a>
            @endif
            
            {{-- 
                Desktop Profile Section 
                Visible ONLY on Desktop (d-none d-lg-block)
                Hidden on Mobile & Tablet
            --}}
            <div class="d-none d-lg-block ms-auto order-lg-3">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle ms-2" 
                                 width="30" height="30" 
                                 style="object-fit: cover;">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('author.show', auth()->user()) }}">
                                    <i class="bi bi-person"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> User Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <div class="row">
            <!-- Admin Sidebar -->
            <div class="col-lg-3 col-xl-2 mb-4 sidebar-wrapper">
                @include('admin.partials._sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
                
                {{-- MODERN FLOATING FLASH MESSAGES --}}
                <div class="flash-container">
                    @if(session('success'))
                        <div class="flash-card flash-success">
                            <div class="flash-inner">
                                <div class="flash-icon">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div class="flash-text">
                                    {{ session('success') }}
                                </div>
                                <button type="button" class="flash-close" onclick="this.closest('.flash-card').remove()">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="flash-card flash-error">
                            <div class="flash-inner">
                                <div class="flash-icon">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div class="flash-text">
                                    {{ session('error') }}
                                </div>
                                <button type="button" class="flash-close" onclick="this.closest('.flash-card').remove()">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="flash-card flash-error">
                            <div class="flash-inner">
                                <div class="flash-icon">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                </div>
                                <div class="flash-text">
                                    Please fix the following errors:
                                    <ul class="mb-0 mt-2 small">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="flash-close" onclick="this.closest('.flash-card').remove()">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Breadcrumbs -->
                @yield('breadcrumbs')

                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">
                        @yield('page-title', 'Dashboard')
                    </h2>
                    @yield('page-actions')
                </div>

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 mt-4" style="background-color: var(--admin-topbar); color: white;">
        <div class="container">
            <p class="mb-0">
                &copy; {{ date('Y') }} LaraBlog Admin Panel | 
                <small>Laravel v{{ app()->version() }}</small>
            </p>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    @vite('resources/js/admin.js')
    @vite('resources/js/app.js')
    <!-- Stack for additional scripts -->
    @stack('scripts')
</body>
</html>
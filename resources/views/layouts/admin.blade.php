<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - LaraBlog Admin</title>
    
    <!-- Bootstrap 5.3 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom Admin Styles -->
    @vite('resources/css/admin.css')
    
    <!-- Additional Styles Stack -->
    @stack('styles')
</head>
<body style="background-color: var(--admin-bg);">

    <!-- Admin Topbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--admin-topbar);">
        <div class="container-fluid px-4">
            {{-- FIXED: Conditional Logo Link based on Role --}}
            @if(auth()->user()->hasRole('Admin'))
                <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-shield-check"></i> LaraBlog Admin
                </a>
            @elseif(auth()->user()->hasRole('Editor'))
                <a class="navbar-brand fw-bold" href="{{ route('admin.posts.index') }}">
                    <i class="bi bi-pencil-square"></i> Post Manager
                </a>
            @else
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                    <i class="bi bi-person-circle"></i> User Panel
                </a>
            @endif
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> View Site
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                 alt="{{ auth()->user()->name }}" 
                                 class="rounded-circle me-1" 
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
            <div class="col-lg-3 col-xl-2 mb-4">
                @include('admin.partials._sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-xl-10">
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

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi bi-exclamation-circle"></i> Please fix the following errors:
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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
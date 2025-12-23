@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" 
                             class="rounded-circle" width="80" height="80" 
                             style="object-fit: cover; border: 3px solid var(--primary);">
                    </div>
                    <div class="flex-grow-1 ms-4">
                        <h1 class="h3 mb-1 fw-bold">Welcome, {{ auth()->user()->name }}!</h1>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope"></i> {{ auth()->user()->email }}
                        </p>
                        <p class="mb-0">
                            <span class="badge bg-primary">
                                <i class="bi bi-person-badge"></i> 
                                {{ auth()->user()->roles->pluck('name')->join(', ') }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Statistics Cards Section REMOVED (Will be added back after Post implementation) -->

                <!-- Quick Actions -->
                <div class="row mt-5">
                    <div class="col-12">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-lightning"></i> Quick Actions
                        </h5>
                        <div class="btn-group flex-wrap gap-2">
                            <!-- Write New Post Button REMOVED -->
                            
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="bi bi-person"></i> Edit Profile
                            </a>

                            @can('view-admin-panel')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-warning text-dark">
                                    <i class="bi bi-shield-check"></i> Admin Panel
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Admin Notice -->
                @can('view-admin-panel')
                    <div class="alert alert-warning mt-5" role="alert">
                        <h6 class="alert-heading">
                            <i class="bi bi-shield-exclamation"></i> Administrative Access
                        </h6>
                        <p class="mb-0">You have administrative privileges. Please use them responsibly.</p>
                    </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
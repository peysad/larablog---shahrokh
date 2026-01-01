@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('content')
<div class="admin-dashboard">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-speedometer2"></i>
            <span>Admin Dashboard</span>
        </h1>
        <p class="page-subtitle">Monitor and manage your blog content and settings</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Total Posts Card -->
        <div class="stat-card stat-card--primary" data-stat-type="posts">
            <div class="stat-card__header">
                <div class="stat-card__content">
                    <h3 class="stat-card__title">Total Posts</h3>
                    <h2 class="stat-card__value">{{ \App\Models\Post::count() }}</h2>
                </div>
                <div class="stat-card__icon">
                    <i class="bi bi-file-text"></i>
                </div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.posts.index') }}" class="stat-card__action">
                    Manage Posts
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Categories Card -->
        <div class="stat-card stat-card--secondary" data-stat-type="categories">
            <div class="stat-card__header">
                <div class="stat-card__content">
                    <h3 class="stat-card__title">Categories</h3>
                    <h2 class="stat-card__value">{{ \App\Models\Category::count() }}</h2>
                </div>
                <div class="stat-card__icon">
                    <i class="bi bi-folder"></i>
                </div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.categories.index') }}" class="stat-card__action">
                    Manage Categories
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Tags Card -->
        <div class="stat-card stat-card--accent" data-stat-type="tags">
            <div class="stat-card__header">
                <div class="stat-card__content">
                    <h3 class="stat-card__title">Tags</h3>
                    <h2 class="stat-card__value">{{ \App\Models\Tag::count() }}</h2>
                </div>
                <div class="stat-card__icon">
                    <i class="bi bi-tags"></i>
                </div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.tags.index') }}" class="stat-card__action">
                    Manage Tags
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Users Card -->
        <div class="stat-card stat-card--success" data-stat-type="users">
            <div class="stat-card__header">
                <div class="stat-card__content">
                    <h3 class="stat-card__title">Users</h3>
                    <h2 class="stat-card__value">{{ \App\Models\User::count() }}</h2>
                </div>
                <div class="stat-card__icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="stat-card__footer">
                <a href="{{ route('admin.users.index') }}" class="stat-card__action">
                    Manage Users
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects and interactions
    const statCards = document.querySelectorAll('.stat-card');
    
    statCards.forEach(card => {
        // Add keyboard navigation support
        card.setAttribute('tabindex', '0');
        
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const actionLink = this.querySelector('.stat-card__action');
                if (actionLink) {
                    actionLink.click();
                }
            }
        });
    });

    // Add loading state simulation (optional)
    function simulateLoading() {
        const loadingCard = document.querySelector('.stat-card');
        if (loadingCard) {
            loadingCard.classList.add('loading');
            setTimeout(() => {
                loadingCard.classList.remove('loading');
            }, 2000);
        }
    }

    // Uncomment to test loading states
    // setTimeout(simulateLoading, 1000);
});

// Utility functions for dashboard updates
const DashboardStats = {
    updateStat: function(statType, newValue) {
        const card = document.querySelector(`[data-stat-type="${statType}"] .stat-card__value`);
        if (card) {
            // Add animation effect
            card.style.transform = 'scale(1.1)';
            card.style.color = 'var(--admin-primary)';
            
            setTimeout(() => {
                card.textContent = newValue;
                card.style.transform = 'scale(1)';
                card.style.color = 'var(--admin-text)';
            }, 150);
        }
    },
    
    refreshStats: function() {
        // Example: Fetch updated stats from API
        fetch('/api/admin/stats')
            .then(response => response.json())
            .then(data => {
                Object.keys(data).forEach(key => {
                    this.updateStat(key, data[key]);
                });
            })
            .catch(error => console.error('Error fetching stats:', error));
    }
};

// Auto-refresh stats every 5 minutes (optional)
// setInterval(DashboardStats.refreshStats, 300000);
</script>
@endsection
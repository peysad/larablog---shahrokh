<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Post, Comment, User, Category, Tag};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        Gate::authorize('view-admin-panel');

        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'draft_posts' => Post::draft()->count(),
            'pending_comments' => Comment::where('approved', false)->count(),
            'total_comments' => Comment::count(),
            'total_users' => User::count(),
            'active_users' => User::whereHas('posts')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'recent_posts' => Post::with(['author'])->latest()->take(5)->get(),
            'recent_comments' => Comment::with(['commentable', 'author'])->latest()->take(10)->get(),
            'top_authors' => User::withCount(['posts' => fn($q) => $q->published()])->orderByDesc('posts_count')->take(5)->get(),
        ];

        $chartData = $this->getChartData();

        return view('admin.dashboard', compact('stats', 'chartData'));
    }

    /**
     * Get data for charts.
     */
    protected function getChartData(): array
    {
        // Posts by month (last 6 months)
        $postsByMonth = Post::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Comments by month
        $commentsByMonth = Comment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('Y-m');
        })->reverse()->values()->toArray();

        return [
            'months' => array_map(function ($m) {
                return \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y');
            }, $months),
            'posts' => array_map(fn($m) => $postsByMonth[$m] ?? 0, $months),
            'comments' => array_map(fn($m) => $commentsByMonth[$m] ?? 0, $months),
        ];
    }
}
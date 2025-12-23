<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'posts_count' => $user->posts()->count(),
            'published_posts' => $user->posts()->where('status', 'published')->count(),
            'draft_posts' => $user->posts()->where('status', 'draft')->count(),
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
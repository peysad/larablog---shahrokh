<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController,
    ResetPasswordController
};
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\TagAdminController;
use App\Http\Controllers\Admin\CommentAdminController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\PostAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// ==== Guest Routes (Unauthenticated) ====
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// ==== Public Routes ====
Route::get('/', fn() => redirect()->route('posts.index'))->name('home');

// ==== Authors Directory ====
// Public access to list Admins, Editors, and Authors
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');

// ==== Author Routes (Profile for Content Creators Only) ====
Route::get('author/{user}', [AuthorController::class, 'show'])->name('author.show');
Route::middleware(['auth', 'role:Admin|Editor|Author'])->group(function () {
    Route::get('profile/edit', [AuthorController::class, 'edit'])->name('author.edit');
    Route::put('profile', [AuthorController::class, 'update'])->name('author.update');
});

// ==== User Dashboard Routes ====
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.update');
});

// ==== Public Routes for Filtering (Categories & Tags) ====
Route::get('categories/{category:slug}', function (\App\Models\Category $category) {
    $posts = $category->posts()
        ->with(['author', 'categories', 'tags'])
        ->published()
        ->latest('published_at')
        ->paginate(12);

    return view('posts.index', [
        'posts' => $posts,
        'filter' => "Category: {$category->name}"
    ]);
})->name('categories.show');

Route::get('tags/{tag:slug}', function (\App\Models\Tag $tag) {
    $posts = $tag->posts()
        ->with(['author', 'categories', 'tags'])
        ->published()
        ->latest('published_at')
        ->paginate(12);

    return view('posts.index', [
        'posts' => $posts,
        'filter' => "Tag: #{$tag->name}"
    ]);
})->name('tags.show');

// ==== Admin Panel Routes (Split by Role) ====

// GROUP 1: Strictly Admin Only Routes
// Editors are denied access at the middleware level
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('users', [UserAdminController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserAdminController::class, 'show'])->name('users.show');
        Route::put('users/{user}/role', [UserAdminController::class, 'updateRole'])->name('users.updateRole');
        Route::post('users/{user}/ban', [UserAdminController::class, 'ban'])->name('users.ban');
        Route::post('users/{user}/unban', [UserAdminController::class, 'unban'])->name('users.unban');
        Route::delete('users/{user}', [UserAdminController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{id}/restore', [UserAdminController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserAdminController::class, 'forceDelete'])->name('users.forceDelete');

        // Category Management
        Route::resource('categories', CategoryAdminController::class)
            ->except(['show'])
            ->middleware('can:manage categories');

        // Tag Management
        Route::resource('tags', TagAdminController::class)
            ->except(['show'])
            ->middleware('can:manage tags');

        // Comment Management
        Route::get('comments', [CommentAdminController::class, 'index'])->name('comments.index');        
        Route::get('comments/pending', [CommentAdminController::class, 'pending'])->name('comments.pending');
        Route::post('comments/{comment}/approve', [CommentAdminController::class, 'approve'])->name('comments.approve');
        Route::post('comments/{comment}/reject', [CommentAdminController::class, 'reject'])->name('comments.reject');
        Route::delete('comments/{comment}', [CommentAdminController::class, 'destroy'])->name('comments.destroy');
    });

// GROUP 2: Admin & Editor Routes (Posts Only)
// Editors only have access to this section
Route::middleware(['auth', 'role:Admin|Editor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Post Management
        Route::resource('posts', PostAdminController::class)->except(['create', 'store']);
        
        // Bulk Actions
        Route::post('posts/bulk-action', [PostAdminController::class, 'bulkAction'])->name('posts.bulk-action');
        Route::post('posts/{id}/restore', [PostAdminController::class, 'restore'])->name('posts.restore');
        Route::delete('posts/{id}/force-delete', [PostAdminController::class, 'forceDelete'])->name('posts.forceDelete');
    });

// ==== Authenticated Routes (User Side) ====
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', PostController::class)->except(['index', 'show']);
    
    Route::post('comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::resource('posts.comments', CommentController::class)->only(['store']);
});

// ==== Post Resource Routes (Public) ====
Route::resource('posts', PostController::class)->only(['index', 'show']);
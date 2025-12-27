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

// ==== Authenticated Routes ====
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ==== Public Routes for Filtering (Categories & Tags) ====
// Category Posts
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

// Tag Posts
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

// ==== Admin Panel Routes ====
Route::middleware(['auth', 'role:Admin|Editor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Category Management
        Route::resource('categories', CategoryAdminController::class)
            ->except(['show'])
            ->middleware('can:manage categories');

        // Tag Management
        Route::resource('tags', TagAdminController::class)
            ->except(['show'])
            ->middleware('can:manage tags');        
        Route::get('comments', [CommentAdminController::class, 'index'])->name('comments.index');        
        Route::get('comments/pending', [CommentAdminController::class, 'pending'])->name('comments.pending');
        Route::post('comments/{comment}/approve', [CommentAdminController::class, 'approve'])->name('comments.approve');
        Route::post('comments/{comment}/reject', [CommentAdminController::class, 'reject'])->name('comments.reject');
        Route::delete('comments/{comment}', [CommentAdminController::class, 'destroy'])->name('comments.destroy');
    });

// ==== Author/Editor/Admin Post Management Routes ====
// Only users with Admin, Editor, or Author roles can create, edit, delete posts
Route::middleware(['auth', 'role:Admin|Editor|Author'])
    ->prefix('posts')
    ->name('posts.')
    ->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::patch('/{post}', [PostController::class, 'update'])->name('update.patch');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');

        Route::patch('/{post}/publish', [PostController::class, 'publish'])
             ->name('publish');
        
        Route::patch('/{post}/toggle-status', [PostController::class, 'toggleStatus'])
             ->name('toggle-status');
    });

// ==== Post Resource Routes (Public) ====
Route::resource('posts', PostController::class)->only(['index', 'show']);

Route::post('comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
Route::resource('posts.comments', CommentController::class)->only(['store']);

// ==== Public Routes ====
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');
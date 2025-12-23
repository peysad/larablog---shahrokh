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
// use App\Http\Middleware\CheckRole; // Not strictly needed if using 'role' middleware alias

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

// ==== Admin Panel Routes ====
Route::middleware(['auth', 'role:Admin|Editor'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
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

// ==== Public Routes ====
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');
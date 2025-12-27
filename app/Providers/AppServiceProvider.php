<?php

namespace App\Providers;

use App\Services\ImageService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind ImageService with logger
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService(Log::channel('daily'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create required directories if they don't exist
        $sizes = config('image.sizes', []);
        $folders = ['posts', 'users', 'categories'];
        
        foreach ($folders as $folder) {
            foreach ($sizes as $sizeName => $config) {
                $path = storage_path("app/public/{$folder}/{$sizeName}");
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
            }
        }
    }
}
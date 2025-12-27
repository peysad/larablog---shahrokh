<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageDirectories extends Command
{
    protected $signature = 'storage:mkdirs';
    protected $description = 'Create required storage directories';

    public function handle()
    {
        $sizes = config('image.sizes', []);
        $folders = ['posts', 'users', 'categories', 'tags'];
        
        foreach ($folders as $folder) {
            foreach ($sizes as $sizeName => $config) {
                $path = storage_path("app/public/{$folder}/{$sizeName}");
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true);
                    $this->info("Created: {$path}");
                }
            }
        }
        
        $this->info('All storage directories created successfully!');
    }
}
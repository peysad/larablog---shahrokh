<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Psr\Log\LoggerInterface;

class ImageService
{
    protected ImageManager $manager;
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->manager = new ImageManager(new Driver());
        $this->logger = $logger;
    }

    /**
     * Store an image with multiple thumbnail sizes.
     * 
     * @param UploadedFile $file The uploaded file
     * @param string $folder Base folder name (e.g., "posts", "users")
     * @param array $sizes Sizes to generate (overrides config)
     * @return string Path to original image (e.g., "posts/original/filename.jpg")
     */
    public function storeImage(UploadedFile $file, string $folder, array $sizes = []): string
    {
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        
        // Use configured sizes if not provided
        if (empty($sizes)) {
            $sizes = config('image.sizes', []);
        }

        $disk = config('image.disk', 'public');

        // Store original image
        $originalPath = $this->storeOriginal($file, $folder, $filename, $disk);

        // Generate thumbnails
        $this->generateThumbnails($file, $folder, $filename, $sizes, $disk);

        $this->logger->info('Image stored successfully', [
            'folder' => $folder,
            'filename' => $filename,
            'sizes' => array_keys($sizes),
            'disk' => $disk,
        ]);

        return $originalPath;
    }

    /**
     * Delete an image and all its thumbnails.
     * 
     * @param string|null $path Path to original image
     */
    public function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = config('image.disk', 'public');
        
        // Parse path to get folder and filename
        $parts = explode('/', $path);
        $filename = array_pop($parts);
        array_pop($parts);
        $folder = implode('/', $parts);

        // Delete original
        Storage::disk($disk)->delete($path);

        // Delete thumbnails
        $sizes = config('image.sizes', []);
        foreach ($sizes as $sizeName => $config) {
            $thumbPath = "{$folder}/{$sizeName}/{$filename}";
            Storage::disk($disk)->delete($thumbPath);
        }

        $this->logger->info('Image deleted', [
            'path' => $path,
            'disk' => $disk,
        ]);
    }

    /**
     * Update an existing image (delete old, store new).
     * 
     * @param UploadedFile $newFile
     * @param string|null $oldPath
     * @param string $folder
     * @param array $sizes
     * @return string New image path
     */
    public function updateImage(UploadedFile $newFile, ?string $oldPath, string $folder, array $sizes = []): string
    {
        // Delete old image if exists
        if ($oldPath) {
            $this->deleteImage($oldPath);
        }

        // Store new image
        return $this->storeImage($newFile, $folder, $sizes);
    }

    /**
     * Generate a unique filename.
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $hash = hash('sha256', $file->getClientOriginalName() . microtime() . uniqid('', true));
        $extension = $file->getClientOriginalExtension();
        
        return Str::random(8) . "_{$hash}." . $extension;
    }

    /**
     * Store the original image.
     */
    protected function storeOriginal(UploadedFile $file, string $folder, string $filename, string $disk): string
    {
        $path = "{$folder}/original/{$filename}";
        
        // For large files, use stream for better memory management
        Storage::disk($disk)->putFileAs("{$folder}/original", $file, $filename);
        
        return $path;
    }

    /**
     * Generate thumbnails for different sizes.
     */
    protected function generateThumbnails(UploadedFile $file, string $folder, string $filename, array $sizes, string $disk): void
    {
        foreach ($sizes as $sizeName => $config) {
            try {
                $image = $this->manager->read($file->getRealPath());

                $width = $config['width'];
                $height = $config['height'];
                $quality = $config['quality'] ?? config('image.quality', 85);
                $fit = $config['fit'] ?? true;

                if ($fit) {
                    // Crop to exact dimensions
                    $image->cover($width, $height);
                } else {
                    // Resize maintaining aspect ratio
                    $image->scale($width, $height);
                }

                $thumbPath = "{$folder}/{$sizeName}/{$filename}";
                $encodedImage = $image->toJpeg(quality: $quality);

                Storage::disk($disk)->put($thumbPath, $encodedImage);

                $this->logger->debug('Thumbnail generated', [
                    'size' => $sizeName,
                    'dimensions' => "{$width}x{$height}",
                    'quality' => $quality,
                ]);

            } catch (\Exception $e) {
                $this->logger->error('Thumbnail generation failed', [
                    'size' => $sizeName,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Disk
    |--------------------------------------------------------------------------
    |
    | The storage disk to use for storing images.
    |
    */
    'disk' => env('IMAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Default Quality
    |--------------------------------------------------------------------------
    |
    | The default quality for saved images (0-100).
    |
    */
    'quality' => (int) env('IMAGE_QUALITY', 85),

    /*
    |--------------------------------------------------------------------------
    | Image Sizes
    |--------------------------------------------------------------------------
    |
    | Define sizes for thumbnails.
    | 'fit' => true will crop the image to exact dimensions (cover).
    | 'fit' => false will resize maintaining aspect ratio (scale).
    |
    */
    'sizes' => [
        'social' => [
            'width' => 1200,
            'height' => 630,
            'quality' => 90,
            'fit' => true,
        ],
        'card' => [
            'width' => 400,
            'height' => 300,
            'quality' => 80,
            'fit' => true,
        ],
        'thumbnail' => [
            'width' => 150,
            'height' => 150,
            'quality' => 75,
            'fit' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Optimization
    |--------------------------------------------------------------------------
    |
    | Whether to attempt to optimize images (requires extra packages usually).
    | For now, we just use quality settings.
    |
    */
    'optimize' => true,
];
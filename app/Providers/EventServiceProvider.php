<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\CommentPosted;
use App\Listeners\SendCommentNotification;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        CommentPosted::class => [
            SendCommentNotification::class,
        ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

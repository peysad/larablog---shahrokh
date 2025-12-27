<?php

namespace App\Listeners;

use App\Events\CommentPosted;
use App\Notifications\NewCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendCommentNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(CommentPosted $event): void
    {
        $comment = $event->comment;
        $post = $comment->commentable;

        // Don't notify if comment author is post author
        if ($comment->user_id === $post->user_id) {
            Log::info('Skipping notification - comment author is post author', [
                'comment_id' => $comment->id,
                'post_id' => $post->id,
            ]);
            return;
        }

        // Send notification to post author
        $post->author->notify(
            new NewCommentNotification($comment)
        );

        Log::info('Comment notification sent', [
            'comment_id' => $comment->id,
            'post_id' => $post->id,
            'notified_user_id' => $post->user_id,
        ]);
    }

    /**
     * Determine whether the listener should be queued.
     */
    public function shouldQueue(CommentPosted $event): bool
    {
        // Only queue if comment is approved and has an author
        return $event->comment->approved && $event->comment->commentable->author;
    }
}
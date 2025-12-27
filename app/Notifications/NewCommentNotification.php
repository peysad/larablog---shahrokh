<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Comment $comment
    ) {
        $this->comment->load(['commentable', 'author']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $post = $this->comment->commentable;
        $commenter = $this->comment->display_name;

        return (new MailMessage)
            ->subject('New Comment on Your Post: ' . $post->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($commenter . ' has commented on your post:')
            ->line(new HtmlString('<strong>' . $post->title . '</strong>'))
            ->line(new HtmlString('<blockquote>' . Str::limit($this->comment->body, 200) . '</blockquote>'))
            ->action('View Comment', $post->url . '#comment-' . $this->comment->id)
            ->line('You can manage comments in your admin panel:')
            ->action('Manage Comments', url('/admin/comments/pending'))
            ->line('Keep up the great work!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->commentable_id,
            'post_title' => $this->comment->commentable->title,
            'commenter_name' => $this->comment->display_name,
            'comment_body' => Str::limit($this->comment->body, 100),
            'url' => $this->comment->commentable->url . '#comment-' . $this->comment->id,
        ];
    }
}
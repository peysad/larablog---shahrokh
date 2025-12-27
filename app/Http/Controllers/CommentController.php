<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\{Comment, Post};
use App\Events\CommentPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate, Log};

class CommentController extends Controller
{
    /**
     * Store a new comment.
     */
    public function store(CommentRequest $request, Post $post)
    {
        // Check if post allows comments
        if (!$post->allow_comments ?? true) {
            return redirect()->back()->with('error', 'Comments are disabled for this post.');
        }

        $comment = DB::transaction(function () use ($request, $post) {
            $data = $request->getCommentData();
            $data['commentable_id'] = $post->id;
            $data['commentable_type'] = Post::class;
            
            $comment = Comment::create($data);

            // Fire event for notifications
            if ($comment->approved) {
                event(new CommentPosted($comment));
            } else {
                Log::info('Comment pending moderation', [
                    'comment_id' => $comment->id,
                    'post_id' => $post->id,
                ]);
            }

            return $comment;
        });

        $message = $comment->approved 
            ? 'Comment posted successfully!' 
            : 'Comment submitted and is pending moderation.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Store a reply to a comment.
     */
    public function reply(CommentRequest $request, Comment $comment)
    {
        if (!$comment->approved) {
            return redirect()->back()->with('error', 'Cannot reply to an unapproved comment.');
        }

        $reply = DB::transaction(function () use ($request, $comment) {
            $data = $request->getCommentData();
            $data['commentable_id'] = $comment->commentable_id;
            $data['commentable_type'] = $comment->commentable_type;
            $data['parent_id'] = $comment->id;
            
            $reply = Comment::create($data);

            if ($reply->approved) {
                event(new CommentPosted($reply));
            }

            return $reply;
        });

        $message = $reply->approved 
            ? 'Reply posted successfully!' 
            : 'Reply submitted and is pending moderation.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Approve a comment (Admin/Editor only).
     */
    public function approve(Comment $comment)
    {
        Gate::authorize('approve', $comment);

        if ($comment->approved) {
            return redirect()->back()->with('info', 'Comment is already approved.');
        }

        DB::transaction(function () use ($comment) {
            $comment->approve();

            // Fire event now that it's approved
            event(new CommentPosted($comment));

            Log::info('Comment approved', [
                'comment_id' => $comment->id,
                'approved_by' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Comment approved and published!');
    }

    /**
     * Reject (unapprove) a comment.
     */
    public function reject(Comment $comment)
    {
        Gate::authorize('approve', $comment);

        if (!$comment->approved) {
            return redirect()->back()->with('info', 'Comment is already rejected.');
        }

        $comment->reject();

        Log::info('Comment rejected', [
            'comment_id' => $comment->id,
            'rejected_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Comment rejected and hidden.');
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        DB::transaction(function () use ($comment) {
            // Soft delete all replies
            $comment->replies()->delete();

            // Soft delete the comment
            $comment->delete();

            Log::info('Comment deleted (with replies)', [
                'comment_id' => $comment->id,
                'deleted_by' => auth()->id(),
            ]);
        });

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}
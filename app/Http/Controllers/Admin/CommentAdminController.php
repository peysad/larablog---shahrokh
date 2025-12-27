<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentAdminController extends Controller
{
    /**
     * Display pending comments.
     */
    public function pending()
    {
        Gate::authorize('approve', Comment::class);

        $comments = Comment::where('approved', false)
            ->with(['commentable', 'author'])
            ->latest()
            ->paginate(20);

        return view('admin.comments.pending', compact('comments'));
    }

    /**
     * Display all comments.
     */
    public function index()
    {
        Gate::authorize('viewAny', Comment::class);

        $comments = Comment::with(['commentable', 'author'])
            ->latest()
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment)
    {
        Gate::authorize('approve', $comment);

        $comment->approve();

        return redirect()->back()->with('success', 'Comment approved!');
    }

    /**
     * Reject a comment.
     */
    public function reject(Comment $comment)
    {
        Gate::authorize('approve', $comment);

        $comment->reject();

        return redirect()->back()->with('success', 'Comment rejected!');
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted!');
    }
}
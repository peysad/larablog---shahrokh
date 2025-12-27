<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\{
    Post,
    Category,
    Tag
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Gate, Log};
use Illuminate\Auth\Access\AuthorizationException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Post::query()->with(['author', 'categories', 'tags']);

        // 1. Apply Search (Works independently of role)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // 2. Apply Filters (Category/Tag)
        if ($request->filled('category')) {
            $query->inCategory($request->category);
        }
        if ($request->filled('tag')) {
            $query->withTag($request->tag);
        }

        // 3. Apply Visibility Logic based on Role
        if ($user && $user->hasRole(['Admin', 'Editor'])) {
            // Admins and Editors see ALL posts (Drafts, Published, Scheduled)
            // No additional constraints needed here.
        } elseif ($user && $user->hasRole('Author')) {
            // Authors see their OWN published posts.
            // We intentionally DO NOT use the strict published() scope here because
            // it filters out future-dated (scheduled) posts.
            // Authors need to see their scheduled posts to verify them.
            $query->where('user_id', $user->id)
                  ->where('status', 'published');
        } else {
            // Guests see only strictly published posts (status=published AND date <= now)
            $query->published();
        }

        $posts = $query->latest('published_at')
                       ->paginate(12)
                       ->withQueryString();

        return view('posts.index', compact('posts'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Check if post is published or user can view drafts
        if (!$post->isPublished() && !Gate::allows('view', $post)) {
            abort(403, 'This post is not published yet.');
        }

        // Increment views (use Redis for high-traffic sites)
        $post->incrementViews();

        // Load relationships excluding comments as they are not implemented yet
        $post->load([
            'author', 
            'categories', 
            'tags'
        ]);

        // Load comments as per Phase 3 requirements
        // We fetch top-level comments, with their author and nested replies
        $comments = $post->comments()
            ->with(['author', 'replies']) 
            ->whereNull('parent_id') // Only top level comments
            ->approved()             // Only approved comments
            ->latest()
            ->get();

        return view('posts.show', compact('post', 'comments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Post::class);

        $categories = Category::all(['id', 'name']);
        $tags = Tag::all(['id', 'name']);

        return view('posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        $data = $request->getPostData();

        $post = DB::transaction(function () use ($data, $request) {
            $post = Post::create($data);

            // Sync categories and tags (use sync for consistency)
            $post->categories()->sync($request->categories ?? []);
            $post->tags()->sync($request->tags ?? []);

            Log::info('Post created successfully', [
                'post_id' => $post->id,
                'author_id' => $post->user_id,
                'title' => $post->title,
                'status' => $post->status
            ]);

            return $post; // Return from transaction
        });

        $message = $post->status === 'published' 
            ? 'Post published successfully!' 
            : 'Draft saved successfully!';

        return redirect()->route('posts.show', $post->slug)
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::all(['id', 'name']);
        $tags = Tag::all(['id', 'name']);

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        $data = $request->getPostData();

        DB::transaction(function () use ($data, $request, $post) {
            
            $post->update($data);

            // Sync categories and tags
            $post->categories()->sync($request->categories ?? []);
            $post->tags()->sync($request->tags ?? []);

            Log::info('Post updated successfully', [
                'post_id' => $post->id,
                'author_id' => $post->user_id,
                'title' => $post->title,
                'status' => $post->status
            ]);
        });

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        DB::transaction(function () use ($post) {
            // Soft delete the post (relationships will be handled by database constraints)
            $post->delete();

            Log::info('Post deleted (soft delete)', [
                'post_id' => $post->id,
                'title' => $post->title,
            ]);
        });

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Publish a draft post.
     */
    public function publish(Post $post)
    {
        Gate::authorize('publish', $post);

        if ($post->status === 'published') {
            return redirect()->back()->with('error', 'Post is already published.');
        }

        $post->publish();

        Log::info('Post published', [
            'post_id' => $post->id,
            'published_by' => Auth::id(),
        ]);

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Post published successfully!');
    }

    /**
     * Toggle post status between draft and published.
     */
    public function toggleStatus(Post $post)
    {
        Gate::authorize('publish', $post);

        $wasPublished = $post->status === 'published';
        
        $wasPublished ? $post->unpublish() : $post->publish();

        $message = $wasPublished 
            ? 'Post converted to draft!' 
            : 'Post published successfully!';

        Log::info('Post status toggled', [
            'post_id' => $post->id,
            'new_status' => $post->status,
            'changed_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', $message);
    }

    // deletePostImages method removed as discussed
}
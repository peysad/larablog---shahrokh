<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\{
    Post,
    Category,
    Tag
};
use App\Services\ImageService;
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
        
        $query = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->withCount(['comments' => function ($query) {
                $query->approved();
            }]);

        // 1. Apply Search
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
            // Admins and Editors see ALL posts
        } elseif ($user && $user->hasRole('Author')) {
            // Authors see their OWN published posts
            $query->where('user_id', $user->id)
                  ->where('status', 'published');
        } else {
            // Guests see only strictly published posts
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
    public function store(PostRequest $request, ImageService $imageService)
    {
        $data = $request->getPostData();

        $post = DB::transaction(function () use ($data, $request, $imageService) {
            $post = Post::create($data);

            // Sync categories and tags
            $post->categories()->sync($request->categories ?? []);
            $post->tags()->sync($request->tags ?? []);

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $imagePath = $imageService->storeImage(
                    $request->file('featured_image'),
                    'posts',
                    config('image.sizes', [])
                );
                $post->update(['featured_image' => $imagePath]);
            }

            Log::info('Post created successfully', [
                'post_id' => $post->id,
                'author_id' => $post->user_id,
                'title' => $post->title,
                'status' => $post->status,
                'has_image' => !is_null($post->featured_image),
            ]);

            return $post;
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
    public function update(PostRequest $request, Post $post, ImageService $imageService)
    {
        Gate::authorize('update', $post);

        $data = $request->getPostData();

        DB::transaction(function () use ($data, $request, $post, $imageService) {
            $post->update($data);

            // Sync categories and tags
            $post->categories()->sync($request->categories ?? []);
            $post->tags()->sync($request->tags ?? []);

            // Handle image deletion
            if ($request->boolean('delete_image') && $post->featured_image) {
                $imageService->deleteImage($post->featured_image);
                $post->update(['featured_image' => null]);
            }

            // Handle featured image replacement
            if ($request->hasFile('featured_image')) {
                // Delete old image first
                if ($post->featured_image) {
                    $imageService->deleteImage($post->featured_image);
                }

                $imagePath = $imageService->storeImage(
                    $request->file('featured_image'),
                    'posts',
                    config('image.sizes', [])
                );
                $post->update(['featured_image' => $imagePath]);
            }

            Log::info('Post updated successfully', [
                'post_id' => $post->id,
                'author_id' => $post->user_id,
                'title' => $post->title,
                'status' => $post->status,
                'image_updated' => $request->hasFile('featured_image') || $request->boolean('delete_image'),
            ]);
        });

        return redirect()->route('posts.show', $post->slug)
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * MODIFIED: Removed physical image deletion to prevent errors on Restore.
     */
    public function destroy(Post $post, ImageService $imageService)
    {
        Gate::authorize('delete', $post);

        DB::transaction(function () use ($post, $imageService) {
            // We DO NOT detach categories/tags or delete image during Soft Delete.
            // This allows the post to be restored with all its original data.
            // $post->categories()->detach(); 
            // $post->tags()->detach();
            
            // // Delete featured image if exists
            // if ($post->featured_image) {
            //     $imageService->deleteImage($post->featured_image);
            // }

            // Soft delete the post
            $post->delete();

            Log::info('Post deleted (soft delete)', [
                'post_id' => $post->id,
                'title' => $post->title,
                'image_kept' => !is_null($post->featured_image),
            ]);
        });

        return redirect()->route('posts.index')
            ->with('success', 'Post moved to trash.');
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
}
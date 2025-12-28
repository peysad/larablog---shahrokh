<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\{User, Post, Category, Tag};
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Gate, Log, Storage};
use Symfony\Component\Console\Input\Input;

class PostAdminController extends Controller
{
    /**
     * Display all posts (including drafts) for admin.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::query()
            ->with(['author', 'categories', 'tags'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('author'), fn($q) => $q->where('user_id', $request->author))
            ->when($request->filled('search'), fn($q) => $q->search($request->search))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $authors = User::whereHas('posts')->pluck('name', 'id');

        return view('admin.posts.index', compact('posts', 'authors'));
    }

    /**
     * Show the form for editing a post.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        $categories = Category::all(['id', 'name']);
        $tags = Tag::all(['id', 'name']);

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update a post (admin version).
     */
    public function update(PostRequest $request, Post $post, ImageService $imageService)
    {
        Gate::authorize('update', $post);

        $data = $request->getPostData();

        DB::transaction(function () use ($data, $request, $post, $imageService) {
            // Track the old author for logging
            $oldAuthorId = $post->user_id;
            
            $post->update($data);

            // Sync categories and tags
            $post->categories()->sync($request->categories ?? []);
            $post->tags()->sync($request->tags ?? []);

            // Handle image operations
            if ($request->boolean('delete_image') && $post->featured_image) {
                $imageService->deleteImage($post->featured_image);
                $post->update(['featured_image' => null]);
            }

            if ($request->hasFile('featured_image')) {
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

            Log::info('Admin updated post', [
                'post_id' => $post->id,
                'old_author_id' => $oldAuthorId,
                'new_author_id' => $post->user_id,
                'changed_by' => auth()->id(),
                'title' => $post->title,
            ]);
        });

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Destroy a post (admin version).
     */
    public function destroy(Post $post, ImageService $imageService)
    {
        Gate::authorize('delete', $post);

        DB::transaction(function () use ($post, $imageService) {
            $post->categories()->detach();
            $post->tags()->detach();

            if ($post->featured_image) {
                $imageService->deleteImage($post->featured_image);
            }

            $post->delete();

            Log::info('Admin deleted post', [
                'post_id' => $post->id,
                'title' => $post->title,
                'deleted_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Bulk actions for posts.
     */
    public function bulkAction(Request $request)
    {
        Gate::authorize('manage posts');

        $request->validate([
            'action' => 'required|in:publish,draft,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:posts,id',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No posts selected.');
        }

        // Ensure we only work with posts the user has permission to modify
        $posts = Post::whereIn('id', $ids)->get();
        foreach ($posts as $post) {
            Gate::authorize('update', $post); // or appropriate permission check
        }

        switch ($action) {
            case 'publish':
                Post::whereIn('id', $ids)->update([
                    'status'  => 'published',
                    'published_at' => now(),
                ]);
                $message = count($ids) . ' posts published.';
                break;
            
            case 'draft':
                Post::whereIn('id', $ids)->update([
                    'status' => 'draft',
                    'published_at' => null,
                ]);
                $message = count($ids) . ' posts moved to draft.';
                break;

            case 'delete':
                Post::whereIn('id', $ids)->delete();
                $message = count($ids) . ' posts deleted';
                break;
            
            default:
                return redirect()->back()->with('error', 'Invalid action selected.');
        }

        Log::info('Admin bulk action on posts', [
            'action' => $action,
            'count' => count($ids),
            'user_id' => auth()->id(),
            'post_ids' => $ids,
        ]);

        return redirect()->route('admin.posts.index')->with('success', $message);
    }
}
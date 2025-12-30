<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Requests\PostRequest;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Gate, Log};
use Illuminate\Support\Facades\URL;

class PostAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories']);

        // 1. Filter by Status (Active vs Trashed)
        if ($request->status === 'trashed') {
            $query->onlyTrashed(); // نمایش پست‌های حذف شده
        } else {
            $query->whereNull('deleted_at'); // نمایش پست‌های فعال
            if ($request->status) {
                $query->where('status', $request->status);
            }
        }

        // 2. Filter by Author
        if ($request->filled('author')) {
            $query->where('user_id', $request->author);
        }

        // 3. Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('body', 'like', "%{$request->search}%");
            });
        }

        $posts = $query->latest('deleted_at')
                       ->paginate(12)
                       ->withQueryString();

        // برای استفاده در Dropdown نویسندگان
        $authors = \App\Models\User::role(['Admin', 'Editor', 'Author'])
            ->pluck('name', 'id');

        return view('admin.posts.index', compact('posts', 'authors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        $categories = \App\Models\Category::all(['id', 'name']);
        $tags = \App\Models\Tag::all(['id', 'name']);

        return view('posts.edit', compact('post', 'categories', 'tags')); // Assume using user-side edit view or create admin edit
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post, ImageService $imageService)
    {
        Gate::authorize('update', $post);
        // Logika e update mitavanad hamantor ke PostController bashad
        // Baraye chalesh-e zaman estefade az PostController logic ham raa ejra mikonim:
        return app(\App\Http\Controllers\PostController::class)->update($request, $post, $imageService);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, ImageService $imageService)
    {
        Gate::authorize('delete', $post);
        
        // استفاده از متد اصلی PostController برای حذف نرم
        return app(\App\Http\Controllers\PostController::class)->destroy($post, $imageService);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $post);

        $post->restore();

        Log::info('Post restored from trash', [
            'post_id' => $post->id,
            'restored_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Post restored successfully.');
    }

    /**
     * Force Delete (Permanently remove) the specified resource from storage.
     */
    public function forceDelete($id, ImageService $imageService)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $post);

        DB::transaction(function () use ($post, $imageService) {
            // Detach relationships
            $post->categories()->detach();
            $post->tags()->detach();

            // Delete featured image from disk permanently
            if ($post->featured_image) {
                $imageService->deleteImage($post->featured_image);
            }

            // Permanently delete from database
            $post->forceDelete();

            Log::warning('Post permanently deleted', [
                'post_id' => $post->id,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Post permanently deleted.');
    }

    /**
     * Perform bulk actions on posts.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'No posts selected.');
        }

        switch ($action) {
            case 'delete':
                Post::whereIn('id', $ids)->delete();
                return redirect()->back()->with('success', 'Selected posts moved to trash.');
            
            case 'publish':
                // You can implement bulk publish logic here
                Post::whereIn('id', $ids)->update(['status' => 'published', 'published_at' => now()]);
                return redirect()->back()->with('success', 'Selected posts published.');
            
            case 'draft':
                Post::whereIn('id', $ids)->update(['status' => 'draft']);
                return redirect()->back()->with('success', 'Selected posts moved to draft.');
            
            default:
                return redirect()->back()->with('error', 'Unknown action.');
        }
    }
}
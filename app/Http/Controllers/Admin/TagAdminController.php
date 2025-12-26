<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Gate, Auth};

class TagAdminController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index()
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = Tag::withCount(['posts' => fn($q) => $q->withTrashed()])
            ->latest()
            ->paginate(20);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        Gate::authorize('create', Tag::class);

        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag.
     */
    public function store(TagRequest $request)
    {
        $tag = DB::transaction(function () use ($request) {
            $tag = Tag::create($request->getTagData());

            Log::info('Tag created', [
                'tag_id' => $tag->id,
                'name' => $tag->name,
                'created_by' => Auth::id(),
            ]);

            return $tag;
        });

        return redirect()->route('admin.tags.index')
            ->with('success', "Tag '{$tag->name}' created successfully!");
    }

    /**
     * Show the form for editing the tag.
     */
    public function edit(Tag $tag)
    {
        Gate::authorize('update', $tag);

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag.
     */
    public function update(TagRequest $request, Tag $tag)
    {
        DB::transaction(function () use ($request, $tag) {
            $oldName = $tag->name;
            $tag->update($request->getTagData());

            Log::info('Tag updated', [
                'tag_id' => $tag->id,
                'old_name' => $oldName,
                'new_name' => $tag->name,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.tags.index')
            ->with('success', "Tag '{$tag->name}' updated successfully!");
    }

    /**
     * Remove the tag.
     */
    public function destroy(Tag $tag)
    {
        Gate::authorize('delete', $tag);

        if ($tag->posts()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot delete tag '{$tag->name}' because it has associated posts.");
        }

        DB::transaction(function () use ($tag) {
            $name = $tag->name;
            $tag->delete();

            Log::info('Tag deleted', [
                'tag_id' => $tag->id,
                'name' => $name,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.tags.index')
            ->with('success', "Tag '{$tag->name}' deleted successfully!");
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Gate, Auth};

class CategoryAdminController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        $categories = Category::withCount(['posts' => fn($q) => $q->withTrashed()])
            ->latest()
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request)
    {
        $category = DB::transaction(function () use ($request) {
            $category = Category::create($request->getCategoryData());

            Log::info('Category created', [
                'category_id' => $category->id,
                'name' => $category->name,
                'created_by' => Auth::id(),
            ]);

            return $category;
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' created successfully!");
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(Category $category)
    {
        Gate::authorize('update', $category);

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        DB::transaction(function () use ($request, $category) {
            $oldName = $category->name;
            $category->update($request->getCategoryData());

            Log::info('Category updated', [
                'category_id' => $category->id,
                'old_name' => $oldName,
                'new_name' => $category->name,
                'updated_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully!");
    }

    /**
     * Remove the category.
     */
    public function destroy(Category $category)
    {
        Gate::authorize('delete', $category);

        if ($category->posts()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot delete category '{$category->name}' because it has associated posts.");
        }

        DB::transaction(function () use ($category) {
            $name = $category->name;
            $category->delete();

            Log::info('Category deleted', [
                'category_id' => $category->id,
                'name' => $name,
                'deleted_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' deleted successfully!");
    }
}
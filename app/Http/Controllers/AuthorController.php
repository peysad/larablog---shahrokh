<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\{User, Post};
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Storage, Gate};

class AuthorController extends Controller
{
    /**
     * Display a list of all content creators (Admins, Editors, Authors).
     */
    public function index(Request $request)
    {
        // Fetch users with Admin, Editor, or Author roles
        $query = User::role(['Admin', 'Editor', 'Author']);

        // Apply Search if exists
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginate results
        $authors = $query->orderBy('created_at', 'desc')
                         ->paginate(12)
                         ->withQueryString();

        return view('authors.index', compact('authors'));
    }
    /**
     * Show the author's profile and posts.
     */
    public function show(User $user)
    {
        $posts = $user->posts()
            ->with(['categories', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate(12);

        $stats = [
            'total_posts' => $user->posts()->published()->count(),
            'total_comments' => $user->posts()->withCount('comments')->get()->sum('comments_count'),
            'total_views' => $user->posts()->sum('views'),
            'member_since' => $user->created_at->diffForHumans(),
        ];

        return view('authors.show', compact('user', 'posts', 'stats'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();

        // STRICT CHECK: Only allow users to edit their OWN profile via this route.
        // This overrides the UserPolicy for this specific context.
        if ($user->id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('authors.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(ProfileRequest $request, ImageService $imageService)
    {
        $user = auth()->user();
        
        // STRICT CHECK: Ensure the authenticated user is only updating their own data.
        if ($user->id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->getProfileData();

        DB::transaction(function () use ($data, $request, $user, $imageService) {
            $user->update($data);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $avatarPath = $imageService->storeImage(
                    $request->file('avatar'),
                    'users',
                    ['thumb' => [150, 150]]
                );
                $user->update(['avatar' => $avatarPath]);
            }

            // Handle avatar deletion
            if ($request->boolean('delete_avatar') && $user->avatar) {
                $imageService->deleteImage($user->avatar);
                $user->update(['avatar' => null]);
            }

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'has_avatar' => !is_null($user->avatar),
            ]);
        });

        return redirect()->route('author.show', $user)
            ->with('success', 'Profile updated successfully!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardProfileRequest;
use App\Services\ImageService;
use Illuminate\Support\Facades\{DB, Log, Storage};

class DashboardController extends Controller
{
    /**
     * Display user dashboard with profile management.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Only show stats for content creators
        $stats = [];
        if ($user->hasRole(['Admin', 'Editor', 'Author'])) {
            $stats = [
                'published_posts' => $user->posts()->published()->count(),
                'total_comments' => $user->posts()->withCount('comments')->get()->sum('comments_count'),
                'total_views' => $user->posts()->sum('views'),
            ];
        }

        return view('dashboard', compact('user', 'stats'));
    }

    /**
     * Update regular user profile (restricted fields).
     */
    public function updateProfile(DashboardProfileRequest $request, ImageService $imageService)
    {
        $user = auth()->user();

        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $user, $imageService) {
            // Update core profile fields only
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'bio' => $data['bio'] ?? null,
            ]);

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $this->handleAvatarUpload($request, $user, $imageService);
            }

            // Handle avatar deletion
            if ($request->boolean('delete_avatar') && $user->avatar) {
                $this->handleAvatarDeletion($user, $imageService);
            }

            Log::info('Dashboard profile updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'has_avatar' => !is_null($user->avatar),
            ]);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Handle avatar file upload.
     */
    private function handleAvatarUpload($request, $user, $imageService): void
    {
        // Delete existing avatar
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

    /**
     * Handle avatar file deletion.
     */
    private function handleAvatarDeletion($user, $imageService): void
    {
        $imageService->deleteImage($user->avatar);
        $user->update(['avatar' => null]);
    }
}
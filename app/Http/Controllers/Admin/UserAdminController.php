<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRoleRequest;
use App\Http\Requests\AdminUserBanRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index(Request $request)
    {
        $query = User::with(['roles', 'bannedBy'])->withTrashed();

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->role($role);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            match($status) {
                'banned' => $query->banned(),
                'active' => $query->notBanned(),
                'deleted' => $query->onlyTrashed(),
                default => null
            };
        }

        // Sort
        $sortBy = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $users = $query->paginate(15)->withQueryString();

        $roles = ['Admin', 'Editor', 'Author', 'User'];
        $stats = [
            'total' => User::count(),
            'active' => User::notBanned()->count(),
            'banned' => User::banned()->count(),
            'deleted' => User::onlyTrashed()->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function show(User $user)
    {
        $user->load(['posts', 'roles', 'permissions', 'bannedBy']);
        $activity = $this->getUserActivity($user);
        
        return view('admin.users.show', compact('user', 'activity'));
    }

    public function updateRole(AdminUserRoleRequest $request, User $user)
    {
        Gate::authorize('updateRole', $user);
        
        $oldRole = $user->roles->first()?->name;
        $user->syncRoles([$request->validated()['role']]);
        
        Log::info('User role updated', [
            'user_id' => $user->id,
            'old_role' => $oldRole,
            'new_role' => $request->role,
            'changed_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User role updated from {$oldRole} to {$request->role}");
    }

    public function ban(AdminUserBanRequest $request, User $user)
    {
        Gate::authorize('ban', $user);
        
        $user->update([
            'banned_at' => now(),
            'banned_by' => Auth::id(),
            'ban_reason' => $request->validated()['reason'],
        ]);

        // Logout banned user sessions
        $user->tokens()->delete();

        Log::warning('User banned', [
            'banned_user_id' => $user->id,
            'banned_by' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} has been banned successfully");
    }

    public function unban(User $user)
    {
        Gate::authorize('unban', $user);
        
        $user->update([
            'banned_at' => null,
            'banned_by' => null,
            'ban_reason' => null,
        ]);

        Log::info('User unbanned', [
            'user_id' => $user->id,
            'unbanned_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} has been unbanned");
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        
        $userName = $user->name;
        $user->delete();

        Log::warning('User soft deleted', [
            'user_id' => $user->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$userName} moved to trash");
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $user);
        
        $user->restore();

        Log::info('User restored', [
            'user_id' => $user->id,
            'restored_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index', ['status' => 'deleted'])
            ->with('success', "User {$user->name} restored successfully");
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        Gate::authorize('forceDelete', $user);
        
        $userName = $user->name;
        $user->forceDelete();

        Log::warning('User permanently deleted', [
            'user_id' => $user->id,
            'deleted_by' => Auth::id(),
        ]);

        return redirect()->route('admin.users.index', ['status' => 'deleted'])
            ->with('success', "User {$userName} permanently deleted");
    }

    private function getUserActivity(User $user): array
    {
        return [
            'last_post' => $user->posts()->latest()->first()?->created_at,
            'posts_count' => $user->posts()->count(),
            'published_count' => $user->published_posts_count,
            'draft_count' => $user->posts()->draft()->count(),
            'member_since' => $user->created_at,
        ];
    }
}
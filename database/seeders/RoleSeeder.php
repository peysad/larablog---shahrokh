<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Truncate existing data (development only)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        Role::truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Define all permissions
        $permissions = [
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'publish posts',
            'create comments',
            'manage comments',
            'approve comments',
            'delete comments',
            'manage categories',
            'manage tags',
            'manage users',
            'view admin panel',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Admin role (full access)
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        // Create Editor role (content management)
        $editor = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $editor->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'delete posts', 'publish posts',
            'manage comments', 'approve comments',
            'manage categories', 'manage tags',
            'view admin panel',
        ]);

        // Create Author role (limited to their content)
        $author = Role::create(['name' => 'Author', 'guard_name' => 'web']);
        $author->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'publish posts',
            'manage comments',
        ]);

        // Create User role (read-only)
        $user = Role::create(['name' => 'User', 'guard_name' => 'web']);
        $user->givePermissionTo(['view posts']);
    }
}
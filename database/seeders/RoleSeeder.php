<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Default roles with their permissions
     */
    private array $roles = [
        'Super Admin' => [
            'description' => 'Full system access with all permissions',
            'permissions' => '*', // Special case: all permissions
        ],
        'Admin' => [
            'description' => 'System administrator with most permissions',
            'permissions' => [
                'view users', 'create users', 'edit users', 'delete users',
                'view roles', 'create roles', 'edit roles',
                'view content', 'create content', 'edit content', 'delete content', 'publish content',
                'view settings', 'edit settings',
                'view reports', 'export reports',
                'view audit log',
            ],
        ],
        'Editor' => [
            'description' => 'Content manager with publishing rights',
            'permissions' => [
                'view content', 'create content', 'edit content', 'publish content',
                'view reports',
            ],
        ],
        'Author' => [
            'description' => 'Content creator without publishing rights',
            'permissions' => [
                'view content', 'create content', 'edit content',
            ],
        ],
        'Viewer' => [
            'description' => 'Read-only access to content',
            'permissions' => [
                'view content',
                'view reports',
            ],
        ],
        'API User' => [
            'description' => 'External system with API access',
            'permissions' => [
                'api access',
            ],
        ],
    ];

    public function run(): void
    {
        $allPermissions = Permission::all();

        foreach ($this->roles as $name => $attributes) {
            $role = Role::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $attributes['description'],
            ]);

            // Handle special case for Super Admin
            if ($attributes['permissions'] === '*') {
                $role->permissions()->attach($allPermissions->pluck('id'));
                continue;
            }

            // Attach specific permissions
            $permissions = Permission::whereIn('name', $attributes['permissions'])->get();
            $role->permissions()->attach($permissions->pluck('id'));
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Default permissions grouped by category
     */
    private array $permissions = [
        'User Management' => [
            'view users' => 'Can view user list and details',
            'create users' => 'Can create new users',
            'edit users' => 'Can edit existing users',
            'delete users' => 'Can delete users',
            'manage user roles' => 'Can assign/remove user roles',
        ],
        'Role Management' => [
            'view roles' => 'Can view role list and details',
            'create roles' => 'Can create new roles',
            'edit roles' => 'Can edit existing roles',
            'delete roles' => 'Can delete roles',
        ],
        'Content Management' => [
            'view content' => 'Can view all content',
            'create content' => 'Can create new content',
            'edit content' => 'Can edit existing content',
            'delete content' => 'Can delete content',
            'publish content' => 'Can publish or unpublish content',
        ],
        'Settings' => [
            'view settings' => 'Can view system settings',
            'edit settings' => 'Can modify system settings',
        ],
        'Reports' => [
            'view reports' => 'Can view system reports',
            'export reports' => 'Can export system reports',
            'manage reports' => 'Can create and edit report configurations',
        ],
        'API Access' => [
            'api access' => 'Can access API endpoints',
            'manage api tokens' => 'Can create and manage API tokens',
        ],
        'Audit Log' => [
            'view audit log' => 'Can view system audit log',
            'export audit log' => 'Can export audit log data',
        ],
    ];

    public function run(): void
    {
        foreach ($this->permissions as $category => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::create([
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'category' => $category,
                ]);
            }
        }
    }
}
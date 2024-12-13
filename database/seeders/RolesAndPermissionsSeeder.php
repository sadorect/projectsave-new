<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'manage-users',
            'edit-content',
            'view-dashboard',
            'manage-anniversaries',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => ucwords(str_replace('-', ' ', $permission)),
                'slug' => $permission
            ]);
        }

        // Create roles
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin'
        ]);
        $editorRole = Role::create([
            'name' => 'Editor',
            'slug' => 'editor'
        ]);
        $userRole = Role::create([
            'name' => 'User',
            'slug' => 'user'
        ]);

        $managerRole = Role::create([
          'name' => 'Manager',
          'slug' => 'manager'
      ]);
        // Attach permissions to roles
      $editorRole->permissions()->attach([1, 2]);
      $userRole->permissions()->attach([3]);
      $managerRole->permissions()->attach([4]);



        // Attach all permissions to admin role
        $adminRole->permissions()->attach(Permission::all());
    }
}

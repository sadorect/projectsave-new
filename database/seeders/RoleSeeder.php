<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Default roles with their permissions
     */
    private array $roles = [
        'Super Admin' => [
            'slug' => 'super-admin',
            'description' => 'Full system access with all permissions',
            'permissions' => '*', // Special case: all permissions
        ],
        'Admin' => [
            'slug' => 'admin',
            'description' => 'Platform administrator with broad operational coverage',
            'permissions' => [
                'access-admin-dashboard',
                'access-content-admin',
                'access-lms-admin',
                'manage-users',
                'view-users',
                'create-users',
                'edit-users',
                'delete-users',
                'verify-users',
                'manage-user-roles',
                'manage-user-sessions',
                'view-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'manage-roles',
                'view-posts',
                'create-posts',
                'edit-posts',
                'delete-posts',
                'publish-posts',
                'manage-post-taxonomy',
                'view-events',
                'create-events',
                'edit-events',
                'delete-events',
                'publish-events',
                'view-faqs',
                'create-faqs',
                'edit-faqs',
                'delete-faqs',
                'publish-faqs',
                'view-content',
                'create-content',
                'edit-content',
                'delete-content',
                'publish-content',
                'view-courses',
                'manage-courses',
                'manage-lessons',
                'manage-enrollments',
                'manage-exams',
                'manage-certificates',
                'manage-partners',
                'manage-prayer-force',
                'manage-forms',
                'manage-mail',
                'manage-mail-templates',
                'manage-notification-settings',
                'manage-files',
                'view-reports',
                'export-reports',
                'view-audit-log',
                'manage-audit-log',
                'manage-settings',
                'api-access',
                'manage-api-tokens',
            ],
        ],
        'Content Admin' => [
            'slug' => 'content-admin',
            'description' => 'Content operations lead with full publishing control',
            'permissions' => [
                'access-content-admin',
                'view-posts',
                'create-posts',
                'edit-posts',
                'delete-posts',
                'publish-posts',
                'manage-post-taxonomy',
                'view-events',
                'create-events',
                'edit-events',
                'delete-events',
                'publish-events',
                'view-faqs',
                'create-faqs',
                'edit-faqs',
                'delete-faqs',
                'publish-faqs',
                'view-content',
                'create-content',
                'edit-content',
                'delete-content',
                'publish-content',
            ],
        ],
        'Editor' => [
            'slug' => 'editor',
            'description' => 'Content manager with publishing rights',
            'permissions' => [
                'access-content-admin',
                'view-posts',
                'create-posts',
                'edit-posts',
                'publish-posts',
                'manage-post-taxonomy',
                'view-events',
                'create-events',
                'edit-events',
                'publish-events',
                'view-faqs',
                'create-faqs',
                'edit-faqs',
                'publish-faqs',
                'view-content',
                'create-content',
                'edit-content',
                'publish-content',
            ],
        ],
        'Author' => [
            'slug' => 'author',
            'description' => 'Content creator without publishing rights',
            'permissions' => [
                'access-content-admin',
                'view-posts',
                'create-posts',
                'edit-posts',
                'view-events',
                'create-events',
                'edit-events',
                'view-faqs',
                'create-faqs',
                'edit-faqs',
                'view-content',
                'create-content',
                'edit-content',
            ],
        ],
        'Viewer' => [
            'slug' => 'viewer',
            'description' => 'Read-only access to content',
            'permissions' => [
                'access-content-admin',
                'view-posts',
                'view-events',
                'view-faqs',
                'view-content',
                'view-reports',
            ],
        ],
        'LMS Admin' => [
            'slug' => 'lms-admin',
            'description' => 'LMS operator for courses, enrollments, exams, and certificates',
            'permissions' => [
                'access-admin-dashboard',
                'access-lms-admin',
                'view-courses',
                'manage-courses',
                'manage-lessons',
                'manage-enrollments',
                'manage-exams',
                'manage-certificates',
                'view-reports',
            ],
        ],
        'Student Support' => [
            'slug' => 'student-support',
            'description' => 'Learner support role focused on account and enrollment issues',
            'permissions' => [
                'access-admin-dashboard',
                'view-users',
                'edit-users',
                'verify-users',
                'manage-user-sessions',
                'view-courses',
                'manage-enrollments',
                'view-reports',
            ],
        ],
        'Partner Operations' => [
            'slug' => 'partner-operations',
            'description' => 'Operations role for partner and prayer-force workflows',
            'permissions' => [
                'access-admin-dashboard',
                'manage-partners',
                'manage-prayer-force',
                'view-reports',
            ],
        ],
        'API User' => [
            'slug' => 'api-user',
            'description' => 'External system with API access',
            'permissions' => [
                'api-access',
                'manage-api-tokens',
            ],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allPermissions = Permission::all();
        $columns = array_flip(Schema::getColumnListing('roles'));

        foreach ($this->roles as $name => $attributes) {
            $roleAttributes = [
                'name' => $name,
            ];

            if (isset($columns['description'])) {
                $roleAttributes['description'] = $attributes['description'];
            }

            if (isset($columns['guard_name'])) {
                $roleAttributes['guard_name'] = 'web';
            }

            $role = Role::query()->updateOrCreate(
                ['slug' => $attributes['slug']],
                $roleAttributes
            );

            // Handle special case for Super Admin
            if ($attributes['permissions'] === '*') {
                $role->syncPermissions($allPermissions);
                continue;
            }

            // Attach specific permissions
            $permissions = Permission::query()
                ->whereIn('slug', $attributes['permissions'])
                ->get();
            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

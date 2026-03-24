<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Default permissions grouped by category
     */
    private array $permissions = [
        'Admin Access' => [
            [
                'name' => 'Access Admin Dashboard',
                'slug' => 'access-admin-dashboard',
                'description' => 'Can enter the main admin dashboard and shared admin shell.',
            ],
            [
                'name' => 'Access Content Admin',
                'slug' => 'access-content-admin',
                'description' => 'Can enter the content management surface.',
            ],
            [
                'name' => 'Access LMS Admin',
                'slug' => 'access-lms-admin',
                'description' => 'Can enter LMS operations and management surfaces.',
            ],
        ],
        'User Management' => [
            [
                'name' => 'Manage Users',
                'slug' => 'manage-users',
                'description' => 'Can administer users across the admin surface.',
            ],
            [
                'name' => 'View Users',
                'slug' => 'view-users',
                'description' => 'Can view user lists and user detail pages.',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'description' => 'Can create new users.',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'edit-users',
                'description' => 'Can edit existing users.',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete-users',
                'description' => 'Can delete users.',
            ],
            [
                'name' => 'Verify Users',
                'slug' => 'verify-users',
                'description' => 'Can manually verify user email addresses.',
            ],
            [
                'name' => 'Manage User Roles',
                'slug' => 'manage-user-roles',
                'description' => 'Can assign and remove user roles.',
            ],
            [
                'name' => 'Manage User Sessions',
                'slug' => 'manage-user-sessions',
                'description' => 'Can view and terminate user sessions.',
            ],
        ],
        'Role Management' => [
            [
                'name' => 'View Roles',
                'slug' => 'view-roles',
                'description' => 'Can view role lists and role detail pages.',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'create-roles',
                'description' => 'Can create new roles.',
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'edit-roles',
                'description' => 'Can edit roles.',
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'delete-roles',
                'description' => 'Can delete roles.',
            ],
            [
                'name' => 'Manage Roles',
                'slug' => 'manage-roles',
                'description' => 'Can administer the platform role matrix.',
            ],
        ],
        'Post Management' => [
            [
                'name' => 'View Posts',
                'slug' => 'view-posts',
                'description' => 'Can browse and review posts in content admin.',
            ],
            [
                'name' => 'Create Posts',
                'slug' => 'create-posts',
                'description' => 'Can create posts.',
            ],
            [
                'name' => 'Edit Posts',
                'slug' => 'edit-posts',
                'description' => 'Can edit posts.',
            ],
            [
                'name' => 'Delete Posts',
                'slug' => 'delete-posts',
                'description' => 'Can delete posts.',
            ],
            [
                'name' => 'Publish Posts',
                'slug' => 'publish-posts',
                'description' => 'Can publish or unpublish posts.',
            ],
            [
                'name' => 'Manage Post Taxonomy',
                'slug' => 'manage-post-taxonomy',
                'description' => 'Can create and organize post categories and tags.',
            ],
        ],
        'Event Management' => [
            [
                'name' => 'View Events',
                'slug' => 'view-events',
                'description' => 'Can browse and review events in content admin.',
            ],
            [
                'name' => 'Create Events',
                'slug' => 'create-events',
                'description' => 'Can create events.',
            ],
            [
                'name' => 'Edit Events',
                'slug' => 'edit-events',
                'description' => 'Can edit events.',
            ],
            [
                'name' => 'Delete Events',
                'slug' => 'delete-events',
                'description' => 'Can delete events.',
            ],
            [
                'name' => 'Publish Events',
                'slug' => 'publish-events',
                'description' => 'Can publish or unpublish events.',
            ],
        ],
        'FAQ Management' => [
            [
                'name' => 'View FAQs',
                'slug' => 'view-faqs',
                'description' => 'Can browse and review FAQs in content admin.',
            ],
            [
                'name' => 'Create FAQs',
                'slug' => 'create-faqs',
                'description' => 'Can create FAQs.',
            ],
            [
                'name' => 'Edit FAQs',
                'slug' => 'edit-faqs',
                'description' => 'Can edit FAQs.',
            ],
            [
                'name' => 'Delete FAQs',
                'slug' => 'delete-faqs',
                'description' => 'Can delete FAQs.',
            ],
            [
                'name' => 'Publish FAQs',
                'slug' => 'publish-faqs',
                'description' => 'Can publish or unpublish FAQs.',
            ],
        ],
        'Legacy Content Compatibility' => [
            [
                'name' => 'View Content',
                'slug' => 'view-content',
                'description' => 'Legacy compatibility permission for broad content viewing.',
            ],
            [
                'name' => 'Create Content',
                'slug' => 'create-content',
                'description' => 'Legacy compatibility permission for broad content creation.',
            ],
            [
                'name' => 'Edit Content',
                'slug' => 'edit-content',
                'description' => 'Legacy compatibility permission for broad content editing.',
            ],
            [
                'name' => 'Delete Content',
                'slug' => 'delete-content',
                'description' => 'Legacy compatibility permission for broad content deletion.',
            ],
            [
                'name' => 'Publish Content',
                'slug' => 'publish-content',
                'description' => 'Legacy compatibility permission for broad content publishing.',
            ],
        ],
        'LMS Operations' => [
            [
                'name' => 'View Courses',
                'slug' => 'view-courses',
                'description' => 'Can view course records and LMS dashboards.',
            ],
            [
                'name' => 'Manage Courses',
                'slug' => 'manage-courses',
                'description' => 'Can create, update, and delete courses.',
            ],
            [
                'name' => 'Manage Lessons',
                'slug' => 'manage-lessons',
                'description' => 'Can create, update, and delete lessons.',
            ],
            [
                'name' => 'Manage Enrollments',
                'slug' => 'manage-enrollments',
                'description' => 'Can manage course enrollments and student status.',
            ],
            [
                'name' => 'Manage Exams',
                'slug' => 'manage-exams',
                'description' => 'Can create, import, update, and grade exams.',
            ],
            [
                'name' => 'Manage Certificates',
                'slug' => 'manage-certificates',
                'description' => 'Can approve, issue, and verify certificates.',
            ],
        ],
        'Operations' => [
            [
                'name' => 'Manage Partners',
                'slug' => 'manage-partners',
                'description' => 'Can manage partner applications and records.',
            ],
            [
                'name' => 'Manage Prayer Force',
                'slug' => 'manage-prayer-force',
                'description' => 'Can manage prayer force operations.',
            ],
            [
                'name' => 'Manage Forms',
                'slug' => 'manage-forms',
                'description' => 'Can review and process form submissions.',
            ],
            [
                'name' => 'Manage Mail',
                'slug' => 'manage-mail',
                'description' => 'Can send and monitor operational mail flows.',
            ],
            [
                'name' => 'Manage Mail Templates',
                'slug' => 'manage-mail-templates',
                'description' => 'Can edit mail templates.',
            ],
            [
                'name' => 'Manage Notification Settings',
                'slug' => 'manage-notification-settings',
                'description' => 'Can edit notification settings and reminder flows.',
            ],
            [
                'name' => 'Manage Files',
                'slug' => 'manage-files',
                'description' => 'Can review and manage uploaded files.',
            ],
        ],
        'Reporting and Settings' => [
            [
                'name' => 'View Reports',
                'slug' => 'view-reports',
                'description' => 'Can view reports and operational summaries.',
            ],
            [
                'name' => 'Export Reports',
                'slug' => 'export-reports',
                'description' => 'Can export report data.',
            ],
            [
                'name' => 'View Audit Log',
                'slug' => 'view-audit-log',
                'description' => 'Can view audit logs.',
            ],
            [
                'name' => 'Manage Audit Log',
                'slug' => 'manage-audit-log',
                'description' => 'Can prune or change audit-log behavior.',
            ],
            [
                'name' => 'Manage Settings',
                'slug' => 'manage-settings',
                'description' => 'Can update platform settings.',
            ],
        ],
        'API Access' => [
            [
                'name' => 'API Access',
                'slug' => 'api-access',
                'description' => 'Can access API endpoints.',
            ],
            [
                'name' => 'Manage API Tokens',
                'slug' => 'manage-api-tokens',
                'description' => 'Can create and manage API tokens.',
            ],
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->permissions as $category => $permissions) {
            foreach ($permissions as $permission) {
                Permission::query()->updateOrCreate(
                    ['slug' => $permission['slug']],
                    [
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                        'category' => $category,
                        'guard_name' => 'web',
                    ]
                );
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifyPermissionsStructure extends Command
{
    protected $signature = 'permissions:verify 
                          {--fix : Attempt to fix common issues}
                          {--user= : Check specific user\'s permissions}';

    protected $description = 'Verify the permissions structure and identify any issues';

    private $errors = [];
    private $warnings = [];
    private $fixes = [];

    public function handle()
    {
        $this->info('Starting permissions structure verification...');
        $this->newLine();

        // 1. Database Structure Check
        $this->checkDatabaseStructure();

        // 2. Permissions Check
        $this->checkPermissions();

        // 3. Roles Check
        $this->checkRoles();

        // 4. User Permissions Check
        $this->checkUserPermissions();

        // 5. Route Permissions Check
        $this->checkRoutePermissions();

        // Display Results
        $this->displayResults();

        // Apply Fixes if requested
        if ($this->option('fix') && count($this->fixes) > 0) {
            if ($this->confirm('Would you like to apply the suggested fixes?')) {
                $this->applyFixes();
            }
        }

        return count($this->errors) === 0 ? 0 : 1;
    }

    private function checkDatabaseStructure()
    {
        $this->info('Checking database structure...');

        // Check required tables
        $requiredTables = ['users', 'roles', 'permissions', 'role_user', 'role_permission'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->errors[] = "Missing table: {$table}";
                $this->fixes[] = "Run: php artisan migrate";
            }
        }

        // Check required columns
        if (Schema::hasTable('permissions')) {
            $requiredColumns = ['id', 'name', 'slug', 'description', 'category'];
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn('permissions', $column)) {
                    $this->errors[] = "Missing column in permissions table: {$column}";
                }
            }
        }

        // Check indexes
        $this->checkIndexes();
    }

    private function checkIndexes()
    {
        // Check if slug has unique index
        $indexes = DB::select("SHOW INDEX FROM permissions WHERE Column_name = 'slug'");
        if (empty($indexes)) {
            $this->warnings[] = "Missing unique index on permissions.slug";
            $this->fixes[] = "CREATE UNIQUE INDEX permissions_slug_unique ON permissions(slug)";
        }

        // Check pivot table indexes
        $pivotIndexes = DB::select("SHOW INDEX FROM role_permission WHERE Column_name IN ('role_id', 'permission_id')");
        if (count($pivotIndexes) < 2) {
            $this->warnings[] = "Missing indexes on role_permission pivot table";
        }
    }

    private function checkPermissions()
    {
        $this->info('Checking permissions...');

        $permissions = Permission::all();

        // Check for duplicate slugs
        $duplicateSlugs = $permissions->groupBy('slug')
            ->filter(function ($group) {
                return $group->count() > 1;
            });

        if ($duplicateSlugs->isNotEmpty()) {
            $this->errors[] = "Found duplicate permission slugs: " . $duplicateSlugs->keys()->implode(', ');
        }

        // Check for missing slugs
        $permissionsWithoutSlug = $permissions->filter(function ($permission) {
            return empty($permission->slug);
        });

        if ($permissionsWithoutSlug->isNotEmpty()) {
            $this->errors[] = "Found permissions without slugs: " . $permissionsWithoutSlug->pluck('name')->implode(', ');
            foreach ($permissionsWithoutSlug as $permission) {
                $this->fixes[] = "UPDATE permissions SET slug = '" . Str::slug($permission->name) . "' WHERE id = " . $permission->id;
            }
        }

        // Check for common required permissions
        $requiredPermissions = [
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'manage-roles',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users'
        ];

        $missingRequired = array_diff($requiredPermissions, $permissions->pluck('slug')->toArray());
        if (!empty($missingRequired)) {
            $this->warnings[] = "Missing common permissions: " . implode(', ', $missingRequired);
            $this->fixes[] = "Run: php artisan db:seed --class=PermissionSeeder";
        }
    }

    private function checkRoles()
    {
        $this->info('Checking roles...');

        $roles = Role::with('permissions')->get();

        // Check for roles without permissions
        $rolesWithoutPermissions = $roles->filter(function ($role) {
            return $role->permissions->isEmpty();
        });

        if ($rolesWithoutPermissions->isNotEmpty()) {
            $this->warnings[] = "Found roles without any permissions: " . $rolesWithoutPermissions->pluck('name')->implode(', ');
        }

        // Check for Super Admin role
        $superAdmin = $roles->where('slug', 'super-admin')->first();
        if (!$superAdmin) {
            $this->warnings[] = "Super Admin role not found";
        } else {
            // Check if Super Admin has all permissions
            $allPermissionsCount = Permission::count();
            if ($superAdmin->permissions->count() !== $allPermissionsCount) {
                $this->errors[] = "Super Admin role doesn't have all permissions";
                $this->fixes[] = "Assign all permissions to Super Admin role";
            }
        }
    }

    private function checkUserPermissions()
    {
        $this->info('Checking user permissions...');

        if ($userId = $this->option('user')) {
            $users = User::where('id', $userId)->get();
        } else {
            $users = User::all();
        }

        // Check for users without roles
        $usersWithoutRoles = $users->filter(function ($user) {
            return $user->roles->isEmpty();
        });

        if ($usersWithoutRoles->isNotEmpty()) {
            $this->warnings[] = "Found users without any roles: " . $usersWithoutRoles->pluck('email')->implode(', ');
        }

        // Verify each user's permissions
        foreach ($users as $user) {
            $this->info("Checking user {$user->email}...");
            
            foreach ($user->roles as $role) {
                if ($role->permissions->isEmpty()) {
                    $this->warnings[] = "User {$user->email} has role '{$role->name}' with no permissions";
                }
            }
        }
    }

    private function checkRoutePermissions()
    {
        $this->info('Checking route permissions...');

        $routes = collect(\Route::getRoutes())->filter(function ($route) {
            return collect($route->middleware())->contains('permission');
        });

        foreach ($routes as $route) {
            $middlewares = collect($route->middleware());
            $permissionMiddleware = $middlewares->first(function ($middleware) {
                return Str::startsWith($middleware, 'permission:');
            });

            if ($permissionMiddleware) {
                $permission = Str::after($permissionMiddleware, 'permission:');
                if (!Permission::where('slug', $permission)->exists()) {
                    $this->errors[] = "Route {$route->uri()} requires non-existent permission: {$permission}";
                }
            }
        }
    }

    private function displayResults()
    {
        $this->newLine(2);
        
        if (empty($this->errors) && empty($this->warnings)) {
            $this->info('✅ All permission checks passed successfully!');
            return;
        }

        if (!empty($this->errors)) {
            $this->error('Errors found:');
            foreach ($this->errors as $error) {
                $this->error("❌ {$error}");
            }
        }

        if (!empty($this->warnings)) {
            $this->warn('Warnings found:');
            foreach ($this->warnings as $warning) {
                $this->warn("⚠️ {$warning}");
            }
        }

        if (!empty($this->fixes) && $this->option('fix')) {
            $this->info('Suggested fixes:');
            foreach ($this->fixes as $fix) {
                $this->line("🔧 {$fix}");
            }
        }
    }

    private function applyFixes()
    {
        $this->info('Applying fixes...');

        foreach ($this->fixes as $fix) {
            if (Str::startsWith($fix, 'UPDATE ') || Str::startsWith($fix, 'CREATE ')) {
                // Execute SQL fixes
                try {
                    DB::statement($fix);
                    $this->info("✅ Applied fix: {$fix}");
                } catch (\Exception $e) {
                    $this->error("Failed to apply fix: {$fix}");
                    $this->error($e->getMessage());
                }
            } elseif (Str::startsWith($fix, 'Run: ')) {
                // Command fixes
                $command = Str::after($fix, 'Run: ');
                $this->call(trim($command));
            } else {
                // Custom fixes
                if ($fix === "Assign all permissions to Super Admin role") {
                    $this->assignAllPermissionsToSuperAdmin();
                }
            }
        }
    }

    private function assignAllPermissionsToSuperAdmin()
    {
        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::pluck('id');
            $superAdmin->permissions()->sync($allPermissions);
            $this->info('✅ Assigned all permissions to Super Admin role');
        }
    }
}
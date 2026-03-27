<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    private const PROTECTED_ROLE_SLUGS = ['super-admin', 'admin'];

    public function index()
    {
        $roles = Role::query()
            ->with(['permissions' => fn (BelongsToMany $query) => $this->applyPermissionGuard($query)])
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->permissionGroups();

        return view('admin.roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array|nullable',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::query()->create($this->buildRolePayload($validated, true));

        $permissions = $this->permissionQuery()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->permissionGroups();
        $isProtected = in_array($role->slug, self::PROTECTED_ROLE_SLUGS, true);

        return view('admin.roles.edit', compact('role', 'permissionGroups', 'isProtected'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array|nullable',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if (in_array($role->slug, self::PROTECTED_ROLE_SLUGS, true) && Str::slug($validated['name']) !== $role->slug) {
            return redirect()->route('admin.roles.edit', $role)
                ->withErrors(['name' => 'Protected system roles cannot be renamed.'])
                ->withInput();
        }

        $role->update($this->buildRolePayload($validated));

        $permissions = $this->permissionQuery()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->slug, self::PROTECTED_ROLE_SLUGS, true)) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Protected system roles cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }

    private function permissionGroups()
    {
        $query = $this->permissionQuery();

        if ($this->permissionHasColumn('category')) {
            $query->orderByRaw("COALESCE(category, 'Uncategorized')");
        }

        return $query
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $this->permissionCategoryLabel($permission));
    }

    private function buildRolePayload(array $validated, bool $includeGuard = false): array
    {
        $payload = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ];

        if ($this->roleHasColumn('description')) {
            $payload['description'] = $validated['description'] ?? null;
        }

        if ($includeGuard && $this->roleHasColumn('guard_name')) {
            $payload['guard_name'] = $this->defaultGuardName();
        }

        return $payload;
    }

    private function permissionQuery(): Builder
    {
        $query = Permission::query();

        return $this->applyPermissionGuard($query);
    }

    private function applyPermissionGuard(Builder|BelongsToMany $query): Builder|BelongsToMany
    {
        if ($this->permissionHasColumn('guard_name')) {
            $query->where('guard_name', $this->defaultGuardName());
        }

        return $query;
    }

    private function permissionCategoryLabel(Permission $permission): string
    {
        if (! $this->permissionHasColumn('category')) {
            return 'Permissions';
        }

        return $permission->category ?: 'Uncategorized';
    }

    private function defaultGuardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }

    private function roleHasColumn(string $column): bool
    {
        static $columns;
        $columns ??= array_flip(Schema::getColumnListing((new Role())->getTable()));

        return isset($columns[$column]);
    }

    private function permissionHasColumn(string $column): bool
    {
        static $columns;
        $columns ??= array_flip(Schema::getColumnListing((new Permission())->getTable()));

        return isset($columns[$column]);
    }
}

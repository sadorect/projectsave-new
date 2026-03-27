<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissionQuery = Permission::query()->with(['roles' => fn (BelongsToMany $query) => $this->applyRoleGuard($query)]);

        if ($this->permissionHasColumn('guard_name')) {
            $permissionQuery->where('guard_name', $this->defaultGuardName());
        }

        if ($this->permissionHasColumn('category')) {
            $permissionQuery->orderByRaw("COALESCE(category, 'Uncategorized')");
        }

        $permissions = $permissionQuery
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $this->categoryLabel($permission));

        $roles = $this->roleQuery()->orderBy('name')->get();
        $categories = $permissions->keys()->values();

        return view('admin.permissions.index', compact('permissions', 'roles', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'roles' => 'array|nullable',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $permission = Permission::query()->create($this->buildPermissionPayload($validated, true));

        if (! empty($validated['roles'])) {
            $roles = $this->roleQuery()
                ->whereIn('id', $validated['roles'])
                ->get();

            $permission->syncRoles($roles);
        }

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $roles = $this->roleQuery()->orderBy('name')->get();
        $categories = $this->permissionQuery()->pluck('category')->filter()->unique()->values();

        return view('admin.permissions.edit', compact('permission', 'roles', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'roles' => 'array|nullable',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $permission->update($this->buildPermissionPayload($validated));

        $roles = $this->roleQuery()
            ->whereIn('id', $validated['roles'] ?? [])
            ->get();

        $permission->syncRoles($roles);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->syncRoles([]);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully');
    }

    private function buildPermissionPayload(array $validated, bool $includeGuard = false): array
    {
        $payload = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ];

        if ($this->permissionHasColumn('category')) {
            $payload['category'] = filled($validated['category'] ?? null) ? trim((string) $validated['category']) : 'Custom';
        }

        if ($this->permissionHasColumn('description')) {
            $payload['description'] = $validated['description'] ?? null;
        }

        if ($includeGuard && $this->permissionHasColumn('guard_name')) {
            $payload['guard_name'] = $this->defaultGuardName();
        }

        return $payload;
    }

    private function roleQuery(): Builder
    {
        $query = Role::query();

        return $this->applyRoleGuard($query);
    }

    private function permissionQuery(): Builder
    {
        $query = Permission::query();

        if ($this->permissionHasColumn('guard_name')) {
            $query->where('guard_name', $this->defaultGuardName());
        }

        return $query;
    }

    private function applyRoleGuard(Builder|BelongsToMany $query): Builder|BelongsToMany
    {
        if ($this->roleHasColumn('guard_name')) {
            $query->where('guard_name', $this->defaultGuardName());
        }

        return $query;
    }

    private function categoryLabel(Permission $permission): string
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

    private function permissionHasColumn(string $column): bool
    {
        static $columns;
        $columns ??= array_flip(Schema::getColumnListing((new Permission())->getTable()));

        return isset($columns[$column]);
    }

    private function roleHasColumn(string $column): bool
    {
        static $columns;
        $columns ??= array_flip(Schema::getColumnListing((new Role())->getTable()));

        return isset($columns[$column]);
    }
}

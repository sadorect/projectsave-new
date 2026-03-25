<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
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

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        $permissions = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $permissionGroups = $this->permissionGroups();

        return view('admin.roles.edit', compact('role', 'permissionGroups'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array|nullable',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        $permissions = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->get();

        $role->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }

    private function permissionGroups()
    {
        return Permission::query()
            ->orderByRaw("COALESCE(category, 'Uncategorized')")
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->category ?: 'Uncategorized');
    }
}

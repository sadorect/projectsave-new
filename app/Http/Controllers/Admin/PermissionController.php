<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderByRaw("COALESCE(category, 'Uncategorized')")
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->category ?: 'Uncategorized');

        $roles = Role::query()->orderBy('name')->get();

        return view('admin.permissions.index', compact('permissions', 'roles'));
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

        $permission = Permission::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category' => $validated['category'] ?? 'Custom',
            'description' => $validated['description'] ?? null,
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        if (! empty($validated['roles'])) {
            $roles = Role::query()
                ->whereIn('id', $validated['roles'])
                ->where('guard_name', config('auth.defaults.guard', 'web'))
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
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.permissions.edit', compact('permission', 'roles'));
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

        $permission->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category' => $validated['category'] ?? 'Custom',
            'description' => $validated['description'] ?? null,
        ]);

        $roles = Role::query()
            ->whereIn('id', $validated['roles'] ?? [])
            ->where('guard_name', config('auth.defaults.guard', 'web'))
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
}

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
        $permissions = Permission::with('roles')->get();
        $roles = Role::all();
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
            'roles' => 'array|nullable',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        if (! empty($validated['roles'])) {
            $roles = Role::query()
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

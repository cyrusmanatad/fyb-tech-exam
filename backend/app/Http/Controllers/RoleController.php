<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('guard_name', 'api')
            ->withCount('users')
            ->with([
                'permissions:id,name',
                'users:id,name,email', // for avatars
            ])
            ->get()
            ->map(fn($role) => [
                'id'          => $role->id,
                'name'        => $role->name,
                'description' => $role->desc,
                'color'       => $role->color,
                'users_count' => $role->users_count,
                'users'       => $role->users->take(3)->map(fn($u) => [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'avatar'=> "https://ui-avatars.com/api/?name={$u->name}&background=random&color=fff"
                ]),
                'permissions' => $role->permissions->pluck('name'),
            ]);

        return response()->json(['data' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description'   => ['required', 'string'],
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name'       => $request->name,
            'desc'       => $request->description,
            'guard_name' => 'api',
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role created successfully',
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'guard_name'  => $role->guard_name,
                'permissions' => $role->permissions->pluck('name'),
            ]
        ], 201);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string'],
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Update role name
        $role->update(['name' => $request->name, 'desc' => $request->description]);

        // Sync permissions — removes old, adds new
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role updated successfully',
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'permissions' => $role->permissions->pluck('name'),
            ]
        ]);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    public function permissions()
    {
        // $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'api')
        //     ->get(['id', 'name'])
        //     ->groupBy(fn($p) => str($p->name)->afterLast(' ')->title()) // group by module
        //     ->map(fn($group) => $group->pluck('name'));

        // Return flat array of permission names
        $permissions = \Spatie\Permission\Models\Permission::where('guard_name', 'api')
        ->pluck('name'); // ['view products', 'edit products', ...]


        return response()->json(['data' => $permissions]);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RolesStoreRequest;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Auth\Access\AuthorizationException;

class RolesAndPermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['success'=>true,'data'=>$roles], 200);
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
    public function store(RolesStoreRequest $request)
    {
        
            $data = $request->validated();
            $role = Role::create(['name' => $data['role'], 'guard_name' => 'api']);
            $role->givePermissionTo($data['permissions']);
            $role->refresh();
            $roleTransformed = [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->map(function ($permission) {
                    return $permission->only(['id', 'name']); // Use only() to select fields
                }),
            ];
            return response()->json(['success'=>true,'data'=>$roleTransformed ], 201);
        
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
        $role = Role::find($id);
        $permissions = $role->permissions;
        $role->revokePermissionTo($permissions);
        $role->delete();
        return response()->json(['success'=>true], 200);
    }
}

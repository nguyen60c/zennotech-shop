<?php

namespace App\Http\Controllers\admin\roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /*
     * Display a listing roles
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy("id", "DESC")->paginate(5);
        return view("admin.roles.index", compact("roles"))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /*
     * Show the form for creating role
     */
    public function create()
    {
        $permissions = Permission::get();
        return view("admin.roles.create", compact("permissions"));
    }

    /*
     * Handle creating role
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(["name" => $request->get("name")]);
        $role->syncPermission($request->get("permission"));

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    /*
     * Display the specified role
     */
    public function show(Role $role)
    {
        $rolePermissions = $role->permissions;

        return view("admin.roles.show", compact("role", "rolePermissions"));
    }

    /*
     * Show the form for editing specified role
     */
    public function edit(Role $role)
    {
        $rolePermissions = $role->permissions->pluck("name")->toArray();
        $permissions = Permission::get();

        return view("admin.roles.edit", compact("rolePermissions", "permissions", "role"));
    }

    /*
     * Handle editing specified role
     */
    public function update(Role $role, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role->update($request->only("name"));

        $role->syncPermissions($request->get("permission"));

        return redirect()->route("routes.index")
            ->with('success', 'Role updated successfully');
    }

    /*
     * Remove the specified role
     */
    public function destroy(Role $role){
        $role->delete();

        return redirect()->route("routes.delete")
            ->with('success','Role deleted successfully');
    }
}

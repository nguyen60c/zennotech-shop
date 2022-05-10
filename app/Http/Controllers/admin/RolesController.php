<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    /*
     * Display a listing roles
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('roles.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');


        $roles = Role::orderBy("id", "DESC")->paginate(5);
        return view("admin.roles.index", compact("roles"))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /*
     * Show the form for creating role
     */
    public function create()
    {
        abort_if(Gate::denies('roles.create'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::get();
        return view("admin.roles.create", compact("permissions"));
    }

    /*
     * Handle creating role
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('roles.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

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
        abort_if(Gate::denies('roles.show'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rolePermissions = $role->permissions;

//        $rolePermissions

        return view("admin.roles.show", compact("role", "rolePermissions"));
    }

    /*
     * Show the form for editing specified role
     */
    public function edit(Role $role)
    {
        abort_if(Gate::denies('roles.edit'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rolePermissions = $role->permissions->pluck("name")->toArray();
        $permissions = Permission::get();

        return view("admin.roles.edit", compact("rolePermissions", "permissions", "role"));
    }

    /*
     * Handle editing specified role
     */
    public function update(Role $role, Request $request)
    {
        abort_if(Gate::denies('roles.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role->update($request->only("name"));

        $role->syncPermissions($request->get("permission"));

        return redirect()->route("roles.index")
            ->with('success', 'Role updated successfully');
    }

    /*
     * Remove the specified role
     */
    public function destroy(Role $role){
        abort_if(Gate::denies('roles.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return redirect()->route("routes.delete")
            ->with('success','Role deleted successfully');
    }
}

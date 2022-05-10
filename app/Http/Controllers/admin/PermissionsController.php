<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('permissions.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');


        $permissions = Permission::paginate(15);

        return view('admin.permissions.index', [
            'permissions' => $permissions
        ]);

    }

    /**
     * Show form for creating permissions
     */
    public function create()
    {
        abort_if(Gate::denies('permissions.create'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('permissions.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => 'required|unique:users,name'
        ]);

        Permission::create($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {

        abort_if(Gate::denies('permissions.edit'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.permissions.edit', [
            'permission' => $permission
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        abort_if(Gate::denies('permissions.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id
        ]);

        $permission->update($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {

        abort_if(Gate::denies('permissions.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permission->delete();

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission deleted successfully.'));
    }
}

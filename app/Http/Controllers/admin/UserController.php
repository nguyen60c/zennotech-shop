<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\users\UpdateUserRequest;
use App\Http\Requests\users\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    /*
     * Display all users
     */
    public function index(){
        abort_if(Gate::denies('admin.users.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::latest()->paginate(10);

        return view("admin.users.index",compact("users"));
    }

    /*
     * Show creating product form
     */
    public function create(){
        abort_if(Gate::denies('admin.users.create'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view("admin.users.create");
    }

    /*
     * Handle creating product form
     */
    public function store(User $_user, StoreUserRequest $request){
        abort_if(Gate::denies('admin.users.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = User::create(array_merge($request->validated(), [
            'password' => '123'
        ]));

        $user->assignRole("user");


        return redirect()->route('admin.users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /*
     * Show specified user data form
     */
    public function show(User $user)
    {
        abort_if(Gate::denies('admin.users.show'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    /*
     * Show edit form specified user data
     */
    public function edit(User $user)
    {
        abort_if(Gate::denies('admin.users.edit'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.users.edit', [
            'user' => $user,
            'userRole' => $user->roles->pluck('name')->toArray(),
            'roles' => Role::latest()->get()
        ]);
    }

    /**
     * Update user data
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        abort_if(Gate::denies('admin.users.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->update($request->validated());

        $user->syncRoles($request->get('role'));

        return redirect()->route('admin.users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     */
    public function destroy(User $user)
    {

        abort_if(Gate::denies('admin.users.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ok = $user->delete();

        return redirect()->route('admin.users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    /*
     * Display all users
     */
    public function index(){
        $users = User::latest()->paginate(15);

        return view("admin.users.index",compact("users"));
    }

    /*
     * Show creating product form
     */
    public function create(){
        return view("admin.users.create");
    }

    /*
     * Handle creating product form
     */
    public function store(User $user, StoreUserResponse $request){
        $user->create(array_merge($request->validated(), [
            'password' => 'test'
        ]));

        return redirect()->route('admin.users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /*
     * Show specified user data form
     */
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    /*
     * Show edit form specified user data
     */
    public function edit(User $user)
    {
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
        $user->update($request->validated());

        $user->syncRoles($request->get('role'));

        return redirect()->route('users.index')
            ->withSuccess(__('User updated successfully.'));
    }

    /**
     * Delete user data
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }
}

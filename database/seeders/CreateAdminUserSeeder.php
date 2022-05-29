<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone_number' => '+840987123543',
            'uid' => 'lH1USy6TyHU22I9F2k5jThhk5Vn2',
            'username' => 'admin',
            'password' => 'admin123'
        ]);

        $role = Role::create(['name' => 'admin']);

        $permissions = Permission::pluck('name')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}

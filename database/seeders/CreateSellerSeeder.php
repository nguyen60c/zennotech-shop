<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateSellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Seller',
            'email' => 'seller@gmail.com',
            'phone_number' => '+840983028691',
            'uid' => 'v7YP4OIm8ObT3Wf5tVMgDZIdDrN2',
            'username' => 'seller',
            'password' => 'seller123'
        ]);

        $role = Role::create(['name' => 'seller']);


        $user->assignRole([$role->id]);
    }
}

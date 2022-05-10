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
            'username' => 'seller',
            'password' => 'seller123'
        ]);

        $role = Role::create(['name' => 'seller']);


        $user->assignRole([$role->id]);
    }
}

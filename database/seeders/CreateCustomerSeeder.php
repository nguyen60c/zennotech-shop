<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class CreateCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'phone_number' => '+840983028690',
            'uid' => 'V8NTPCvvEXhKgvjabnNN5Ucq9s42',
            'username' => 'customer',
            'password' => 'customer123'
        ]);

        $role = Role::create(['name' => 'user']);


        $user->assignRole([$role->id]);
    }
}

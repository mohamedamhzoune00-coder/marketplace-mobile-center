<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@test.com',
            // password kaykhrej mn .env, machi mktoub b sara7a f code
            'password'  => bcrypt(env('ADMIN_SEED_PASSWORD', 'ChangeMe123!')),
            'role'      => 'super_admin',
            'telephone' => '0612345678',
            'actif'     => true,
        ]);
    }
}
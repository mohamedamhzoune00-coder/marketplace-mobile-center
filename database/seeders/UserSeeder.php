<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([

            // اسم المستخدم
            'name' => 'Admin',

            // الإيميل
            'email' => 'admin@test.com',

            // كلمة السر (خاصها تكون مشفرة)
            'password' => bcrypt('123456'),

            // الدور
            'role' => 'super_admin',

            // الهاتف
            'telephone' => '0612345678',

            // الحساب مفعل
            'actif' => true,

        ]);
    }
}
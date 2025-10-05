<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // ✅ إنشاء مستخدم Admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('12345678'), // 🔐 تأكد من تغييرها لاحقًا
            'role'     => 'admin',
        ]);

        // ✅ إنشاء مستخدم عادي
        User::create([
            'name'     => 'User',
            'email'    => 'user@example.com',
            'password' => Hash::make('12345678'), // 🔐 تأكد من تغييرها لاحقًا
            'role'     => 'user',
        ]);
    }
}

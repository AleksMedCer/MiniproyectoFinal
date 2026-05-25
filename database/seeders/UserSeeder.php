<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Isaac',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
                     'role' => 'admin',
        ]);
    }
}

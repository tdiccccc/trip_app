<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'たろう',
            'email' => 'taro@example.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'はなこ',
            'email' => 'hanako@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}

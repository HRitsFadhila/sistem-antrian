<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [   'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123')
            ],
        );
        $admin->assignRole('admin');

        $petugas = User::firstOrCreate(
            ['username' => 'harits'],
            [
                'name' => 'Harits Fadhila',
                'email' => 'rits.fadhila@gmail.com',
                'password' => Hash::make('12345678'),
            ],
        );
        $petugas->assignRole('petugas');
    }
}

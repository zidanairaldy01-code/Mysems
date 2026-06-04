<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@gmail.com',
            'role' => '1',
            'status' => 1,
            'hp' => '0812345678901',
            'password' => bcrypt('12345'),
        ]);
        User::create([
            'nama' => 'Sopian Aji',
            'email' => 'sopianaji@admin.com',
            'role' => '0',
            'status' => 1,
            'hp' => '0812345678902',
            'password' => bcrypt('elsa20'),
        ]);
    }
}


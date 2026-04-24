<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@biblioteca.pt',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'João Leitor',
            'email'    => 'joao@exemplo.pt',
            'password' => Hash::make('leitor123'),
            'role'     => 'leitor',
        ]);
    }
}
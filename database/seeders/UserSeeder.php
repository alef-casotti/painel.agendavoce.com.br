<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@agendavoce.com.br',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
    }
}

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

        // Criar usuário Financeiro
        User::create([
            'name' => 'Financeiro',
            'email' => 'financeiro@agendavoce.com.br',
            'password' => Hash::make('financeiro123'),
            'role' => 'financeiro',
        ]);

        // Criar usuário Suporte
        User::create([
            'name' => 'Suporte',
            'email' => 'suporte@agendavoce.com.br',
            'password' => Hash::make('suporte123'),
            'role' => 'suporte',
        ]);
    }
}

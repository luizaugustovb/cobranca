<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Criação do Admin Geral solicitado
        $admin = User::create([
            'name' => 'Admin Geral',
            'email' => 'contato@luizaugusto.me',
            'password' => Hash::make('Luiz2012@'),
            'is_admin' => true,
            'tenant_id' => null, // Admin Geral não pertence a nenhum tenant específico
        ]);

        // Atribui o papel (role) de Admin Geral
        $admin->assignRole('Admin Geral');
    }
}

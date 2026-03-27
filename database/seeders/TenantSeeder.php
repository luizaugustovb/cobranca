<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Criar um tenant de exemplo
        $tenant = Tenant::create([
            'name' => 'Exemplo Escritório de Cobrança',
            'slug' => 'exemplo-cobranca',
            'document' => '12.345.678/0001-99',
            'email' => 'financeiro@exemplo.com.br',
            'status' => 'active',
            'plan' => 'gold'
        ]);

        // Criar um usuário admin para este tenant
        $adminTenant = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Admin do Tenant',
            'email' => 'admin@exemplo.com.br',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $adminTenant->assignRole('Admin Tenant');
    }
}

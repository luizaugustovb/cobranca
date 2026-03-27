<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões básicas
        $permissions = [
            'view admin dashboard',
            'manage tenants',
            'manage users',
            'impersonate tenants',
            'view logs',
            'view settings',
            'manage clients',
            'manage debtors',
            'manage billing',
            'manage payments',
            'manage negotiations',
            'manage imports',
            'view reports'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar roles e atribuir permissões existentes
        
        // Admin Geral: Tem todas as permissões
        $adminGeral = Role::create(['name' => 'Admin Geral']);
        $adminGeral->givePermissionTo(Permission::all());

        // Admin Tenant: Gerencia tudo do seu tenant
        $adminTenant = Role::create(['name' => 'Admin Tenant']);
        $adminTenant->givePermissionTo([
            'manage users',
            'manage clients',
            'manage debtors',
            'manage billing',
            'manage payments',
            'manage negotiations',
            'manage imports',
            'view reports'
        ]);

        // Gestor: Quase um admin de tenant, mas sem gestão de usuários
        $gestor = Role::create(['name' => 'Gestor']);
        $gestor->givePermissionTo([
            'manage clients',
            'manage debtors',
            'manage billing',
            'manage payments',
            'manage negotiations',
            'view reports'
        ]);

        // Cobrador: Operacional de cobrança
        $cobrador = Role::create(['name' => 'Cobrador']);
        $cobrador->givePermissionTo([
            'manage debtors',
            'manage billing',
            'manage negotiations'
        ]);

        // Financeiro: Baixas e conferência
        $financeiro = Role::create(['name' => 'Financeiro']);
        $financeiro->givePermissionTo([
            'manage payments',
            'view reports'
        ]);

        // Cliente Externo: Acesso restrito
        $clienteExterno = Role::create(['name' => 'Cliente Externo']);
    }
}

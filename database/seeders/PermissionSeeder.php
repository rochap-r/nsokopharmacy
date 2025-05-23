<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions pour la gestion des utilisateurs
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'manage-users', // Permission spéciale pour accéder à la page de gestion des utilisateurs
        ];

        // Permissions pour la gestion des rôles
        $rolePermissions = [
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'manage-roles', // Permission spéciale pour accéder à la page de gestion des rôles
        ];

        // Permissions pour la gestion des produits
        $productPermissions = [
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
        ];

        // Permissions pour le module catalogue
        $catalogPermissions = [
            'catalog.view',
            'catalog.create',
            'catalog.edit',
            'catalog.delete',
            'catalog.import',
            'catalog.export',
            'catalog.manage',
        ];

        // Permissions pour la gestion des stocks
        $stockPermissions = [
            'stocks.view',
            'stocks.create',
            'stocks.edit',
            'stocks.delete',
        ];

        // Permissions pour la gestion des ventes
        $salePermissions = [
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
        ];

        // Permissions pour la gestion des rapports
        $reportPermissions = [
            'reports.view',
            'reports.create',
            'reports.export',
        ];

        // Permissions pour les paramètres
        $settingPermissions = [
            'settings.view',
            'settings.edit',
        ];

        // Toutes les permissions
        $allPermissions = array_merge(
            $userPermissions,
            $rolePermissions,
            $productPermissions,
            $catalogPermissions,
            $stockPermissions,
            $salePermissions,
            $reportPermissions,
            $settingPermissions
        );

        // Créer toutes les permissions
        foreach ($allPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
        $firstUser = User::first();

        // S'assurer que le rôle Admin existe et a toutes les permissions
        $adminRole = Role::firstOrCreate(['name' => 'Root-'.$firstUser->tenant_id, 'guard_name' => 'web','tenant_id'=>$firstUser->tenant_id]);
        $adminRole->syncPermissions(Permission::all());

        // S'assurer que le premier utilisateur a le rôle Admin

        if ($firstUser) {
            $firstUser->assignRole($adminRole);
        }

        $this->command->info('Permissions et rôles créés avec succès.');
    }
}

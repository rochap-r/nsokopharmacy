<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
        ]);

        //User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Rodrigue CHOT',
            'email' => 'rodriguechot@gmail.com',
        ]);


        $this->call([
        PermissionSeeder::class,]);

        // Les permissions sont maintenant gérées par le PermissionSeeder

        $role = Role::firstOrCreate(['name' => 'Root-'.$user->tenant_id, 'tenant_id' => $user->tenant_id]);
        $role->syncPermissions(Permission::all());
        $user->assignRole($role);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // for multi-tenancy
        $t1=Tenant::create([
            'name' => 'Root',
            'domain'=>'root000',
            'address' => 'Address1',
            'phone' => '123456789',]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            ConfigurationSeeder::class,
            CompanySeeder::class,
            MediaSeeder::class,
            UnitSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            ProjectSeeder::class,
            KnowledgeSeeder::class,
            QuotationSeeder::class,
            ClientSeeder::class,
            PortalSeeder::class,
            CommunicationSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            \Database\Seeders\UnitSeeder::class,
            \Database\Seeders\GroupSeeder::class,
            \Database\Seeders\PageSeeder::class,
            \Database\Seeders\GroupPageSeeder::class,
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\CompanySeeder::class,
            \Database\Seeders\ShiftSeeder::class,
            \Database\Seeders\ProductSeeder::class,
            \Database\Seeders\ProductVariantSeeder::class,
            \Database\Seeders\MenuItemSeeder::class,
            \Database\Seeders\SystemSettingSeeder::class,
        ]);
    }
}

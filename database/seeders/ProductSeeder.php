<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $unitPcs = Unit::where('unit_code', 'PCS')->first();
        if (!$unitPcs) {
            $this->command->error('❌ Unit "PCS" not found. Please run UnitSeeder first.');
            return;
        }

        $companies = Company::whereIn('company_name', [
            'My Fried Chicken',
            'Raja Kepiting',
            'Ayam Bebek Ganza'
        ])->get()->keyBy('company_name');

        if ($companies->count() < 3) {
            $this->command->error('❌ Companies not found. Please run CompanySeeder first.');
            return;
        }

        $mfc = $companies->get('My Fried Chicken');
        $raja = $companies->get('Raja Kepiting');
        $ganza = $companies->get('Ayam Bebek Ganza');

        $this->command->info('📦 Seeding products...');

        // ============================================================
        // MY FRIED CHICKEN
        // ============================================================

        // 1. Ayam (has_variant = true) - 5 varian sesuai gambar
        Product::updateOrCreate(
            ['company_id' => $mfc->id, 'product_code' => 'AYM_MFC'],
            [
                'unit_id' => $unitPcs->id,
                'product_name' => 'Ayam',
                'description' => 'Ayam segar untuk menu My Fried Chicken',
                'has_variant' => true,
                'is_active' => true,
            ]
        );

        // 2. Lele (has_variant = false)
        Product::updateOrCreate(
            ['company_id' => $mfc->id, 'product_code' => 'LLE_MFC'],
            [
                'unit_id' => $unitPcs->id,
                'product_name' => 'Lele',
                'description' => 'Lele segar',
                'has_variant' => false,
                'is_active' => true,
            ]
        );

        // 3. Snack (has_variant = true) - Kentang, Singkong, Nugget
        Product::updateOrCreate(
            ['company_id' => $mfc->id, 'product_code' => 'SNK_MFC'],
            [
                'unit_id' => $unitPcs->id,
                'product_name' => 'Snack',
                'description' => 'Aneka snack',
                'has_variant' => true,
                'is_active' => true,
            ]
        );

        // 4. Minuman (has_variant = true) - hanya Es Teh Manis
        Product::updateOrCreate(
            ['company_id' => $mfc->id, 'product_code' => 'DRK_MFC'],
            [
                'unit_id' => $unitPcs->id,
                'product_name' => 'Minuman',
                'description' => 'Minuman segar',
                'has_variant' => true,
                'is_active' => true,
            ]
        );

        // 5. Additional (has_variant = true) - saus-saus
        Product::updateOrCreate(
            ['company_id' => $mfc->id, 'product_code' => 'ADD_MFC'],
            [
                'unit_id' => $unitPcs->id,
                'product_name' => 'Additional',
                'description' => 'Aneka saus tambahan',
                'has_variant' => true,
                'is_active' => true,
            ]
        );

        // ============================================================
        // RAJA KEPITING
        // ============================================================
        Product::updateOrCreate(
            ['company_id' => $raja->id, 'product_code' => 'AYM_RJ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Ayam', 'description' => 'Ayam untuk Raja Kepiting', 'has_variant' => true, 'is_active' => true]
        );
        Product::updateOrCreate(
            ['company_id' => $raja->id, 'product_code' => 'BDK_RJ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Bebek', 'description' => 'Bebek segar', 'has_variant' => false, 'is_active' => true]
        );
        Product::updateOrCreate(
            ['company_id' => $raja->id, 'product_code' => 'SFD_RJ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Seafood', 'description' => 'Aneka seafood', 'has_variant' => true, 'is_active' => true]
        );

        // ============================================================
        // AYAM BEBEK GANZA
        // ============================================================
        Product::updateOrCreate(
            ['company_id' => $ganza->id, 'product_code' => 'CRS_GZ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Ayam Crispy', 'description' => 'Ayam crispy khas Ganza', 'has_variant' => true, 'is_active' => true]
        );
        Product::updateOrCreate(
            ['company_id' => $ganza->id, 'product_code' => 'GAN_GZ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Ayam Ganza', 'description' => 'Ayam ganza (besar/kecil)', 'has_variant' => true, 'is_active' => true]
        );
        Product::updateOrCreate(
            ['company_id' => $ganza->id, 'product_code' => 'BDK_GZ'],
            ['unit_id' => $unitPcs->id, 'product_name' => 'Bebek', 'description' => 'Bebek segar', 'has_variant' => false, 'is_active' => true]
        );

        $this->command->info('✅ Products seeded successfully!');
    }
}
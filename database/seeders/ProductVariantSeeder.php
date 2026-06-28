<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::whereIn('company_name', [
            'My Fried Chicken',
            'Raja Kepiting',
            'Ayam Bebek Ganza'
        ])->get()->keyBy('company_name');

        if ($companies->count() < 3) {
            $this->command->error('❌ Companies not found.');
            return;
        }

        $mfc = $companies->get('My Fried Chicken');
        $raja = $companies->get('Raja Kepiting');
        $ganza = $companies->get('Ayam Bebek Ganza');

        $this->command->info('🧬 Seeding product variants and stocks...');

        // ============================================================
        // MY FRIED CHICKEN
        // ============================================================

        // 1a. Ayam MFC - 5 varian sesuai gambar
        $productAyamMfc = Product::where('company_id', $mfc->id)->where('product_code', 'AYM_MFC')->first();
        if ($productAyamMfc) {
            $variants = [
                ['name' => 'Ayam Geprek',       'code' => 'MFC_GEP', 'purchase' => 8400, 'selling' => 12000],
                ['name' => 'Ayam Geprek Keju',  'code' => 'MFC_KEJ', 'purchase' => 10500, 'selling' => 15000],
                ['name' => 'Ayam Lada Hitam',   'code' => 'MFC_LDH', 'purchase' => 9100, 'selling' => 13000],
                ['name' => 'Ayam Saus BBQ',     'code' => 'MFC_BBQ', 'purchase' => 9100, 'selling' => 13000],
                ['name' => 'Ayam Keju',         'code' => 'MFC_KJ2', 'purchase' => 10500, 'selling' => 15000],
            ];
            foreach ($variants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productAyamMfc->id, 'variant_code' => $v['code']],
                    ['variant_name' => $v['name'], 'purchase_price' => $v['purchase'], 'selling_price' => $v['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        // 1b. Lele MFC - 1 varian
        $productLele = Product::where('company_id', $mfc->id)->where('product_code', 'LLE_MFC')->first();
        if ($productLele) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productLele->id, 'variant_code' => 'LLE'],
                ['variant_name' => 'Lele Goreng', 'purchase_price' => 8000, 'selling_price' => 13000, 'is_active' => true]
            );
            Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
        }

        // 1c. Snack MFC - Kentang, Singkong, Nugget
        $productSnack = Product::where('company_id', $mfc->id)->where('product_code', 'SNK_MFC')->first();
        if ($productSnack) {
            $snacks = [
                ['name' => 'Kentang Goreng', 'code' => 'KNT', 'purchase' => 5000, 'selling' => 8000],
                ['name' => 'Singkong Goreng', 'code' => 'SKG', 'purchase' => 5000, 'selling' => 8000],
                ['name' => 'Nugget',          'code' => 'NUG', 'purchase' => 6000, 'selling' => 10000],
            ];
            foreach ($snacks as $s) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productSnack->id, 'variant_code' => $s['code']],
                    ['variant_name' => $s['name'], 'purchase_price' => $s['purchase'], 'selling_price' => $s['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        // 1d. Minuman MFC - hanya Es Teh Manis
        $productDrink = Product::where('company_id', $mfc->id)->where('product_code', 'DRK_MFC')->first();
        if ($productDrink) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productDrink->id, 'variant_code' => 'ETM'],
                ['variant_name' => 'Es Teh Manis', 'purchase_price' => 3000, 'selling_price' => 5000, 'is_active' => true]
            );
            Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
        }

        // 1e. Additional MFC - Saus
        $productAdd = Product::where('company_id', $mfc->id)->where('product_code', 'ADD_MFC')->first();
        if ($productAdd) {
            $adds = [
                ['name' => 'Saus BBQ',        'code' => 'SBBQ', 'purchase' => 3000, 'selling' => 5000],
                ['name' => 'Saus Lada Hitam', 'code' => 'SLH',  'purchase' => 3000, 'selling' => 5000],
                ['name' => 'Saus Keju',       'code' => 'SKEJ', 'purchase' => 3000, 'selling' => 5000],
                ['name' => 'Chili Oil',       'code' => 'CHO',  'purchase' => 3000, 'selling' => 5000],
            ];
            foreach ($adds as $a) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productAdd->id, 'variant_code' => $a['code']],
                    ['variant_name' => $a['name'], 'purchase_price' => $a['purchase'], 'selling_price' => $a['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        // ============================================================
        // RAJA KEPITING
        // ============================================================
        $productAyamRj = Product::where('company_id', $raja->id)->where('product_code', 'AYM_RJ')->first();
        if ($productAyamRj) {
            $variants = [
                ['name' => 'Ayam Besar', 'code' => 'RJ_BR', 'purchase' => 17500, 'selling' => 25000],
                ['name' => 'Ayam Kecil', 'code' => 'RJ_KR', 'purchase' => 12600, 'selling' => 18000],
            ];
            foreach ($variants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productAyamRj->id, 'variant_code' => $v['code']],
                    ['variant_name' => $v['name'], 'purchase_price' => $v['purchase'], 'selling_price' => $v['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        $productBebekRj = Product::where('company_id', $raja->id)->where('product_code', 'BDK_RJ')->first();
        if ($productBebekRj) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productBebekRj->id, 'variant_code' => 'BDK'],
                ['variant_name' => 'Bebek Goreng', 'purchase_price' => 30000, 'selling_price' => 45000, 'is_active' => true]
            );
            Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
        }

        $productSeafood = Product::where('company_id', $raja->id)->where('product_code', 'SFD_RJ')->first();
        if ($productSeafood) {
            $seafoods = [
                ['name' => 'Cumi',           'code' => 'CUM', 'purchase' => 21000, 'selling' => 35000],
                ['name' => 'Kepiting',       'code' => 'KEP', 'purchase' => 30000, 'selling' => 50000],
                ['name' => 'Kerang Dara',    'code' => 'KDR', 'purchase' => 15000, 'selling' => 25000],
                ['name' => 'Kerang Simping', 'code' => 'KSP', 'purchase' => 16800, 'selling' => 28000],
                ['name' => 'Kerang Hijau',   'code' => 'KHJ', 'purchase' => 13200, 'selling' => 22000],
            ];
            foreach ($seafoods as $s) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productSeafood->id, 'variant_code' => $s['code']],
                    ['variant_name' => $s['name'], 'purchase_price' => $s['purchase'], 'selling_price' => $s['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        // ============================================================
        // AYAM BEBEK GANZA
        // ============================================================
        $productCrispy = Product::where('company_id', $ganza->id)->where('product_code', 'CRS_GZ')->first();
        if ($productCrispy) {
            $crispyVariants = [
                ['name' => 'Ayam Crispy Original', 'code' => 'GZ_ORI', 'purchase' => 8400, 'selling' => 12000],
                ['name' => 'Ayam Crispy Pedas',    'code' => 'GZ_PDS', 'purchase' => 9100, 'selling' => 13000],
                ['name' => 'Ayam Crispy Keju',     'code' => 'GZ_KEJ', 'purchase' => 10500, 'selling' => 15000],
                ['name' => 'Ayam Crispy BBQ',      'code' => 'GZ_BBQ', 'purchase' => 9800, 'selling' => 14000],
            ];
            foreach ($crispyVariants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productCrispy->id, 'variant_code' => $v['code']],
                    ['variant_name' => $v['name'], 'purchase_price' => $v['purchase'], 'selling_price' => $v['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        $productGanza = Product::where('company_id', $ganza->id)->where('product_code', 'GAN_GZ')->first();
        if ($productGanza) {
            $ganzaVariants = [
                ['name' => 'Ayam Ganza Besar', 'code' => 'GZ_BR', 'purchase' => 17500, 'selling' => 25000],
                ['name' => 'Ayam Ganza Kecil', 'code' => 'GZ_KR', 'purchase' => 12600, 'selling' => 18000],
            ];
            foreach ($ganzaVariants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $productGanza->id, 'variant_code' => $v['code']],
                    ['variant_name' => $v['name'], 'purchase_price' => $v['purchase'], 'selling_price' => $v['selling'], 'is_active' => true]
                );
                Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
            }
        }

        $productBebekGz = Product::where('company_id', $ganza->id)->where('product_code', 'BDK_GZ')->first();
        if ($productBebekGz) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productBebekGz->id, 'variant_code' => 'BDK_GZ'],
                ['variant_name' => 'Bebek Goreng', 'purchase_price' => 30000, 'selling_price' => 45000, 'is_active' => true]
            );
            Stock::updateOrCreate(['product_variant_id' => $variant->id], ['current_stock' => rand(10, 100)]);
        }

        $this->command->info('✅ Product variants and stocks seeded successfully!');
    }
}
<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\MenuItem;
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

        $this->command->info('🧬 Seeding product variants, stocks, and menu items...');

        // Helper: generate stok per menu sesuai distribusi
        $getPerMenuStock = function () {
            $roll = rand(1, 100);
            if ($roll <= 1) {
                return 0; // out of stock (1%)
            } elseif ($roll <= 10) {
                return rand(1, 25); // low stock (9%)
            } else {
                return rand(26, 100); // available (90%)
            }
        };

        // Helper: create or update menu item (tanpa hardcode stock/status)
        $findOrCreateMenuItem = function ($companyId, $name, $price, $category = 'food') {
            $item = MenuItem::where('company_id', $companyId)->where('name', $name)->first();
            if (!$item) {
                $item = MenuItem::create([
                    'company_id'         => $companyId,
                    'product_variant_id' => null,
                    'name'               => $name,
                    'price'              => $price,
                    'category'           => $category,
                    'status'             => 'available', // sementara, akan di-sync
                    'image'              => null,
                    'stock'              => null,
                ]);
                $this->command->line("   ➕ Created menu item: {$name}");
            }
            return $item;
        };

        // ============================================================
        // MY FRIED CHICKEN
        // ============================================================

        // --- 1. AYAM MFC (5 menu, 1 variant) ---
        $productAyamMfc = Product::where('company_id', $mfc->id)->where('product_code', 'AYM_MFC')->first();
        if ($productAyamMfc) {
            // Buat variant
            $variantAyam = ProductVariant::updateOrCreate(
                ['product_id' => $productAyamMfc->id, 'variant_code' => 'MFC_AYAM'],
                [
                    'variant_name'   => 'Ayam Goreng Krispi',
                    'purchase_price' => 8400,
                    'selling_price'  => 13000,
                    'is_active'      => true
                ]
            );

            // Daftar menu ayam
            $ayamMenus = [
                ['name' => 'Ayam Geprek',       'price' => 12000],
                ['name' => 'Ayam Geprek Keju',  'price' => 15000],
                ['name' => 'Ayam Lada Hitam',   'price' => 13000],
                ['name' => 'Ayam Saus BBQ',     'price' => 13000],
                ['name' => 'Ayam Keju',         'price' => 15000],
            ];

            // Buat/update menu item, tautkan ke variant
            foreach ($ayamMenus as $menuData) {
                $menuItem = $findOrCreateMenuItem($mfc->id, $menuData['name'], $menuData['price'], 'food');
                $menuItem->product_variant_id = $variantAyam->id;
                $menuItem->save();
            }

            // Tentukan stok per menu menggunakan distribusi
            $perMenuStock = $getPerMenuStock();
            $totalStock = $perMenuStock * count($ayamMenus); // total variant stock
            Stock::updateOrCreate(
                ['product_variant_id' => $variantAyam->id],
                ['current_stock' => $totalStock]
            );
        }

        // --- 2. LELE MFC (1 menu) ---
        $productLele = Product::where('company_id', $mfc->id)->where('product_code', 'LLE_MFC')->first();
        if ($productLele) {
            $variantLele = ProductVariant::updateOrCreate(
                ['product_id' => $productLele->id, 'variant_code' => 'LLE'],
                ['variant_name' => 'Lele Goreng', 'purchase_price' => 8000, 'selling_price' => 13000, 'is_active' => true]
            );
            $menuItem = $findOrCreateMenuItem($mfc->id, 'Lele Goreng', 13000, 'food');
            $menuItem->product_variant_id = $variantLele->id;
            $menuItem->save();

            $perMenuStock = $getPerMenuStock();
            Stock::updateOrCreate(
                ['product_variant_id' => $variantLele->id],
                ['current_stock' => $perMenuStock]
            );
        }

        // --- 3. SNACK MFC (masing-masing 1 menu) ---
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
                $menuItem = $findOrCreateMenuItem($mfc->id, $s['name'], $s['selling'], 'snack');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // --- 4. MINUMAN MFC (1 menu) ---
        $productDrink = Product::where('company_id', $mfc->id)->where('product_code', 'DRK_MFC')->first();
        if ($productDrink) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productDrink->id, 'variant_code' => 'ETM'],
                ['variant_name' => 'Es Teh Manis', 'purchase_price' => 3000, 'selling_price' => 5000, 'is_active' => true]
            );
            $menuItem = $findOrCreateMenuItem($mfc->id, 'Es Teh Manis', 5000, 'drink');
            $menuItem->product_variant_id = $variant->id;
            $menuItem->save();

            $perMenuStock = $getPerMenuStock();
            Stock::updateOrCreate(
                ['product_variant_id' => $variant->id],
                ['current_stock' => $perMenuStock]
            );
        }

        // --- 5. ADDITIONAL MFC (masing-masing 1 menu) ---
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
                $menuItem = $findOrCreateMenuItem($mfc->id, $a['name'], $a['selling'], 'additional');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // ============================================================
        // RAJA KEPITING
        // ============================================================
        // Ayam Raja (masing-masing 1 menu)
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
                $menuItem = $findOrCreateMenuItem($raja->id, $v['name'], $v['selling'], 'food');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // Bebek Raja (1 menu)
        $productBebekRj = Product::where('company_id', $raja->id)->where('product_code', 'BDK_RJ')->first();
        if ($productBebekRj) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productBebekRj->id, 'variant_code' => 'BDK'],
                ['variant_name' => 'Bebek Goreng', 'purchase_price' => 30000, 'selling_price' => 45000, 'is_active' => true]
            );
            $menuItem = $findOrCreateMenuItem($raja->id, 'Bebek Goreng', 45000, 'food');
            $menuItem->product_variant_id = $variant->id;
            $menuItem->save();

            $perMenuStock = $getPerMenuStock();
            Stock::updateOrCreate(
                ['product_variant_id' => $variant->id],
                ['current_stock' => $perMenuStock]
            );
        }

        // Seafood Raja (masing-masing 1 menu)
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
                $menuItem = $findOrCreateMenuItem($raja->id, $s['name'], $s['selling'], 'food');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // ============================================================
        // AYAM BEBEK GANZA
        // ============================================================
        // Crispy Ganza (masing-masing 1 menu)
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
                $menuItem = $findOrCreateMenuItem($ganza->id, $v['name'], $v['selling'], 'food');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // Ayam Ganza (masing-masing 1 menu)
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
                $menuItem = $findOrCreateMenuItem($ganza->id, $v['name'], $v['selling'], 'food');
                $menuItem->product_variant_id = $variant->id;
                $menuItem->save();

                $perMenuStock = $getPerMenuStock();
                Stock::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    ['current_stock' => $perMenuStock]
                );
            }
        }

        // Bebek Ganza (1 menu)
        $productBebekGz = Product::where('company_id', $ganza->id)->where('product_code', 'BDK_GZ')->first();
        if ($productBebekGz) {
            $variant = ProductVariant::updateOrCreate(
                ['product_id' => $productBebekGz->id, 'variant_code' => 'BDK_GZ'],
                ['variant_name' => 'Bebek Goreng', 'purchase_price' => 30000, 'selling_price' => 45000, 'is_active' => true]
            );
            $menuItem = $findOrCreateMenuItem($ganza->id, 'Bebek Goreng', 45000, 'food');
            $menuItem->product_variant_id = $variant->id;
            $menuItem->save();

            $perMenuStock = $getPerMenuStock();
            Stock::updateOrCreate(
                ['product_variant_id' => $variant->id],
                ['current_stock' => $perMenuStock]
            );
        }

        // ============================================================
        // FINAL SYNC: Update menu item statuses & stock berdasarkan variant stock
        // ============================================================
        $this->command->info('🔄 Syncing menu item statuses and stock...');

        $menuItems = MenuItem::whereNotNull('product_variant_id')->get();

        if ($menuItems->isEmpty()) {
            $this->command->warn('⚠️ No menu items with product_variant_id found.');
        } else {
            $grouped = $menuItems->groupBy('product_variant_id');
            $updatedCount = 0;

            foreach ($grouped as $variantId => $items) {
                $variant = ProductVariant::find($variantId);
                if (!$variant) {
                    $this->command->warn("⚠️ Variant ID $variantId not found.");
                    continue;
                }

                $stock = Stock::where('product_variant_id', $variantId)->first();
                $totalStock = $stock ? (int) $stock->current_stock : 0;
                $count = $items->count();
                $perMenuStock = $count > 0 ? floor($totalStock / $count) : 0;

                $this->command->line("   Variant: {$variant->variant_name} (ID: $variantId), total stock: $totalStock, items: $count, per menu: $perMenuStock");

                foreach ($items as $menuItem) {
                    // Tentukan status
                    if ($perMenuStock <= 0) {
                        $status = 'out';
                    } elseif ($perMenuStock <= 25) {
                        $status = 'low';
                    } else {
                        $status = 'available';
                    }

                    // Update status dan stock
                    $changed = false;
                    if ($menuItem->status !== $status) {
                        $menuItem->status = $status;
                        $changed = true;
                    }
                    if ($menuItem->stock !== $perMenuStock) {
                        $menuItem->stock = $perMenuStock;
                        $changed = true;
                    }
                    if ($changed) {
                        $menuItem->save();
                        $updatedCount++;
                        $this->command->line("      Updated {$menuItem->name} to status: $status, stock: $perMenuStock");
                    }
                }
            }

            $this->command->info("✅ Updated $updatedCount menu items (status & stock).");
        }

        // Untuk menu tanpa variant, set stock = 0 dan status available
        $noVariant = MenuItem::whereNull('product_variant_id')->update([
            'status' => 'available',
            'stock'  => 0,
        ]);
        if ($noVariant > 0) {
            $this->command->info("✅ Updated $noVariant menu items without variant to available with stock 0.");
        }

        $this->command->info('🎉 Seeding completed.');
    }
}

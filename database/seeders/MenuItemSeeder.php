<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\MenuItem;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
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

        $this->command->info('📋 Seeding menu items...');

        // ============================================================
        // MY FRIED CHICKEN
        // ============================================================

        // 1. Ayam MFC (5 menu)
        $ayamMfc = ProductVariant::whereHas('product', function ($q) use ($mfc) {
            $q->where('company_id', $mfc->id)->where('product_code', 'AYM_MFC');
        })->get();
        foreach ($ayamMfc as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $mfc->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // 2. Lele MFC (1 menu)
        $lele = ProductVariant::whereHas('product', function ($q) use ($mfc) {
            $q->where('company_id', $mfc->id)->where('product_code', 'LLE_MFC');
        })->first();
        if ($lele) {
            MenuItem::updateOrCreate(
                ['company_id' => $mfc->id, 'product_variant_id' => $lele->id],
                ['name' => 'Lele Goreng', 'price' => 13000, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // 3. Snack MFC (3 menu)
        $snacks = ProductVariant::whereHas('product', function ($q) use ($mfc) {
            $q->where('company_id', $mfc->id)->where('product_code', 'SNK_MFC');
        })->get();
        foreach ($snacks as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $mfc->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'snack', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // 4. Minuman MFC (1 menu: Es Teh Manis)
        $drink = ProductVariant::whereHas('product', function ($q) use ($mfc) {
            $q->where('company_id', $mfc->id)->where('product_code', 'DRK_MFC');
        })->first();
        if ($drink) {
            MenuItem::updateOrCreate(
                ['company_id' => $mfc->id, 'product_variant_id' => $drink->id],
                ['name' => 'Es Teh Manis', 'price' => 5000, 'category' => 'drink', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // 5. Additional MFC (4 menu)
        $adds = ProductVariant::whereHas('product', function ($q) use ($mfc) {
            $q->where('company_id', $mfc->id)->where('product_code', 'ADD_MFC');
        })->get();
        foreach ($adds as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $mfc->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'additional', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // ============================================================
        // RAJA KEPITING
        // ============================================================
        $ayamRj = ProductVariant::whereHas('product', function ($q) use ($raja) {
            $q->where('company_id', $raja->id)->where('product_code', 'AYM_RJ');
        })->get();
        foreach ($ayamRj as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $raja->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        $bebekRj = ProductVariant::whereHas('product', function ($q) use ($raja) {
            $q->where('company_id', $raja->id)->where('product_code', 'BDK_RJ');
        })->first();
        if ($bebekRj) {
            MenuItem::updateOrCreate(
                ['company_id' => $raja->id, 'product_variant_id' => $bebekRj->id],
                ['name' => 'Bebek Goreng', 'price' => 45000, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        $seafoods = ProductVariant::whereHas('product', function ($q) use ($raja) {
            $q->where('company_id', $raja->id)->where('product_code', 'SFD_RJ');
        })->get();
        foreach ($seafoods as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $raja->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        // ============================================================
        // AYAM BEBEK GANZA
        // ============================================================
        $crispy = ProductVariant::whereHas('product', function ($q) use ($ganza) {
            $q->where('company_id', $ganza->id)->where('product_code', 'CRS_GZ');
        })->get();
        foreach ($crispy as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $ganza->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        $ganzaAyam = ProductVariant::whereHas('product', function ($q) use ($ganza) {
            $q->where('company_id', $ganza->id)->where('product_code', 'GAN_GZ');
        })->get();
        foreach ($ganzaAyam as $v) {
            MenuItem::updateOrCreate(
                ['company_id' => $ganza->id, 'product_variant_id' => $v->id],
                ['name' => $v->variant_name, 'price' => $v->selling_price, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        $bebekGz = ProductVariant::whereHas('product', function ($q) use ($ganza) {
            $q->where('company_id', $ganza->id)->where('product_code', 'BDK_GZ');
        })->first();
        if ($bebekGz) {
            MenuItem::updateOrCreate(
                ['company_id' => $ganza->id, 'product_variant_id' => $bebekGz->id],
                ['name' => 'Bebek Goreng', 'price' => 45000, 'category' => 'food', 'status' => 'available', 'stock' => rand(10, 100)]
            );
        }

        $this->command->info('✅ Menu items seeded successfully!');
    }
}
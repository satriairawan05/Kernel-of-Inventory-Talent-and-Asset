<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // --- COUNTABLE & PACKAGING UNITS (Satuan Jumlah & Kemasan) ---
            [
                'unit_name' => 'Pieces',
                'unit_code' => 'PCS',
                'description' => 'Standard unit for counting individual items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Box',
                'unit_code' => 'BOX',
                'description' => 'Box packaging unit for bulk or bundled items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Pack',
                'unit_code' => 'PCK',
                'description' => 'Pack or sachet packaging unit.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Carton',
                'unit_code' => 'CTN',
                'description' => 'Large outer cardboard box containing multiple packs or boxes.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Dozen',
                'unit_code' => 'DZN',
                'description' => 'Unit of measurement consisting of twelve (12) items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Set',
                'unit_code' => 'SET',
                'description' => 'Unit for items sold together as a group, kit, or collection.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Pair',
                'unit_code' => 'PR',
                'description' => 'Unit for items consisting of two matched parts (e.g., shoes, gloves).',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Roll',
                'unit_code' => 'ROL',
                'description' => 'Unit for continuous items wound around a core (e.g., tape, fabrics, cables).',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Sack',
                'unit_code' => 'SCK',
                'description' => 'Large bag unit typically used for bulk commodities like rice, flour, or charcoal.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Bundle',
                'unit_code' => 'BDL',
                'description' => 'Unit for a collection of items fastened, tied, or wrapped together.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Pallet',
                'unit_code' => 'PLT',
                'description' => 'Large structural foundation unit used for warehouse bulk logistics.',
                'is_active' => true,
            ],

            // --- WEIGHT UNITS (Satuan Berat untuk Bahan Baku/Grosir) ---
            [
                'unit_name' => 'Kilogram',
                'unit_code' => 'KG',
                'description' => 'Weight unit for raw materials or heavy items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Gram',
                'unit_code' => 'GR',
                'description' => 'Weight unit for small ingredients or items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Milligram',
                'unit_code' => 'MG',
                'description' => 'Very small weight unit used for precise pharmaceutical or chemical items.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Ton',
                'unit_code' => 'TON',
                'description' => 'Heavy weight unit equivalent to 1,000 kilograms.',
                'is_active' => true,
            ],

            // --- VOLUME & LIQUID UNITS (Satuan Volume Cairan/F&B) ---
            [
                'unit_name' => 'Bottle',
                'unit_code' => 'BTL',
                'description' => 'Unit for liquid packaging or beverages.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Litre',
                'unit_code' => 'LTR',
                'description' => 'Volume unit for liquids, oils, or beverages.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Millilitre',
                'unit_code' => 'ML',
                'description' => 'Small volume unit for liquid ingredients, perfumes, or small drinks.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Gallon',
                'unit_code' => 'GAL',
                'description' => 'Large volume container unit for bulk liquids or mineral water.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Can',
                'unit_code' => 'CAN',
                'description' => 'Cylindrical metal container unit for beverages or preserved food.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Tube',
                'unit_code' => 'TUB',
                'description' => 'Squeezable cylindrical container unit for creams, gels, pomade, or pastes.',
                'is_active' => true,
            ],

            // --- DIMENSION UNITS (Satuan Panjang & Luas) ---
            [
                'unit_name' => 'Meter',
                'unit_code' => 'M',
                'description' => 'Length unit for items sold by length (e.g., custom cables, fabrics, ropes).',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Centimeter',
                'unit_code' => 'CM',
                'description' => 'Smaller length unit for precise custom dimension measurements.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Square Meter',
                'unit_code' => 'M2',
                'description' => 'Area unit for items sold by surface space (e.g., carpets, custom banners, tiles).',
                'is_active' => true,
            ],
        ];

        \Illuminate\Support\Facades\DB::table('units')->insert($units);
    }
}

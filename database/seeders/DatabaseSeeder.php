<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@aam-group.com',
            'password' => bcrypt('password'),
        ]);

        $units = [
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
                'unit_name' => 'Bottle',
                'unit_code' => 'BTL',
                'description' => 'Unit for liquid packaging or beverages.',
                'is_active' => true,
            ],
            [
                'unit_name' => 'Service',
                'unit_code' => 'SRV',
                'description' => 'Unit for services rendered (suitable for barbershop treatments).',
                'is_active' => true,
            ],
        ];

        DB::table('units')->insert($units);

        // 1. Insert Companies individually to retrieve their specific IDs
        $cateringId = DB::table('companies')->insertGetId([
            'company_name' => 'Nusantara Delight Catering',
            'company_email' => 'info@nusantaradelight.com',
            'company_phone' => '081234567890',
            'company_address' => 'Jl. Kuliner Raya No. 12, Jakarta',
            'business_type' => 'Restaurant',
        ]);

        $gadgetId = DB::table('companies')->insertGetId([
            'company_name' => 'Gadget Zone Accessories',
            'company_email' => 'support@gadgetzone.id',
            'company_phone' => '089876543210',
            'company_address' => 'Mall ITC Roxy Mas Lantai 2, Jakarta',
            'business_type' => 'Phone Store',
        ]);

        $barberId = DB::table('companies')->insertGetId([
            'company_name' => 'The Classic Cut Barbershop',
            'company_email' => 'contact@classiccut.com',
            'company_phone' => '085511223344',
            'company_address' => 'Jl. Jenderal Sudirman No. 45, Bandung',
            'business_type' => 'Barbershop',
        ]);

        // 2. Map Categories to their respective company IDs
        $categories = [
            // F&B / Food UMKM Categories (Belongs to Nusantara Delight Catering)
            [
                'company_id' => $cateringId,
                'category_name' => 'Heavy Meals',
                'category_code' => 'FB-MEAL',
                'description' => 'Main courses and heavy food items.',
            ],
            [
                'company_id' => $cateringId,
                'category_name' => 'Snacks',
                'category_code' => 'FB-SNAK',
                'description' => 'Light bites, appetizers, and side dishes.',
            ],
            [
                'company_id' => $cateringId,
                'category_name' => 'Beverages',
                'category_code' => 'FB-DRNK',
                'description' => 'Soft drinks, juices, coffee, and other refreshments.',
            ],
            [
                'company_id' => $cateringId,
                'category_name' => 'Food Ingredients',
                'category_code' => 'FB-INGR',
                'description' => 'Raw materials and kitchen stocks.',
            ],

            // Phone Accessories UMKM Categories (Belongs to Gadget Zone Accessories)
            [
                'company_id' => $gadgetId,
                'category_name' => 'Phone Cases',
                'category_code' => 'ACC-CASE',
                'description' => 'Protective, silicone, and fashion phone cases.',
            ],
            [
                'company_id' => $gadgetId,
                'category_name' => 'Chargers & Cables',
                'category_code' => 'ACC-CHRG',
                'description' => 'Power adapters, USB cables, and wireless chargers.',
            ],
            [
                'company_id' => $gadgetId,
                'category_name' => 'Audio Devices',
                'category_code' => 'ACC-AUDI',
                'description' => 'Earphones, headphones, speakers, and TWS.',
            ],
            [
                'company_id' => $gadgetId,
                'category_name' => 'Screen Protectors',
                'category_code' => 'ACC-SCRN',
                'description' => 'Tempered glass and hydrogel screen protectors.',
            ],

            // Barbershop Categories (Belongs to The Classic Cut Barbershop)
            [
                'company_id' => $barberId,
                'category_name' => 'Haircut Services',
                'category_code' => 'BRB-CUT',
                'description' => 'Standard, premium, and kids haircut packages.',
            ],
            [
                'company_id' => $barberId,
                'category_name' => 'Hair Treatments',
                'category_code' => 'BRB-TRT',
                'description' => 'Hair wash, creambath, shaving, and scalp care services.',
            ],
            [
                'company_id' => $barberId,
                'category_name' => 'Hair Products',
                'category_code' => 'BRB-PROD',
                'description' => 'Retail products like pomade, hair wax, hair oil, and shampoo.',
            ],
        ];

        // 3. Bulk insert the correctly linked categories
        DB::table('categories')->insert($categories);
    }
}

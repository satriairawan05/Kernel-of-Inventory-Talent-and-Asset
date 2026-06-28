<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // 1. My Fried Chicken
        Company::updateOrCreate(
            ['company_name' => 'My Fried Chicken'],
            [
                'company_email'    => 'myfc@gmail.com',
                'company_phone'    => '089527658250',
                'company_address'  => 'Jl. Gerilya, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75242',
                'company_logo'     => null,
                'bussiness_type'   => 'Restaurant',
                'use_menu'         => true,
                'use_service'      => false,
                'use_inventory'    => true,
            ]
        );

        // 2. Raja Kepiting
        Company::updateOrCreate(
            ['company_name' => 'Raja Kepiting'],
            [
                'company_email'    => 'rajakepiting@gmail.com',
                'company_phone'    => '089527658250',
                'company_address'  => 'Jl. Gerilya, Tikungan Joang, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117',
                'company_logo'     => null,
                'bussiness_type'   => 'Restaurant',
                'use_menu'         => true,
                'use_service'      => false,
                'use_inventory'    => true,
            ]
        );

        // 3. Ayam Bebek Ganza (id 3)
        Company::updateOrCreate(
            ['company_name' => 'Ayam Bebek Ganza'],
            [
                'company_email'    => 'ayambebekganza@gmail.com',
                'company_phone'    => '089527658250', // same phone
                'company_address'  => 'Jl. Contoh No. 123, Kota Samarinda, Kalimantan Timur',
                'company_logo'     => null,
                'bussiness_type'   => 'Restaurant', // same type
                'use_menu'         => true,
                'use_service'      => false,
                'use_inventory'    => true,
            ]
        );

        $this->command->info('✅ Companies seeded successfully!');
    }
}

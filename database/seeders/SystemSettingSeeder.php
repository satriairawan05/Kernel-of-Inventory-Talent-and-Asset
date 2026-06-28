<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\SystemSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            'My Fried Chicken' => [
                'opening_balance' => '150000',
                'print_size'      => '58mm',
                'print_connection' => 'Bluetooth',
            ],
            'Raja Kepiting' => [
                'opening_balance' => '400000',
                'print_size'      => '58mm',
                'print_connection' => 'Bluetooth',
            ],
            'Ayam Bebek Ganza' => [
                'opening_balance' => '400000',
                'print_size'      => '58mm',
                'print_connection' => 'Bluetooth',
            ],
        ];

        foreach ($companies as $companyName => $settingsData) {
            $company = Company::where('company_name', $companyName)->first();

            if ($company) {
                foreach ($settingsData as $key => $value) {
                    SystemSetting::updateOrCreate(
                        [
                            'company_id' => $company->id,
                            'key'        => $key,
                        ],
                        ['value' => $value]
                    );
                }
                $this->command->info("Settings for '{$companyName}' seeded.");
            } else {
                $this->command->warn("Company '{$companyName}' not found. Please run CompanySeeder first.");
            }
        }

        $this->command->info('All settings seeded successfully!');
    }
}

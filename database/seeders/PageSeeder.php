<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'System Setting' => [
                'Outlet' => ['Create', 'Read', 'Update', 'Delete'],
                'Shift' => ['Create', 'Read', 'Update', 'Delete'],
                'Unit' => ['Create', 'Read', 'Update', 'Delete'],
                'Roles'  => ['Create', 'Read', 'Update', 'Delete'],
                'Account'   => ['Create', 'Read', 'Update', 'Delete'],
                'System Setting'   => ['Create', 'Read', 'Update', 'Delete'],
            ],
            'Personal' => [
                'Profile'  => ['Read', 'Update'],
            ],
            'Inventory' => [
                'Product' => ['Create', 'Read', 'Update', 'Delete'],
                'Category' => ['Create', 'Read', 'Update', 'Delete'],
                'Stock' => ['Create', 'Read', 'Update', 'Delete'],
                'Incoming Good' => ['Create', 'Read', 'Update', 'Delete'],
                'Exit Item' => ['Create', 'Read', 'Update', 'Delete'],
                'Return Item' => ['Create', 'Read', 'Update', 'Delete'],
                'Stock Opname' => ['Create', 'Read', 'Update', 'Delete'],
                'Report' => ['Create', 'Read', 'Update', 'Delete'],
                'Log' => ['Read'],
            ],
            'Point Of Sales' => [
                'POS'  => ['Create', 'Read', 'Update', 'Delete'],
                'Cash Summary'  => ['Create', 'Read', 'Update', 'Delete'],
                'Sale Reports (POS)'  => ['Create', 'Read', 'Update', 'Delete'],
                'Sale Reports'  => ['Create', 'Read', 'Update', 'Delete'],
            ],
            'Presence' => [
                'Presence'  => ['Create', 'Read'],
            ],
            'Human Resources' => [
                'Presence'  => ['Read'],
                'Reports'  => ['Create', 'Read', 'Update', 'Delete'],
                'SOP'  => ['Create', 'Read', 'Update', 'Delete'],
                'Payroll'  => ['Create', 'Read', 'Update', 'Delete'],
                'Carrer'  => ['Create', 'Read', 'Update', 'Delete'],
                'Interview'  => ['Create', 'Read', 'Update', 'Delete'],
                'Employee'  => ['Create', 'Read', 'Update', 'Delete'],
            ],
            'Dashboard' => [
                'Dashboard'  => ['Read'],
            ],
        ];

        foreach ($modules as $moduleName => $pages) {
            foreach ($pages as $pageName => $actions) {
                foreach ($actions as $action) {
                    \App\Models\Page::create([
                        'module'    => $moduleName,
                        'page_name' => $pageName,
                        'action'    => $action,
                    ]);
                    $this->command->info('Page : '. $pageName . ' & Action : '. $action . ' Generated!');
                }
                $this->command->info('Page : '. $pageName . ' Generated!');
            }
            $this->command->info('Module : ' . $moduleName . ' Generated!');
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dapatkan perusahaan berdasarkan nama
        $rajaKepiting = Company::where('company_name', 'Raja Kepiting')->first();
        $myFriedChicken = Company::where('company_name', 'My Fried Chicken')->first();
        $ayamBebekGanza = Company::where('company_name', 'Ayam Bebek Ganza')->first();

        // Jika perusahaan tidak ada, buat dummy (opsional)
        if (!$rajaKepiting) {
            // bisa dibuat atau skip
        }

        // Shift untuk Raja Kepiting
        if ($rajaKepiting) {
            Shift::updateOrCreate(
                [
                    'company_id' => $rajaKepiting->id,
                    'shift_code' => 'PAGI',
                ],
                [
                    'shift_name' => 'Shift Pagi',
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                    'shift_code' => 'PAGI',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
            Shift::updateOrCreate(
                [
                    'company_id' => $rajaKepiting->id,
                    'shift_code' => 'SORE',
                ],
                [
                    'shift_name' => 'Shift Sore',
                    'start_time' => '16:00:00',
                    'end_time' => '00:00:00',
                    'shift_code' => 'SORE',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
            Shift::updateOrCreate(
                [
                    'company_id' => $rajaKepiting->id,
                    'shift_code' => 'MALAM',
                ],
                [
                    'shift_name' => 'Shift Malam',
                    'start_time' => '00:00:00',
                    'end_time' => '08:00:00',
                    'shift_code' => 'MALAM',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
        }

        // Shift untuk My Fried Chicken
        if ($myFriedChicken) {
            Shift::updateOrCreate(
                [
                    'company_id' => $myFriedChicken->id,
                    'shift_code' => 'PAGI',
                ],
                [
                    'shift_name' => 'Shift Pagi',
                    'start_time' => '07:00:00',
                    'end_time' => '15:00:00',
                    'shift_code' => 'PAGI',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
            Shift::updateOrCreate(
                [
                    'company_id' => $myFriedChicken->id,
                    'shift_code' => 'SORE',
                ],
                [
                    'shift_name' => 'Shift Sore',
                    'start_time' => '15:00:00',
                    'end_time' => '23:00:00',
                    'shift_code' => 'SORE',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
        }

        // Shift untuk Ayam Bebek Ganza
        if ($ayamBebekGanza) {
            Shift::updateOrCreate(
                [
                    'company_id' => $ayamBebekGanza->id,
                    'shift_code' => 'PAGI',
                ],
                [
                    'shift_name' => 'Shift Pagi',
                    'start_time' => '08:00:00',
                    'end_time' => '16:00:00',
                    'shift_code' => 'PAGI',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
            Shift::updateOrCreate(
                [
                    'company_id' => $ayamBebekGanza->id,
                    'shift_code' => 'SORE',
                ],
                [
                    'shift_name' => 'Shift Sore',
                    'start_time' => '16:00:00',
                    'end_time' => '00:00:00',
                    'shift_code' => 'SORE',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
            Shift::updateOrCreate(
                [
                    'company_id' => $ayamBebekGanza->id,
                    'shift_code' => 'MALAM',
                ],
                [
                    'shift_name' => 'Shift Malam',
                    'start_time' => '00:00:00',
                    'end_time' => '08:00:00',
                    'shift_code' => 'MALAM',
                    'late_tolerance_minutes' => 15,
                    'early_leave_tolerance_minutes' => 30,
                ]
            );
        }

        $this->command->info('Shifts seeded successfully!');
    }
}

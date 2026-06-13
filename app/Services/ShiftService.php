<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    /**
     * Create a new shift for a company.
     *
     * @param array $data
     * @return Shift
     */
    public function store(array $data): Shift
    {
        return DB::transaction(function () use ($data) {
            $company = Company::findOrFail($data['company_id']);

            return Shift::create([
                'company_id' => $company->id,
                'shift_name' => $data['shift_name'],
                'shift_code' => $data['shift_code'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'late_tolerance_minutes' => $data['late_tolerance_minutes'],
                'early_leave_tolerance_minutes' => $data['early_leave_tolerance_minutes'],
            ]);
        });
    }

    /**
     * Update an existing shift.
     *
     * @param Shift $shift
     * @param array $data
     * @return Shift
     */
    public function update(Shift $shift, array $data): Shift
    {
        return DB::transaction(function () use ($shift, $data) {
            $shift->update([
                'company_id' => $data['company_id'],
                'shift_name' => $data['shift_name'],
                'shift_code' => $data['shift_code'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'late_tolerance_minutes' => $data['late_tolerance_minutes'],
                'early_leave_tolerance_minutes' => $data['early_leave_tolerance_minutes'],
            ]);

            return $shift->fresh();
        });
    }

    /**
     * Delete a shift.
     *
     * @param Shift $shift
     * @return bool
     */
    public function destroy(Shift $shift): bool
    {
        return DB::transaction(function () use ($shift) {
            return $shift->delete();
        });
    }
}
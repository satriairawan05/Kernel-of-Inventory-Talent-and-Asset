<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class UnitService
{
    /**
     * Create a new unit record.
     *
     * Stores unit data inside a database transaction.
     */
    public function store(array $data): Unit
    {
        return DB::transaction(function () use ($data) {
            // Create unit record
            return Unit::create([
                'unit_name'   => $data['unit_name'],
                'unit_code'   => $data['unit_code'],
                'description' => $data['description'] ?? null,
                'is_active'   => $data['is_active'] ?? true,
            ]);

        });
    }

    /**
     * Update an existing unit record.
     *
     * Updates unit information inside a transaction.
     */
    public function update(Unit $unit,array $data): Unit
    {
        return DB::transaction(function () use ($unit, $data) {
            // Update unit data
            $unit->update([
                'unit_name'   => $data['unit_name'],
                'unit_code'   => $data['unit_code'],
                'description' => $data['description'] ?? null,
                'is_active'   => $data['is_active'] ?? false,
            ]);

            // Return fresh model instance
            return $unit->fresh();
        });
    }

    /**
     * Delete a unit record.
     *
     * Removes the unit from database.
     */
    public function destroy(Unit $unit): bool 
    {
        return DB::transaction(function () use ($unit) {
            // Delete unit record
            return $unit->delete();
        });
    }
}
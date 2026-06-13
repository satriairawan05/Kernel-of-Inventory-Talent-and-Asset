<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    /**
     * Create a new user account.
     *
     * @param array $data
     * @return User
     */
    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Hash password before storing
            $data['password'] = Hash::make($data['password']);

            return User::create($data);
        });
    }

    /**
     * Update an existing user account.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            // Update password only when provided
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return $user->fresh();
        });
    }

    /**
     * Delete a user account.
     *
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return $user->delete();
        });
    }
}
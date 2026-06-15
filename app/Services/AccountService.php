<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Hash, Storage};

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

    /**
     * Update user profile (name, email, avatar)
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $avatarFile = null): User
    {
        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($avatarFile) {
            // Hapus avatar lama jika ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan avatar baru
            $path = $avatarFile->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return $user;
    }

    /**
     * Update user password
     */
    public function updatePassword(User $user, string $newPassword): User
    {
        $user->password = Hash::make($newPassword);
        $user->save();

        return $user;
    }

    /**
     * Update user's group (role).
     *
     * @param \App\Models\User $user
     * @param int $groupId
     * @return \App\Models\User
     */
    public function updateGroup(User $user, int $groupId): User
    {
        return DB::transaction(function () use ($user, $groupId) {
            $user->group_id = $groupId;
            $user->save();

            return $user;
        });
    }
}

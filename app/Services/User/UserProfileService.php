<?php

namespace App\Services\User;

use App\Models\Chef;
use App\Models\User;
use App\Services\Interfaces\User\UserProfileServiceInterface;
use Illuminate\Validation\ValidationException;

class UserProfileService implements UserProfileServiceInterface
{

    public function updateInfo(int $userId, string $firstName, string $lastName): void
    {
        User::query()->where('id', $userId)->update(['first_name' => $firstName, 'last_name' => $lastName]);
    }

    public function updateEmail(int $userId, string $email, bool $commercialAgreement): void
    {
        User::query()->where('id', $userId)->update(
            [
                'email' => $email,
                'email_verified_at' => now(),
                'commercial_agreement' => $commercialAgreement
            ]
        );
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): void
    {
        $user->refresh();
        if ($currentPassword === $newPassword) {
            throw ValidationException::withMessages([
                'password' => __('validation.attributes.different_password'),
            ]);
        }

        if (!$user->passwordCheck($currentPassword)) {
            throw ValidationException::withMessages([
                'password' => __('validation.attributes.current_password_not_match'),
            ]);
        }

        $user->password = User::generatePassword($newPassword);
        $user->save();
    }
}
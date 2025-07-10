<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Interfaces\User\UserProfileServiceInterface;

class UserProfileService implements UserProfileServiceInterface
{

    public function updateInfo(int $userId, string $firstName, string $lastName): void
    {
        User::query()->where('id', $userId)->update(['first_name' => $firstName, 'last_name' => $lastName]);
    }

    public function updateEmail(int $userId, string $email): void
    {
        User::query()->where('id', $userId)->update(['email' => $email, 'email_verified_at' => now()]);
    }
}
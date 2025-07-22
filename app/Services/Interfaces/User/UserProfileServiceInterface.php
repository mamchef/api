<?php

namespace App\Services\Interfaces\User;

use App\Models\User;

interface UserProfileServiceInterface
{


    public function updateInfo(int $userId,string $firstName , string $lastName):void;

    public function updateEmail(int $userId ,string $email):void;

    public function changePassword(User $user, string $currentPassword, string $newPassword): void;

}
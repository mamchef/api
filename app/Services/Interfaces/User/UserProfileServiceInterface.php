<?php

namespace App\Services\Interfaces\User;

interface UserProfileServiceInterface
{


    public function updateInfo(int $userId,string $firstName , string $lastName):void;

    public function updateEmail(int $userId ,string $email):void;
}
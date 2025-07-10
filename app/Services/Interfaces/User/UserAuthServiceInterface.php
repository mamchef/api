<?php

namespace App\Services\Interfaces\User;

use App\DTOs\User\Auth\RegisterDTO;

interface UserAuthServiceInterface
{
    public function loginOrRegister(string $countryCode, string $phoneNumber): string;

    public function register(RegisterDTO $DTO): string;

    public function login(string $countryCode, string $phoneNumber, string $password): string;

    public function sendOtp(string $key, string $phoneNumber);

    public function sendEmailOtp(string $key, string $email): mixed;


}

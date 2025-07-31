<?php

namespace App\Services\Interfaces\Admin;

use App\DTOs\Admin\Auth\LoginByEmailDTO;

interface AdminAuthServiceInterface
{

    public function sendOtp(string $key,string $email);

    public function loginByEmail(LoginByEmailDTO $DTO): string;

}
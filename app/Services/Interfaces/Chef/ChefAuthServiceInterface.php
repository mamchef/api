<?php

namespace App\Services\Interfaces\Chef;

use App\DTOs\Chef\Auth\ForgotPasswordDTO;
use App\DTOs\Chef\Auth\LoginByEmailDTO;
use App\DTOs\Chef\Auth\LoginByGoogleDTO;
use App\DTOs\Chef\Auth\LoginByAppleDTO;
use App\DTOs\Chef\Auth\RegisterByEmailDTO;
use App\DTOs\Chef\Auth\LoginByFacebookDTO;
use App\Models\Chef;

interface ChefAuthServiceInterface
{
    public function registerByEmail(RegisterByEmailDTO $DTO): string;

    public function loginByFacebook(LoginByFacebookDTO $DTO): string;

    public function sendOtp(string $key,string $email);

    public function loginByGoogle(LoginByGoogleDTO $DTO): string;

    public function loginByApple(LoginByAppleDTO $DTO): string;

    public function loginByEmail(LoginByEmailDTO $DTO): string;

    public function forgotPassword(ForgotPasswordDTO $DTO): void;
}
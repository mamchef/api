<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Auth\LoginByEmailDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Auth\LoginByEmailRequest;
use App\Http\Requests\Api\V1\Admin\Auth\SendLoginEmailOTPlRequest;
use App\Http\Resources\V1\Admin\AdminTokenResponse;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\Admin\AdminAuthServiceInterface;
use App\Services\RateLimitService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public static string $LOGIN_PREFIX_KEY = "login-";

    public function __construct(private readonly AdminAuthServiceInterface $authService)
    {
    }

    /**
     * @throws ValidationException
     */
    public function loginSendOtp(SendLoginEmailOTPlRequest $request): SuccessResponse
    {
        RateLimitService::enforce(
            'login-send-otp:' . $request->email,
            1,
            120,
            'Too many OTP requests. Please try again later.'
        );

        $this->authService->sendOtp(
            key: self::$LOGIN_PREFIX_KEY . $request->email,
            email: $request->email
        );

        return new SuccessResponse(
            message: __('otp.sent_otp')
        );
    }

    public function loginByEmail(LoginByEmailRequest $request): AdminTokenResponse
    {
        $token = $this->authService->loginByEmail(
            new LoginByEmailDTO(
                email: $request->email,
                password: $request->password,
            )
        );

        RateLimitService::reset('login-send-otp:' . $request->email);

        return new AdminTokenResponse(
            token: $token
        );
    }


    public function me(Request $request): AdminTokenResponse
    {
        $token = $request->bearerToken();
        return new AdminTokenResponse(
            token: $token
        );
    }

    public function logout(Request $request): SuccessResponse
    {
        $token = $request->user()->currentAccessToken();
        $token->expires_at = now();
        $token->save();
        return new SuccessResponse();
    }

}
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\User\Auth\LoginOrRegisterRequest;
use App\Http\Requests\Api\V1\User\Auth\LoginRequest;
use App\Http\Requests\Api\V1\User\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\User\Auth\ResetPasswordRequest;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\UserTokenResponse;
use App\Services\Interfaces\User\UserAuthServiceInterface;
use App\Services\RateLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public static string $REGISTER_PREFIX_KEY = "register-";
    public static string $LOGIN_PREFIX_KEY = "login-";
    public static string $FORGOT_PREFIX_KEY = "forgot-";

    public function __construct(private readonly UserAuthServiceInterface $authService)
    {
    }

    /**
     * @throws ValidationException
     */
    public function loginOrRegister(LoginOrRegisterRequest $request): SuccessResponse
    {
        $result = $this->authService->loginOrRegister(
            countryCode: $request->country_code,
            phoneNumber: $request->phone_number,
        );

        return new SuccessResponse(
            message: $result
        );
    }

    public function register(RegisterRequest $request): UserTokenResponse
    {
        $token = $this->authService->register(
            DTO: new RegisterDTO(
                countryCode: $request->country_code,
                phoneNumber: $request->phone_number,
                password: $request->password,
            )
        );

        RateLimitService::reset('register-send-otp:' . $request->phone_number);

        return new UserTokenResponse(
            token: $token
        );
    }


    public function login(LoginRequest $request): UserTokenResponse
    {
        RateLimitService::enforce(
            self::$REGISTER_PREFIX_KEY . $request->country_code . $request->phone_number,
            5,
            120,
            'Too many Attempts. Please try again later.'
        );

        $token = $this->authService->login(
            countryCode: $request->country_code,
            phoneNumber: $request->phone_number,
            password: $request->password,
        );

        RateLimitService::reset(self::$REGISTER_PREFIX_KEY . $request->country_code . $request->phone_number,);
        return new UserTokenResponse(
            token: $token
        );
    }


    public function logout(Request $request): SuccessResponse
    {
        $token = $request->user()->currentAccessToken();
        $token->expires_at = now();
        $token->save();

        Cache::forget('token:' . $request->bearerToken());
        return new SuccessResponse();
    }


    public function me(Request $request): UserTokenResponse
    {
        $token = $request->bearerToken();
        return new UserTokenResponse(
            token: $token
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): SuccessResponse
    {
        $this->authService->sendOtp(
            key: AuthController::$REGISTER_PREFIX_KEY,
            phoneNumber: $request->country_code . $request->phone_number,
        );
        return new SuccessResponse();
    }

    public function resetPassword(ResetPasswordRequest $request): SuccessResponse
    {
        $this->authService->resetPassword(
            countryCode: $request->country_code,
            phoneNumber: $request->phone_number,
            password: $request->password,
        );
        return new SuccessResponse();
    }

}

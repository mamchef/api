<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\Auth\ForgotPasswordDTO;
use App\DTOs\Chef\Auth\LoginByEmailDTO;
use App\DTOs\Chef\Auth\LoginByGoogleDTO;
use App\DTOs\Chef\Auth\LoginByAppleDTO;
use App\DTOs\Chef\Auth\RegisterByEmailDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Chef\Auth\LoginByEmailRequest;
use App\Http\Requests\Api\V1\Chef\Auth\RegisterByEmailRequest;
use App\Http\Requests\Api\V1\Chef\Auth\RegisterByGoogleRequest;
use App\Http\Requests\Api\V1\Chef\Auth\RegisterByAppleRequest;
use App\Http\Requests\Api\V1\Chef\Auth\SendForgotPasswordEmailOTPlRequest;
use App\Http\Requests\Api\V1\Chef\Auth\SendLoginEmailOTPlRequest;
use App\Http\Requests\Api\V1\Chef\Auth\SendRegisterEmailOTPlRequest;
use App\Http\Resources\V1\Admin\AdminTokenResponse;
use App\Http\Resources\V1\Chef\ChefTokenResponse;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\Chef\ChefAuthServiceInterface;
use App\Services\RateLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public static string $REGISTER_PREFIX_KEY = "register-";
    public static string $LOGIN_PREFIX_KEY = "login-";
    public static string $FORGOT_PASSWORD_PREFIX_KEY = "forgot-password-";


    public function __construct(private readonly ChefAuthServiceInterface $authService)
    {
    }

    /**
     * @throws ValidationException
     */
    public function registerSendOtp(SendRegisterEmailOTPlRequest $request): SuccessResponse
    {
        RateLimitService::enforce(
            'register-send-otp:' . $request->email,
            1,
            120,
            'Too many OTP requests. Please try again later.'
        );

        $this->authService->sendOtp(
            key: self::$REGISTER_PREFIX_KEY . $request->email,
            email: $request->email
        );
        return new SuccessResponse(
            message: __('otp.sent_otp')
        );
    }

    public function registerByEmail(RegisterByEmailRequest $request): ChefTokenResponse
    {
        $token = $this->authService->registerByEmail(
            DTO: new RegisterByEmailDTO(
                email: $request->email,
                password: $request->password,
                fcmToken: $request->fcm_token ?? null
            )
        );

        RateLimitService::reset('register-send-otp:' . $request->email);

        return new ChefTokenResponse(
            token: $token
        );
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

    public function loginByEmail(LoginByEmailRequest $request): ChefTokenResponse
    {
        $token = $this->authService->loginByEmail(
            new LoginByEmailDTO(
                email: $request->email,
                password: $request->password,
                fcmToken: $request->fcm_token ?? null
            )
        );

        RateLimitService::reset('login-send-otp:' . $request->email);
        return new ChefTokenResponse(
            token: $token
        );
    }

    public function loginByGoogle(RegisterByGoogleRequest $request): ChefTokenResponse
    {
        $token = $this->authService->loginByGoogle(
            DTO: new LoginByGoogleDTO(
                token: $request->token,
                fcmToken: $request->fcm_token ?? null
            )
        );

        return new ChefTokenResponse(
            token: $token
        );
    }

    public function loginByApple(RegisterByAppleRequest $request): ChefTokenResponse
    {
        $token = $this->authService->loginByApple(
            DTO: new LoginByAppleDTO(
                identityToken: $request->identity_token,
                email: $request->email ?? null,
                fullName: $request->full_name ?? null,
                user: $request->user ?? null,
                fcmToken: $request->fcm_token ?? null
            )
        );

        return new ChefTokenResponse(
            token: $token
        );
    }


    public function logout(Request $request): SuccessResponse
    {
        $token = $request->user()->currentAccessToken();
        $token->expires_at = now();
        $token->save();

        Cache::forget('chef-token:' . $request->bearerToken());
        return new SuccessResponse();
    }


    public function forgotPasswordSendOtp(SendForgotPasswordEmailOTPlRequest $request): SuccessResponse
    {
        RateLimitService::enforce(
            'forgot-password-send-otp:' . $request->email,
            1,
            120,
            'Too many OTP requests. Please try again later.'
        );

        $this->authService->sendOtp(
            key: self::$FORGOT_PASSWORD_PREFIX_KEY . $request->email,
            email: $request->email
        );
        return new SuccessResponse(
            message: __('otp.sent_otp')
        );
    }


    public function forgotPassword(ForgotPasswordRequest $request): SuccessResponse
    {
        $this->authService->forgotPassword(
            new ForgotPasswordDTO(
                email: $request->email,
                password: $request->password
            )
        );

        return new SuccessResponse(
            message: __('auth.password_reset.success')
        );
    }


    public function me(Request $request): ChefTokenResponse
    {
        $token = $request->bearerToken();
        return new ChefTokenResponse(
            token: $token
        );
    }

}
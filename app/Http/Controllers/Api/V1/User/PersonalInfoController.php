<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Profile\SendEmailOTPlRequest;
use App\Http\Requests\Api\V1\User\Profile\SetEmailRequest;
use App\Http\Requests\Api\V1\User\Profile\UserProfileUpdateInfoRequest;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\User\UserAuthServiceInterface;
use App\Services\Interfaces\User\UserProfileServiceInterface;
use App\Services\RateLimitService;
use Illuminate\Support\Facades\Auth;

class PersonalInfoController extends Controller
{
    public static string $SET_EMAIL_PREFIX_KEY = "set-email-";

    public function __construct(
        protected UserProfileServiceInterface $profileService,
        protected UserAuthServiceInterface $authService
    ) {
    }

    public function updateInfo(UserProfileUpdateInfoRequest $request): SuccessResponse
    {
        $this->profileService->updateInfo(
            userId: Auth::id(),
            firstName: $request->first_name,
            lastName: $request->last_name,
        );
        return new SuccessResponse();
    }


    public function setEmailOTP(SendEmailOTPlRequest $request): SuccessResponse
    {
        RateLimitService::enforce(
            'set-email-send-otp:' . $request->email,
            1,
            60,
            'Too many   requests. Please try again later.'
        );

        $this->authService->sendEmailOtp(
            key: self::$SET_EMAIL_PREFIX_KEY . $request->email,
            email: $request->email
        );
        return new SuccessResponse(
            message: __('otp.sent_otp')
        );
    }

    public function setEmail(SetEmailRequest $request): SuccessResponse
    {
        $this->profileService->updateEmail(
            userId: Auth::id(),
            email: $request->email,
        );
        return new SuccessResponse();
    }
}
<?php

namespace App\Http\Requests\Api\V1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserFirebaseController extends Controller
{


    /**
     * Store or update FCM token for the authenticated chef
     */
    public function storeFCMToken(Request $request): JsonResponse|SuccessResponse
    {
        try {
            $request->validate([
                'fcm_token' => 'required|string'
            ]);

            /** @var User $user */
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Deactivate all existing FCM tokens for this chef
            $user->fcmTokens()->update(['is_active' => false]);

            // Create or update the new FCM token
            $user->fcmTokens()->updateOrCreate(
                ['token' => $request->fcm_token],
                [
                    'is_active' => true,
                    'device_type' => $this->getDeviceType($request),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

          return new SuccessResponse();

        } catch (\Exception $e) {
            \Log::error('FCM Token Storage Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to store FCM token'
            ], 500);
        }
    }

    /**
     * Get device type from request headers
     */
    private function getDeviceType(Request $request): string
    {
        $userAgent = $request->header('User-Agent', '');

        if (str_contains($userAgent, 'Mobile')) {
            return 'mobile';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            return 'ios';
        } else {
            return 'web';
        }
    }
}
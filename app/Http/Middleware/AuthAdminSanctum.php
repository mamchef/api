<?php

namespace App\Http\Middleware;

use App\DTOs\Admin\Auth\AdminAuthDTO;
use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthAdminSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        /** @var AdminAuthDTO $dto */
        $dto = Cache::rememberForever('admin-token:' . $token, function () use ($token) {
            try {
                $accessToken = PersonalAccessToken::findToken($token);
                if (!$accessToken || ($accessToken->expires_at != null && $accessToken->expires_at < now(
                        )) || !$accessToken->tokenable instanceof Admin) {
                    return new AdminAuthDTO(
                        message: "Unauthorized",
                        status: Response::HTTP_UNAUTHORIZED,
                    );
                }

                $user = $accessToken->tokenable;

                return new AdminAuthDTO(
                    user: $user,
                    accessToken: $accessToken,
                    status: Response::HTTP_OK,
                );
            } catch (\Throwable) {
                return new AdminAuthDTO(
                    message: "Unauthorized",
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }
        });

        if ($dto->getStatus() == Response::HTTP_INTERNAL_SERVER_ERROR) {
            Cache::forget('admin-token:' . $token);
        }

        /** @var PersonalAccessToken $accessToken */
        $accessToken = $dto->getAccessToken();

        $user = $dto->getuser();

        if ($dto->getStatus() !== Response::HTTP_OK || ($accessToken?->expires_at != null and $accessToken?->expires_at < now()) || !$user instanceof Admin) {

           return response()->json(['message' => $dto->getMessage() ?? "Unauthorized"], 401);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        Auth::setUser($user);

        $user->withAccessToken($accessToken);

        return $next($request);
    }
}

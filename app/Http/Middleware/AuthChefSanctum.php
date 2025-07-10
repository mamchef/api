<?php

namespace App\Http\Middleware;

use App\DTOs\Chef\ChefAuthDTO;
use App\Models\Chef;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthChefSanctum
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

        /** @var ChefAuthDTO $dto */
        $dto = Cache::rememberForever('token:' . $token, function () use ($token) {
            try {
                $accessToken = PersonalAccessToken::findToken($token);
                if (!$accessToken || ($accessToken->expires_at != null && $accessToken->expires_at < now(
                        )) || !$accessToken->tokenable instanceof Chef) {
                    return new ChefAuthDTO(
                        message: "Unauthorized",
                        status: Response::HTTP_UNAUTHORIZED,
                    );
                }

                $chef = $accessToken->tokenable;

                return new ChefAuthDTO(
                    chef: $chef,
                    accessToken: $accessToken,
                    status: Response::HTTP_OK,
                );
            } catch (\Throwable) {
                return new ChefAuthDTO(
                    message: "Unauthorized",
                    status: Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }
        });

        if ($dto->getStatus() == Response::HTTP_INTERNAL_SERVER_ERROR) {
            Cache::forget('token:' . $token);
        }

        /** @var PersonalAccessToken $accessToken */
        $accessToken = $dto->getAccessToken();

        $chef = $dto->getChef();


        if ($dto->getStatus() !== Response::HTTP_OK || ($accessToken?->expires_at != null and $accessToken?->expires_at < now()) || !$chef instanceof Chef) {

           return response()->json(['message' => $dto->getMessage() ?? "Unauthorized"], 401);
        }

        $request->setUserResolver(function () use ($chef) {
            return $chef;
        });

        Auth::setUser($chef);

        $chef->withAccessToken($accessToken);

        return $next($request);
    }
}

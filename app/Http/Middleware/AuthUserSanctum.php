<?php

namespace App\Http\Middleware;

use App\DTOs\Chef\ChefAuthDTO;
use App\DTOs\User\UserAuthDTO;
use App\Models\Chef;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthUserSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $lang = request()->header('Language') ?? 'en';

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        /** @var UserAuthDTO $dto */
        $dto = Cache::rememberForever('token:' . $token, function () use ($token) {
            try {
                $accessToken = PersonalAccessToken::findToken($token);
                if (!$accessToken || ($accessToken->expires_at != null && $accessToken->expires_at < now()) || !$accessToken->tokenable instanceof User) {
                    return new UserAuthDTO(
                        message: "Unauthorized",
                        status: Response::HTTP_UNAUTHORIZED,
                    );
                }

                $user = $accessToken->tokenable;

                return new UserAuthDTO(
                    user: $user,
                    accessToken: $accessToken,
                    status: Response::HTTP_OK,
                );
            } catch (\Throwable) {
                return new UserAuthDTO(
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

        $user = $dto->getuser();


        if ($dto->getStatus() !== Response::HTTP_OK || ($accessToken?->expires_at != null and $accessToken?->expires_at < now()) || !$user instanceof User) {
            return response()->json(['message' => $dto->getMessage() ?? "Unauthorized"], 401);
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });


        //Update cached chef when user change lang
        if ($user->lang != $lang) {
            User::query()->where('id', $user->id)->update(['lang' => $lang]);
            $user->lang = $lang;
            $accessToken = $dto->getAccessToken();
            Cache::forget('token:' . $token);
            Cache::rememberForever('token:' . $token, function () use ($token, $user, $accessToken) {
                try {
                    return new UserAuthDTO(
                        user: $user,
                        accessToken: $accessToken,
                        status: Response::HTTP_OK,
                    );
                } catch (\Throwable) {
                    return new UserAuthDTO(
                        message: "Unauthorized",
                        status: Response::HTTP_INTERNAL_SERVER_ERROR,
                    );
                }
            });
        }

        Auth::setUser($user);

        $user->withAccessToken($accessToken);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Http\Resources\V1\ExceptionResource;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NormalizeResponseMiddleware
{

    public function handle(Request $request, Closure $next): ResponseAlias
    {
        $response = $next($request);
        return $response;
        // Skip normalization for broadcasting auth
        if ($request->is('api/broadcasting/auth')) {
            return $next($request);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            if (!isset($data['code']) || !isset($data['message']) || !isset($data['result'])) {
                $code = $response->getStatusCode();
                if ($code !== 200) {
                    $response = response(
                        new ExceptionResource(
                            errors: $data["errors"] ?? [],
                            message: $data["message"] ?? null,
                            code: $code,
                        )
                    );
                }
            }

            if ($response->status() == ResponseAlias::HTTP_NO_CONTENT) {
                return $response;
            }

            $data = json_decode($response->getContent(), true);
            $statusCode = Arr::get($data, 'data.code', Arr::get($data, 'code', 500));
            $statusCode = $statusCode < 100 || $statusCode >= 600 ? 500 : $statusCode;
            $response->setStatusCode($statusCode);
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}

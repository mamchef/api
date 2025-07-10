<?php

namespace App\Http\Controllers;

use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\Chef\ChefProfileServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocuSignController extends Controller
{

    public function __construct(protected ChefProfileServiceInterface $chefProfileService)
    {
    }

    public function handle(Request $request): SuccessResponse
    {
        $payload = json_decode($request->getContent(), true);
        Log::info($payload);
        $envelopeId = $payload['data']['envelopeId'];
        $this->chefProfileService->fetchChefSignedContract($envelopeId);
        return new SuccessResponse();
    }
}

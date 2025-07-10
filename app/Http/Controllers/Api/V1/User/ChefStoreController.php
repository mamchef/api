<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\User\ChefStore\ChefStoreResource;
use App\Services\Interfaces\ChefStoreServiceInterface;

class ChefStoreController extends Controller
{

    public function __construct(protected ChefStoreServiceInterface $chefStoreService)
    {
    }

    public function show(string $slug): ChefStoreResource
    {
        $chefStore = $this->chefStoreService->getBySlug(
            slug: $slug,
            relations: ["chef:id,first_name,last_name"]
        );
        return ChefStoreResource::make($chefStore);
    }
}
<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\User\Tag\TagResource;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TagController extends Controller
{

    public function __construct(protected TagServiceInterface $tagService)
    {
    }

    public function homeTags(): ResourceCollection
    {
        $tags = $this->tagService->all(
            filters: [
                'active' => true,
                'homepage' => true,
            ],
            pagination: 15
        );

        return TagResource::collection($tags);
    }


    public function tags(): ResourceCollection
    {
        $tags = $this->tagService->all(
            filters: [
                'active' => true,
            ]
        );

        return TagResource::collection($tags);
    }
}
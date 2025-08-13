<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Tag\TagStoreDTO;
use App\DTOs\Admin\Tag\TagUpdateDTO;
use App\DTOs\DoNotChange;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Tag\TagStoreRequest;
use App\Http\Requests\Api\V1\Admin\Tag\TagUpdateRequest;
use App\Http\Resources\V1\Admin\Tag\TagResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TagController extends Controller
{

    public function __construct(protected TagServiceInterface $tagService)
    {
    }


    public function index(Request $request): ResourceCollection
    {
        $tags = $this->tagService->all(
            filters: $request->all(),
            relations: ['parent'],
            pagination: self::validPagination()
        );
        return TagResource::collection($tags);
    }


    public function show(int $tagId)
    {
        $tag = $this->tagService->show(
            tagId: $tagId,
            relations: ['parent']
        );
        return new TagResource($tag);
    }


    public function store(TagStoreRequest $request): TagResource
    {
        $dto = new TagStoreDTO(
            name: $request->name,
            priority: $request->priority,
            description: $request->description,
            icon_type: $request->icon_type,
            icon: $request->icon,
            tag_id: $request->tag_id,
            status: $request->status,
            homepage: $request->homepage
        );
        $tag = $this->tagService->store($dto);
        return new TagResource($tag);
    }


    public function update(TagUpdateRequest $request, int $tagId): TagResource
    {
        $dto = new TagUpdateDTO(
            name: $request->has('name') ? $request->name : DoNotChange::value(),
            priority: $request->has('priority') ? $request->priority : DoNotChange::value(),
            description: $request->has('description') ? $request->description : DoNotChange::value(),
            icon_type: $request->has("icon_type") ? $request->icon_type : DoNotChange::value(),
            icon: $request->has("icon") ? $request->icon : DoNotChange::value(),
            tag_id: $request->has("tag_id") ? $request->icon : DoNotChange::value(),
            status: $request->has("status") ? $request->status : DoNotChange::value(),
            homepage: $request->has("homepage") ? $request->homepage : DoNotChange::value()
        );

        $tag = $this->tagService->update(
            tagId: $tagId,
            dto: $dto
        );

        return new TagResource($tag);
    }


    public function destroy(int $tagId): SuccessResponse
    {
        $this->tagService->destroy($tagId);
        return new SuccessResponse();
    }
}
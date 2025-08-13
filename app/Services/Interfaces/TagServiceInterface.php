<?php

namespace App\Services\Interfaces;

use App\DTOs\Admin\Tag\TagStoreDTO;
use App\DTOs\Admin\Tag\TagUpdateDTO;
use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TagServiceInterface
{
    /**
     * @param array $filters
     * @param array $relations
     * @param int|null $pagination
     * @return Collection|LengthAwarePaginator|array
     */
    public function all(
        array $filters,
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;


    /**
     * @param int $tagId
     * @param array $relations
     * @return Tag
     */
    public function show(int $tagId, array $relations = []) : Tag;

    /**
     * @param TagStoreDTO $dto
     * @return Tag
     */
    public function store(TagStoreDTO $dto): Tag;

    /**
     * @param int $tagId
     * @param TagUpdateDTO $dto
     * @return Tag
     */
    public function update(int $tagId, TagUpdateDTO $dto): Tag;

    /**
     * @param int $tagId
     * @return void
     */
    public function destroy(int $tagId):void;
}
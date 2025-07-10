<?php

namespace App\Services;

use App\Models\Tag;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TagService implements TagServiceInterface
{
    public function all(
        array $filters,
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $tags = Tag::query()->filter($filters)->with($relations);
        return $pagination ? $tags->paginate($pagination) : $tags->get();
    }
}
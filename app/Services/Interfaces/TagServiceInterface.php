<?php

namespace App\Services\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TagServiceInterface
{
    public function all(
        array $filters,
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array;
}
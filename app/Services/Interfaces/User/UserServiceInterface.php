<?php

namespace App\Services\Interfaces\User;

use App\DTOs\Admin\User\UserUpdateByAdminDTO;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{

    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator;


    public function show(int $userId, array $relations = []): User;


    public function update(int $userId ,UserUpdateByAdminDTO $DTO): User;

}
<?php

namespace App\Services\Interfaces;

use App\Models\UserTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserTransactionServiceInterface
{
    /**
     * @param array|null $filters
     * @param array $relations
     * @param $pagination
     * @return Collection|LengthAwarePaginator
     */
    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator;


    /**
     * @param int $transactionId
     * @param array $relations
     * @return UserTransaction
     */
    public function show(int $transactionId , array $relations = []):UserTransaction;
}
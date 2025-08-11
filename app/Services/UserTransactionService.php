<?php

namespace App\Services;

use App\Models\UserTransaction;
use App\Services\Interfaces\UserTransactionServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserTransactionService implements UserTransactionServiceInterface
{

    /** @inheritDoc */
    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator {
        $transactions = UserTransaction::query()->when($relations, fn($q) => $q->with($relations))
            ->when($filters, fn($q) => $q->filter($filters));

        return $pagination ? $transactions->paginate($pagination) : $transactions->get();
    }

    /** @inheritDoc */
    public function show(int $transactionId, array $relations = []): UserTransaction
    {
        return  UserTransaction::query()->with($relations)->findOrFail($transactionId);
    }
}
<?php

namespace App\Services\User;

use App\DTOs\Admin\User\UserUpdateByAdminDTO;
use App\Models\User;
use App\Services\Interfaces\User\UserServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserService implements UserServiceInterface
{

    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator {
        $users = User::query()->when($relations, fn($q) => $q->with($relations))
            ->when($filters, fn($q) => $q->filter($filters));

        return $pagination ? $users->paginate($pagination) : $users->get();
    }

    public function show(int $userId, array $relations = []): User
    {
        return User::query()->with($relations)->findOrFail($userId);
    }

    public function update(int $userId, UserUpdateByAdminDTO $DTO): User
    {
        $user = $this->show($userId);
        $data = $DTO->toArray();

        if (isset($data['password'])) {
            $data['password'] = User::generatePassword($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }
}
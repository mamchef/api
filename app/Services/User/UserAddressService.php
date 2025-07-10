<?php

namespace App\Services\User;

use App\Models\UserAddress;
use App\Services\Interfaces\User\UserAddressServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class UserAddressService implements UserAddressServiceInterface
{

    /** @inheritDoc */
    public function index($userId): Collection|array
    {
        return UserAddress::query()
            ->with(['city'])
            ->where('user_id', $userId)->get();
    }

    /** @inheritDoc */
    public function store(array $params, int $userId): UserAddress
    {
        return UserAddress::query()->create(array_merge($params, ['user_id' => $userId]));
    }

    /** @inheritDoc */
    public function show(int $addressId, int $userId): UserAddress
    {
        return UserAddress::query()->where("user_id", $userId)
            ->where("id", $addressId)->with(['city'])->firstOrFail();
    }

    /** @inheritDoc */
    public function update(array $params, int $addressId, int $userId): UserAddress
    {
        $userAddress = $this->show($addressId, $userId);
        $userAddress->update($params);
        return $userAddress->fresh();
    }

    /** @inheritDoc */
    public function destroy(int $addressId, int $userId): void
    {
        $userAddress = $this->show($addressId, $userId);
        $userAddress->delete();
    }
}
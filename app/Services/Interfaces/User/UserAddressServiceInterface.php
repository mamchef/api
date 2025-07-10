<?php

namespace App\Services\Interfaces\User;

use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Collection;

interface UserAddressServiceInterface
{

    /**
     * @param $userId
     * @return Collection|array
     */
    public function index($userId): Collection|array;

    /**
     * @param array $params
     * @param int $userId
     * @return UserAddress
     */
    public function store(array $params, int $userId): UserAddress;

    /**
     * @param int $addressId
     * @param int $userId
     * @return UserAddress
     */
    public function show(int $addressId, int $userId): UserAddress;

    /**
     * @param array $params
     * @param int $addressId
     * @param int $userId
     * @return UserAddress
     */
    public function update(array $params, int $addressId, int $userId): UserAddress;

    /**
     * @param int $addressId
     * @param int $userId
     * @return void
     */
    public function destroy(int $addressId, int $userId): void;
}
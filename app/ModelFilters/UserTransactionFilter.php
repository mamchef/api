<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class UserTransactionFilter extends ModelFilter
{
    protected $drop_id = false;

    public function __construct($query, array $input = [], $relationsEnabled = true)
    {
        parent::__construct($query, $input, $relationsEnabled);
        if (!request()->input('sort_by')) {
            $this->orderBy('id', 'desc');
        }
    }

    public $relations = [];

    public function search(string $param): self
    {
        return $this->where(function ($query) use ($param) {
            return $query->whereHas('user', function ($query) use ($param) {
                return $query->where('first_name', 'like', '%' . $param . '%')
                    ->orWhere('first_name', 'like', '%' . $param . '%');
            })->orWhereHas('order', function ($query) use ($param) {
                return $query->where('order_number', 'like', '%' . $param . '%')->orWhereHas(
                    'chefStore',
                    function ($query) use ($param) {
                        return $query->where('name', 'like', '%' . $param . '%')
                            ->orWhere('short_description', 'like', '%' . $param . '%')
                            ->orWhere('phone', 'like', '%' . $param . '%');
                    }
                );
            });
        });
    }

    public function userId(int $userId): self
    {
        return $this->where('user_id', $userId);
    }

    public function orderId(int $orderId): self
    {
        return $this->where('order_id', $orderId);
    }

    public function chefStoreId(int $chefStoreId): self
    {
        return $this->whereHas('order', function ($query) use ($chefStoreId) {
            return $query->where('chef_store_id', $chefStoreId);
        });
    }

    public function chefId(int $chefId): self
    {
        return $this->whereHas('order', function ($query) use ($chefId) {
            return $query->whereHas('chefStore', function ($query) use ($chefId) {
                return $query->where('chef_id', $chefId);
            });
        });
    }

    public function orderNumber(string $orderNumber): self
    {
        return $this->whereHas('order', function ($query) use ($orderNumber) {
            return $query->where('order_number', $orderNumber);
        });
    }


    public function type(string $type): self
    {
        return $this->where('type', $type);
    }

    public function status(string $status): self
    {
        return $this->where('status', $status);
    }

    public function paymentMethod(string $paymentMethod): self
    {
        return $this->where('payment_method', $paymentMethod);
    }


    public function fromDate(string $fromDate): self
    {
        return $this->whereDate('created_at', '>=', $fromDate);
    }

    public function toDate(string $toDate): self
    {
        return $this->whereDate('created_at', '<=', $toDate);
    }

    public function sortBy(string $column): self
    {
        if (in_array($column, array_merge(($this->getModel())->getTableColumns(), ['id']))) {
            return $this->orderBy($column, request()->input('sort_type') ?? 'asc');
        }
        return $this;
    }

}

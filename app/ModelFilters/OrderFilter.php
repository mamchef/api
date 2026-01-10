<?php

namespace App\ModelFilters;

use App\Enums\Order\OrderPayoutStatusEnum;
use App\Enums\Order\OrderStatusEnum;
use EloquentFilter\ModelFilter;

class OrderFilter extends ModelFilter
{
    protected $drop_id = false;

    public function __construct($query, array $input = [], $relationsEnabled = true)
    {
        parent::__construct($query, $input, $relationsEnabled);
        if (!request()->input('sort_by')) {
            $this->orderBy('id', 'desc');
        }
    }

    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];

    public function search(string $param): self
    {
        return $this->where(function ($query) use ($param) {
        });
    }


    public function active($param): self
    {
        return $this->whereIn('status', OrderStatusEnum::activeStatuses());
    }

    public function history($param): self
    {
        return $this->whereIn('status', OrderStatusEnum::historyStatuses());
    }

    public function status($param): self
    {
        return $this->where('status', $param);
    }

    public function deliveryType($param): self
    {
        return $this->where('delivery_type', $param);
    }


    public function startDate($param): self
    {
        return $this->where('created_at', '>=', $param);
    }

    public function endDate($param): self
    {
        return $this->where('created_at', '<=', $param);
    }

    public function orderNumber($param): self
    {
        return $this->where('order_number', 'like', "%{$param}%");
    }

    public function sortBy(string $column): self
    {
        if (in_array($column, array_merge(($this->getModel())->getTableColumns(), ['id']))) {
            return $this->orderBy($column, request()->input('sort_type') ?? 'asc');
        }
        return $this;
    }

    public function userId(int $userId): self
    {
        return $this->where('user_id', $userId);
    }

    public function chefStoreId(int $chefStoreId): self
    {
        return $this->where('chef_store_id', $chefStoreId);
    }

    public function payoutStatus(string $status): self
    {
      return  $this->where('payout_status', $status);
    }
}

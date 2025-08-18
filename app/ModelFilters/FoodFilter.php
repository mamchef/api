<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class FoodFilter extends ModelFilter
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
            return $query->where('name', 'like', '%' . $param . '%')
                ->orWhere('description', 'like', '%' . $param . '%');
        });
    }

    public function userSearch(string $param): self
    {
        return $this->where(function ($query) use ($param) {
            return $query->where('name', 'like', '%' . $param . '%')
                ->orWhere('description', 'like', '%' . $param . '%')
                ->orWhereHas('tags', function ($query) use ($param) {
                    return $query->where('name', 'like', '%' . $param . '%');
                })->orWhereHas('chefStore', function ($query) use ($param) {
                    return $query->where('name', 'like', '%' . $param . '%');
                });
        });
    }

    public function tags(string $param): self
    {
        $tags = explode(',', $param);
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        return $this->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('tags.id', $tags);
        });
    }


    public function priceRange(string $range): self
    {
        if (str_contains($range, '+')) {
            $prices[0] = str_replace('+', '', $range);
            $prices[1] = 999999;
        } else {
            $prices = explode('-', $range);
            if (!is_array($prices) || count($prices) < 2) {
                return $this;
            }
        }

        return $this->where('price', '>=', $prices[0])->where('price', '<=', $prices[1]);


    }

    public function rating(string $param): self
    {
        return $this->where('rating', $param);
    }


    public function category(string $param): self
    {
        $tags = explode(',', $param);
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        return $this->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('tags.id', $tags);
        });
    }

    public function sortBy(string $column): self
    {
        if (in_array($column, array_merge(($this->getModel())->getTableColumns(), ['id']))) {
            return $this->orderBy($column, request()->input('sort_type') ?? 'asc');
        }


        if ($column == 'price_low') {
            return $this->orderBy('price', 'asc');
        }

        if ($column == 'price_high') {
            return $this->orderBy('price', 'desc');
        }


        if ($column == 'rating_high') {
            return $this->orderBy('price', 'desc');
        }

        if ($column == 'delivery_time') {
            return $this->join('chef_stores', 'foods.chef_store_id', '=', 'chef_stores.food_id')
                ->orderByRaw('ISNULL(chef_stores.estimated_time), chef_stores.estimated_time ASC')
                ->select('foods.*');
        }

        if ($column == 'newest') {
            return $this->orderBy('created_at', 'desc');
        }

        return $this;
    }

    public function inStock(bool $inStock = true): self
    {
        return $this->where('available_qty', '>', 0);
    }
}

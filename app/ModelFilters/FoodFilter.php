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
                ->orWhereHas('tags',function ($query) use ($param) {
                    return $query->where('name', 'like', '%' . $param . '%');
                })->orWhereHas('chefStore',function ($query) use ($param) {
                    return $query->where('name', 'like', '%' . $param . '%');
                });
        });
    }

    public function tags(string $param): self
    {
        $tags = explode(',', $param);

        return $this->whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('tags.id', $tags);
        });
    }

    public function sortBy(string $column): self
    {
        if (in_array($column, array_merge(($this->getModel())->getTableColumns(), ['id']))) {
            return $this->orderBy($column, request()->input('sort_type') ?? 'asc');
        }
        return $this;
    }
}

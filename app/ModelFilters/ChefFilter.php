<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ChefFilter extends ModelFilter
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
            return $query->where('first_name', 'like', '%' . $param . '%')
                ->orWhere('last_name', 'like', '%' . $param . '%')
                ->orWhere('email', 'like', '%' . $param . '%')
                ->orWhere('phone', 'like', '%' . $param . '%');
        });
    }

    public function status(string $param): self
    {
        return $this->where('status', strtoupper($param));
    }

    public function registerSource(string $param): self
    {
        return $this->where('register_source', strtoupper($param));
    }

    public function sortBy(string $column): self
    {
        if (in_array($column, array_merge(($this->getModel())->getTableColumns(), ['id']))) {
            return $this->orderBy($column, request()->input('sort_type') ?? 'asc');
        }
        return $this;
    }

}

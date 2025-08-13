<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class TagFilter extends ModelFilter
{
    protected $drop_id = false;

    public function __construct($query, array $input = [], $relationsEnabled = true)
    {
        parent::__construct($query, $input, $relationsEnabled);
        if (!request()->input('sort_by')) {
            $this->orderBy('priority');
        }
    }


    public function active(bool $param): self
    {
        return $this->where('status', $param);
    }

    public function status(string $param): self
    {
        return $this->where('status', $param);
    }

    public function homepage(bool $param): self
    {
        return $this->where('homepage', $param);
    }

    public function iconType(string $type): self
    {
        return $this->where('icon_type', $type);
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
                ->orWhere('name', 'like', '%' . $param . '%');
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

<?php

namespace App\Traits;

trait GetTableColumn
{
    public function getTableColumns(array $except = []): array
    {
        $except = array_merge($this->guarded, $except);

        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        foreach ($except as $item) {
            $key = array_search($item, $columns);
            if ($key >= 0) {
                unset($columns[$key]);
            }
        }

        return $columns;
    }
}

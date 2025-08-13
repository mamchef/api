<?php

namespace App\DTOs\Admin\Tag;

use App\DTOs\BaseDTO;

readonly class TagStoreDTO extends BaseDTO
{
    public function __construct(
        protected string $name,
        protected int $priority,
        protected null|string $description = null,
        protected null|string $icon_type = null, //svg , png , ...
        protected null|string $icon = null,
        protected null|int $tag_id = null,
        protected bool $status = true,
        protected bool $homepage = false,
    ) {
    }
}
<?php

namespace App\DTOs\Admin\Tag;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;

readonly class TagUpdateDTO extends BaseDTO
{
    public function __construct(
        protected string|DoNotChange $name = new DoNotChange(),
        protected int|DoNotChange $priority = new DoNotChange(),
        protected null|string|DoNotChange $description = new DoNotChange(),
        protected null|string|DoNotChange $icon_type = new DoNotChange(), //svg , png , ...
        protected null|string|DoNotChange $icon = new DoNotChange(),
        protected null|int|DoNotChange $tag_id = new DoNotChange(),
        protected bool|DoNotChange $status = new DoNotChange(),
        protected bool|DoNotChange $homepage = new DoNotChange(),
    ) {
    }
}
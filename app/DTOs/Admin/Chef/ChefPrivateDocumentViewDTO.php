<?php

namespace App\DTOs\Admin\Chef;

use App\DTOs\BaseDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use Illuminate\Http\UploadedFile;

readonly class ChefPrivateDocumentViewDTO extends BaseDTO
{
    public function __construct(
        public string $path,
        public string $name,
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
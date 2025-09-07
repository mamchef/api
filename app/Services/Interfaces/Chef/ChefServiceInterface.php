<?php

namespace App\Services\Interfaces\Chef;

use App\DTOs\Admin\Chef\ChefPrivateDocumentViewDTO;
use App\DTOs\Admin\Chef\ChefUpdateByAdminDTO;
use App\Models\Chef;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ChefServiceInterface
{
    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator;


    public function show(int $chefId, array $relations = []): Chef;


    public function update(int $chefId ,ChefUpdateByAdminDTO $DTO): Chef;

    public function getChefDocumentByFiledName(int $chefId, string $fieldName) : ChefPrivateDocumentViewDTO;


    public function handleChefApproval(int $chefId): void;


}
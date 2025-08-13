<?php

namespace App\Services\Chef;

use App\DTOs\Admin\Chef\ChefPrivateDocumentViewDTO;
use App\DTOs\Admin\Chef\ChefUpdateByAdminDTO;
use App\Models\Chef;
use App\Services\Interfaces\Chef\ChefServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChefService implements ChefServiceInterface
{

    public function all(
        ?array $filters = null,
        array $relations = [],
        $pagination = null
    ): Collection|LengthAwarePaginator {
        $chefs = Chef::query()->when($relations, fn($q) => $q->with($relations))
            ->when($filters, fn($q) => $q->filter($filters));

        return $pagination ? $chefs->paginate($pagination) : $chefs->get();
    }

    public function show(int $chefId, array $relations = []): Chef
    {
        return Chef::query()->with($relations)
            ->findOrFail($chefId);
    }

    public function update(int $chefId, ChefUpdateByAdminDTO $DTO): Chef
    {
        $chef = $this->show($chefId);

        $data = $DTO->toArray();

        if (isset($data['document_1'])) {
            $data['document_1'] = $this->uploadPrivateDoc(
                file: $data['document_1'],
                path: "chef/$chefId",
                name: "document1",
            );
        }

        if (isset($data['document_2'])) {
            $data['document_2'] = $this->uploadPrivateDoc(
                file: $data['document_2'],
                path: "chef/$chefId",
                name: "document2",
            );
        }

        if (isset($data['contract'])) {
            $data['contract'] = $this->uploadPrivateDoc(
                file: $data['contract'],
                path: "chef/$chefId",
                name: "contract",
            );
        }

        if (isset($data['password'])) {
            $data['password'] = Chef::generatePassword($data['password']);
        }

        $chef->update($data);

        return $chef->fresh();
    }


    private function uploadPrivateDoc(UploadedFile $file, string $path, string $name): false|string
    {
        return Storage::disk("private")->putFileAs(
            $path,
            $file,
            $name . "." . $file->getClientOriginalExtension(),
        );
    }


    public function getChefDocumentByFiledName(int $chefId, string $fieldName): ChefPrivateDocumentViewDTO
    {
        $chef = $this->show($chefId);
        if (!in_array($fieldName, $chef->getTableColumns())) {
            abort(404, 'Document not found');
        }

        $path = $chef->{$fieldName} ?? null;

        // Check if attachment exists
        if ($path == null || !Storage::disk('private')->exists($path)) {
            abort(404, 'Document not found');
        }

        // Get full path
        $filePath = Storage::disk('private')->path($path);

        // Extract original filename
        $pathInfo = pathinfo($path);
        $originalName = $pathInfo['basename'] ?? 'document';


        return new ChefPrivateDocumentViewDTO(
            path: $filePath, name: $originalName,
        );
    }
}
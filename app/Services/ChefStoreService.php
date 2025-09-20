<?php

namespace App\Services;

use App\DTOs\Admin\Chef\ChefUpdateByAdminDTO;
use App\DTOs\Admin\ChefStore\ChefStoreUpdateByAdminDTO;
use App\DTOs\Chef\ChefStore\UpdateChefStoreByChefDTO;
use App\Enums\Chef\ChefStore\ChefStoreStatusEnum;
use App\Models\ChefStore;
use App\Services\Interfaces\ChefStoreServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChefStoreService implements ChefStoreServiceInterface
{

    public function myStore(int $chefID): ChefStore
    {
        $chef = ChefStore::query()->where('chef_id', $chefID)->first();
        if ($chef == null) {
            $chef = ChefStore::query()->create(['chef_id' => $chefID, 'delivery_cost' => 3,'status'=>ChefStoreStatusEnum::NeedCompleteData])->fresh();
        }
        return $chef;
    }

    public function getBySlug(string $slug, array $relations = []): ChefStore
    {
        return ChefStore::query()->where('slug', $slug)->with($relations)->firstOrFail();
    }


    public function setProfileImageByChef(int $chefID, UploadedFile $file): ChefStore
    {
        $chefStore = $this->myStore($chefID);

        $path = Storage::disk("public")->putFileAs(
            "chef_store/$chefStore->id",
            $file,
            "profile_image." . $file->getClientOriginalExtension(),
        );

        $chefStore->profile_image = $path;
        if (config('app.need_approve_on_chef_store_edit')) {
            $chefStore->status = ChefStoreStatusEnum::UnderReview;
        }
        $chefStore->save();
        return $chefStore;
    }

    public function updateByChef(int $chefID, UpdateChefStoreByChefDTO $DTO): ChefStore
    {
        $chefStore = $this->myStore($chefID);
        $params = $DTO->toArray();

        if (isset($params["profile_image"])) {
            $path = Storage::disk("public")->putFileAs(
                "chef_store/$chefStore->id",
                $params["profile_image"],
                "profile_image." . $params["profile_image"]->getClientOriginalExtension(),
            );
            $params["profile_image"] = $path;
            if (config('app.need_approve_on_chef_store_edit')) {
                $params["status"] = ChefStoreStatusEnum::UnderReview;
            }
        }


        $chefStore->update($params);

        return $chefStore->fresh();
    }

    public function toggleIsOpenByChef(int $chefID, bool $isOpen): ChefStore
    {
        $chefStore = $this->myStore($chefID);
        $chefStore->is_open = $isOpen;
        $chefStore->save();
        return $chefStore->fresh();
    }

    public function all(
        ?array $filters = null,
        array  $relations = [],
               $pagination = null
    ): Collection|LengthAwarePaginator
    {
        $chefStores = ChefStore::query()->when($relations, fn($q) => $q->with($relations))
            ->when($filters, fn($q) => $q->filter($filters));

        return $pagination ? $chefStores->paginate($pagination) : $chefStores->get();
    }

    public function show(int $chefStoreId, array $relations = []): ChefStore
    {
        return ChefStore::query()->with($relations)->findOrFail($chefStoreId);
    }

    public function update(int $chefStoreId, ChefStoreUpdateByAdminDTO $DTO): ChefStore
    {
        $chefStore = $this->show($chefStoreId);
        $data = $DTO->toArray();

        if (isset($data['profile_image'])) {
            $path = Storage::disk("public")->putFileAs(
                "chef_store/$chefStore->id",
                $data["profile_image"],
                "profile_image." . $data["profile_image"]->getClientOriginalExtension(),
            );
            $data["profile_image"] = $path;
        }

        $chefStore->update($data);
        return $chefStore->fresh();

    }
}
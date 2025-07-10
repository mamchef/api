<?php

namespace App\Services;

use App\DTOs\Chef\ChefStore\UpdateChefStoreByChefDTO;
use App\Models\ChefStore;
use App\Services\Interfaces\ChefStoreServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChefStoreService implements ChefStoreServiceInterface
{

    public function myStore(int $chefID): ChefStore
    {
        $chef = ChefStore::query()->where('chef_id', $chefID)->first();
        if ($chef == null) {
            $chef = ChefStore::query()->create(['chef_id' => $chefID])->fresh();
        }
        return $chef;
    }

    public function getBySlug(string $slug , array $relations = []): ChefStore
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
}
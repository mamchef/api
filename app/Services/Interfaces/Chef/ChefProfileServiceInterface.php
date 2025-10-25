<?php

namespace App\Services\Interfaces\Chef;

use App\DTOs\Chef\PersonalInfo\UpdateProfileByChefDTO;
use App\Models\Chef;
use Illuminate\Http\UploadedFile;

interface ChefProfileServiceInterface
{

    /**
     * @param Chef $chef
     * @param string $password
     * @return void
     */
    public function changePassword(Chef $chef, string $currentPassword, string $newPassword): void;


    public function updateProfileByChef(int $chefID, UpdateProfileByChefDTO $DTO): Chef;

    public function updateDocumentsByChef(int $chefID, UploadedFile $document1, string $vmvtNumber, ?UploadedFile $document2 =null): Chef;

    public function fetchChefSignedContract(string $envelopeID): void;
}
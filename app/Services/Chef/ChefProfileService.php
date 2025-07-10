<?php

namespace App\Services\Chef;

use App\DTOs\Chef\PersonalInfo\UpdateProfileByChefDTO;
use App\Enums\Chef\ChefStatusEnum;
use App\Jobs\SendContractJob;
use App\Models\Chef;
use App\Services\DocuSignService;
use App\Services\Interfaces\Chef\ChefProfileServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ChefProfileService implements ChefProfileServiceInterface
{

    /** @inheritDoc */
    public function changePassword(Chef $chef, string $currentPassword, string $newPassword): void
    {
        if ($currentPassword === $newPassword) {
            throw ValidationException::withMessages([
                'password' => __('validation.attributes.different_password'),
            ]);
        }

        if (!$chef->passwordCheck($currentPassword)) {
            throw ValidationException::withMessages([
                'password' => __('validation.attributes.current_password_not_match'),
            ]);
        }

        $chef->password = Chef::generatePassword($newPassword);
        $chef->save();
    }

    public function updateProfileByChef(int $chefID, UpdateProfileByChefDTO $DTO): Chef
    {
        $chef = Chef::query()->findOrFail($chefID);
        if (!in_array($chef->status, ChefStatusEnum::profileEditable())) {
            throw ValidationException::withMessages([
                "error" => __("public.operation_denied")
            ]);
        }
        $chef = $chef->updateByDTO($DTO);

        if ($chef->status == ChefStatusEnum::Registered) {
            $chef->status = ChefStatusEnum::PersonalInfoFilled;
            $chef->save();

            SendContractJob::dispatch($chef);
        }

        return $chef->fresh();
    }

    public function updateDocumentsByChef(int $chefID, UploadedFile $document1, UploadedFile $document2): Chef
    {
        $chef = Chef::query()->findOrFail($chefID);

        if ($chef->document_1 != null and $chef->document_2 != null) {
            throw ValidationException::withMessages([
                "error" => __("public.operation_denied")
            ]);
        }

        $document1 = Storage::disk("private")->putFileAs(
            "chef/$chefID",
            $document1,
            "document1." . $document1->getClientOriginalExtension(),
        );

        $document2 = Storage::disk("private")->putFileAs(
            "chef/$chefID",
            $document2,
            "document2." . $document2->getClientOriginalExtension(),
        );

        $chef->document_1 = $document1;
        $chef->document_2 = $document2;

        //UPDATE TO NEED REVIEW COX ALL DOCUMENT UPLOADED AND CONTRACT SIGNED
        if ($chef->status == ChefStatusEnum::PersonalInfoFilled) {
            $chef->status = ChefStatusEnum::DocumentUploaded;
        } elseif ($chef->status == ChefStatusEnum::ContractSigned) {
            $chef->status = ChefStatusEnum::NeedAdminApproval;
        }

        $chef->save();
        return $chef->fresh();
    }

    public function fetchChefSignedContract(string $envelopeID): void
    {
        $chef = Chef::query()->where('contract_id', $envelopeID)
            ->firstOrFail();

        $docuSignService = new DocuSignService();
        $doc = $docuSignService->downloadEnvelope($envelopeID);

        $contract = Storage::disk("private")->putFileAs(
            "chef/$chef->id",
            $doc,
            "signed_docs-" . $chef->contract_id . ".pdf",
        );

        $chef->contract = $contract;

        //UPDATE TO NEED REVIEW COX ALL DOCUMENT UPLOADED AND CONTRACT SIGNED
        if ($chef->status == ChefStatusEnum::PersonalInfoFilled) {
            $chef->status = ChefStatusEnum::ContractSigned;
        } elseif ($chef->status == ChefStatusEnum::DocumentUploaded) {
            $chef->status = ChefStatusEnum::NeedAdminApproval;
        }

        $chef->save();
    }
}
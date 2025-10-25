<?php

namespace App\Services\Chef;

use App\DTOs\Chef\PersonalInfo\UpdateProfileByChefDTO;
use App\Enums\Chef\ChefStatusEnum;
use App\Jobs\SendContractJob;
use App\Models\Chef;
use App\Notifications\Chef\ChefWelcomeNotification;
use App\Services\ChefStripeOnboardingService;
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
        $chef->refresh();
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

        $chef->update([
            'password' => Chef::generatePassword($newPassword)
        ]);
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

            $chef->notify(new ChefWelcomeNotification($chef->fresh()));
        }

        return $chef->fresh();
    }

    public function updateDocumentsByChef(int $chefID, UploadedFile $document1, string $vmvtNumber, ?UploadedFile $document2 = null): Chef
    {
        $chef = Chef::query()->findOrFail($chefID);

        if ($chef->document_1 != null and $chef->vmvt_number != null) {
            throw ValidationException::withMessages([
                "error" => __("public.operation_denied")
            ]);
        }

        $document1 = Storage::disk("private")->putFileAs(
            "chef/$chefID",
            $document1,
            "document1." . $document1->getClientOriginalExtension(),
        );

        if ($document2 != null) {
            $document2 = Storage::disk("private")->putFileAs(
                "chef/$chefID",
                $document2,
                "document2." . $document2->getClientOriginalExtension(),
            );
            $chef->document_2 = $document2;
        }

        $chef->document_1 = $document1;
        $chef->vmvt_number = $vmvtNumber;

        //UPDATE TO NEED REVIEW COX ALL DOCUMENT UPLOADED AND CONTRACT SIGNED
        if ($chef->status == ChefStatusEnum::PersonalInfoFilled) {
            $chef->status = ChefStatusEnum::DocumentUploaded;
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
        if ($chef->status == ChefStatusEnum::DocumentUploaded) {
            $chef->status = ChefStatusEnum::NeedAdminApproval;
        }

        $chef->save();

        $this->handleChefApproval($chef->fresh());
    }

    /**
     * Handle chef approval process - create Stripe account and send onboarding email
     */
    public function handleChefApproval(Chef $chef): void
    {
        try {
            if ($chef->stripe_account_id) {
                return;
            }
            $stripeService = new ChefStripeOnboardingService();

            // Get language from request or default to 'en'
            $lang = request()->header('Language') ?? 'en';

            // Create Stripe account and send onboarding email
            $result = $stripeService->completeOnboarding($chef, $lang);

            if ($result['success']) {
                \Log::info("Stripe onboarding initiated for chef {$chef->id}: {$result['message']}");
            } else {
                \Log::error("Failed to initiate Stripe onboarding for chef {$chef->id}: {$result['error']}");
            }

        } catch (\Exception $e) {
            // Log error but don't fail the chef approval
            \Log::error("Error during chef Stripe onboarding for chef {$chef->id}: " . $e->getMessage());
        }
    }
}
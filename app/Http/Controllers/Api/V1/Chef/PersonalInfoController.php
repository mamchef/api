<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\PersonalInfo\UpdateProfileByChefDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\Profile\UpdateDocumentsRequest;
use App\Http\Requests\Api\V1\Chef\Profile\ChangePasswordRequest;
use App\Http\Requests\Api\V1\Chef\Profile\UpdateProfileRequest;
use App\Http\Resources\V1\Chef\ChefProfileResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Chef;
use App\Services\Interfaces\Chef\ChefProfileServiceInterface;
use Illuminate\Support\Facades\Auth;

class PersonalInfoController extends Controller
{

    public function __construct(protected readonly ChefProfileServiceInterface $chefProfileService)
    {
    }

    public function profile(): ChefProfileResource
    {
        return ChefProfileResource::make(Chef::query()->findOrFail(Auth::id()));
    }


    public function changePassword(ChangePasswordRequest $request): SuccessResponse
    {
        /** @var Chef $chef */
        $chef = Auth::user();
        $this->chefProfileService->changePassword(
            chef: $chef,
            currentPassword: $request->current_password,
            newPassword: $request->password,
        );

        return new SuccessResponse(
            message: __('auth.password_reset.success')
        );
    }

    public function updateProfile(UpdateProfileRequest $request): SuccessResponse
    {
        $this->chefProfileService->updateProfileByChef(
            chefID: Auth::id(),
            DTO: UpdateProfileByChefDTO::toDTO($request->validated())
        );

        return new SuccessResponse();
    }

    public function uploadDocuments(UpdateDocumentsRequest $request): SuccessResponse
    {
        $this->chefProfileService->updateDocumentsByChef(
            chefID: Auth::id(),
            document1: $request->document_1,
            vmvtNumber: $request->vmvt_number,
        );
        return new SuccessResponse();
    }


}
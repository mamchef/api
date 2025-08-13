<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Chef\ChefUpdateByAdminDTO;
use App\DTOs\DoNotChange;
use App\Enums\Chef\ChefStatusEnum;
use App\Enums\RegisterSourceEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Chef\ChefUpdateByAdminRequest;
use App\Http\Resources\V1\Admin\Chef\ChefResource;
use App\Http\Resources\V1\Admin\Chef\ChefsResource;
use App\Services\Interfaces\Chef\ChefServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ChefController extends Controller
{
    public function __construct(protected ChefServiceInterface $chefService)
    {
    }


    public function index(Request $request): ResourceCollection
    {
        $chefs = $this->chefService->all(
            filters: $request->all(),
            relations: ["city"],
            pagination: self::validPagination()
        );
        return ChefsResource::collection($chefs);
    }

    public function show(int $chefId): ChefResource
    {
        $chef = $this->chefService->show(
            chefId: $chefId,
            relations: ["city", 'chefStore'],
        );

        return new ChefResource($chef);
    }

    public function update(ChefUpdateByAdminRequest $request, int $chefId): ChefResource
    {
        $chef = $this->chefService->update(
            chefId: $chefId,
            DTO: new ChefUpdateByAdminDTO(
                id_number: $request->has("id_number") ? $request->id_number : DoNotChange::value(),
                first_name: $request->has("first_name") ? $request->first_name : DoNotChange::value(),
                last_name: $request->has("last_name") ? $request->last_name : DoNotChange::value(),
                email: $request->has("email") ? $request->email : DoNotChange::value(),
                register_source: $request->get("register_source") ? RegisterSourceEnum::getEnum(
                    $request->register_source
                ) : DoNotChange::value(),
                password: $request->has("password") ? $request->password : DoNotChange::value(),
                phone: $request->has("phone") ? $request->phone : DoNotChange::value(),
                city_id: $request->has("city_id") ? $request->city_id : DoNotChange::value(),
                main_street: $request->has('main_street') ? $request->main_street : DoNotChange::value(),
                address: $request->has('address') ? $request->address : DoNotChange::value(),
                zip: $request->has('zip') ? $request->zip : DoNotChange::value(),
                status: $request->has("status") ? ChefStatusEnum::getEnum($request->status) : DoNotChange::value(),
                document_1: $request->has('document_1') ? $request->document_1 : DoNotChange::value(),
                document_2: $request->has('document_2') ? $request->document_2 : DoNotChange::value(),
                contract_id: $request->has('contract_id') ? $request->contract_id : DoNotChange::value(),
                contract: $request->has('contract') ? $request->contract : DoNotChange::value(),
            )
        );

        return new ChefResource($chef);
    }

    public function getChefDocumentByFieldName(int $chefId, string $fieldName): BinaryFileResponse
    {
        $dto = $this->chefService->getChefDocumentByFiledName(
            chefId: $chefId,
            fieldName: $fieldName
        );


        // Handle CORS
        $origin = request()->header('Origin');
        $allowedOrigins = config('cors.allowed_origins', []);

        if (!in_array($origin, $allowedOrigins)) {
            $origin = "*";
        }

        // Use Laravel's download method which handles headers properly
        return response()->download($dto->getPath(), $dto->getName(), [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
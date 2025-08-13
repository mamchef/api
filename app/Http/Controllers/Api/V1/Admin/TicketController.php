<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\DTOs\Admin\Ticket\AdminStoreTicketDTO;
use App\DTOs\Admin\Ticket\AdminStoreTicketItemDTO;
use App\Enums\Ticket\TicketStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Ticket\SetTicketStatusByAdminRequest;
use App\Http\Requests\Api\V1\Admin\Ticket\TicketItemStoreRequest;
use App\Http\Requests\Api\V1\Admin\Ticket\TicketStoreRequest;
use App\Http\Resources\V1\Admin\Ticket\TicketResource;
use App\Http\Resources\V1\Admin\Ticket\TicketsResource;
use App\Models\Chef;
use App\Models\User;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketController extends Controller
{
    public function __construct(protected TicketServiceInterface $ticketService)
    {
    }

    /**
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $tickets = $this->ticketService->all(
            filters: $request->all(),
            relations: ['ticketable'],
            pagination: self::validPagination()
        );
        return TicketsResource::collection($tickets);
    }

    /**
     * @param int $chefId
     * @return ResourceCollection
     */
    public function getChefTickets(int $chefId): ResourceCollection
    {
        return TicketsResource::collection($this->ticketService->getChefTickets($chefId));
    }

    /**
     * @param int $userId
     * @return ResourceCollection
     */
    public function getUserTickets(int $userId): ResourceCollection
    {
        return TicketsResource::collection($this->ticketService->getUserTickets($userId));
    }

    /**
     * @param int $ticketId
     * @return TicketResource
     */
    public function show(int $ticketId): TicketResource
    {
        return new TicketResource(
            $this->ticketService->show(
                ticketId: $ticketId,
                relations: ['ticketable', 'items']
            )
        );
    }

    /**
     * @param TicketStoreRequest $request
     * @return TicketResource
     */
    public function store(TicketStoreRequest $request): TicketResource
    {
        $ticket = $this->ticketService->storeTicketByAdmin(
            DTO: new AdminStoreTicketDTO(
                title: $request->title,
                adminId: Auth::id(),
                targetId: $request->target_id,
                targetClass: $request->target_type == 'user' ? User::class : Chef::class,
                description: $request->description,
                status: TicketStatusEnum::getEnum($request->status),
                attachment: $request->attachment,
            )
        );
        return new TicketResource(
            $ticket
        );
    }

    /**
     * @param TicketItemStoreRequest $request
     * @param int $ticketId
     * @return TicketResource
     */
    public function storeTicketItem(TicketItemStoreRequest $request, int $ticketId): TicketResource
    {
        $DTO = new AdminStoreTicketItemDTO(
            ticketId: $ticketId,
            adminId: Auth::id(),
            description: $request->description,
            status: TicketStatusEnum::getEnum($request->status),
            attachment: $request->attachment ?? null,
        );
        $this->ticketService->storeTicketItemByAdmin(DTO: $DTO);

        return new TicketResource(
            $this->ticketService->show(
                ticketId: $ticketId,
                relations: ['ticketable', 'items']
            )
        );
    }

    /**
     * @param SetTicketStatusByAdminRequest $request
     * @param int $ticketId
     * @return TicketResource
     */
    public function setStatus(SetTicketStatusByAdminRequest $request , int $ticketId): TicketResource
    {
        $ticket = $this->ticketService->setTicketStatusByAdmin(
            ticketId:$ticketId,
            status: TicketStatusEnum::getEnum($request->status)
        );
        return new TicketResource($ticket);
    }

    /**
     * @param int $ticketItemId
     * @return BinaryFileResponse
     */
    public function getTicketItemAttachment(int $ticketItemId): BinaryFileResponse
    {
        $dto = $this->ticketService->getTicketItemAttachmentByAdmin(
            ticketItemId: $ticketItemId,
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
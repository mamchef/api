<?php

namespace App\Http\Controllers\Api\V1\User;

use App\DTOs\User\Ticket\UserStoreTicketDTO;
use App\DTOs\User\Ticket\UserStoreTicketItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Ticket\TicketItemStoreRequest;
use App\Http\Requests\Api\V1\User\Ticket\TicketStoreRequest;
use App\Http\Resources\V1\SuccessResponse;
use App\Http\Resources\V1\User\Ticket\TicketResource;
use App\Models\TicketItem;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class TicketController extends Controller
{
    public function __construct(protected TicketServiceInterface $ticketService)
    {
    }

    public function index(Request $request)
    {
        $tickets = $this->ticketService->getUserTickets(
            userId: Auth::id(),
            filters: [],
            relations: [
                'items.itemable'
            ],
            pagination: $this->validPagination()
        );

        return TicketResource::collection($tickets);
    }


    public function show(int $ticketId): TicketResource
    {
        $ticket = $this->ticketService->getUserTicket(
            userId: Auth::id(),
            ticketId: $ticketId,
            relations: [
                'items.itemable'
            ]
        );
        return TicketResource::make($ticket);
    }

    public function store(TicketStoreRequest $request): SuccessResponse
    {
        $DTO = new UserStoreTicketDTO(
            title: $request->title,
            userId: Auth::id(),
            description: $request->description,
            priority: $request->priority,
            attachment: $request->attachment ?? null,
        );

        $this->ticketService->storeTicketByUser(DTO: $DTO);
        return new SuccessResponse();
    }


    public function storeTicketItem(TicketItemStoreRequest $request, int $ticketId): TicketResource
    {
        $DTO = new UserStoreTicketItemDTO(
            ticketId: $ticketId,
            userId: Auth::id(),
            description: $request->description,
            attachment: $request->attachment ?? null,
        );

        return TicketResource::make($this->ticketService->storeTicketItemByUser(DTO: $DTO));
    }


    public function getTicketItemAttachment(int $ticketItemId)
    {
        // Verify ownership and get ticket item
        $ticketItem = TicketItem::forUser(Auth::id())->where('id', $ticketItemId)->firstOrFail();

        // Check if attachment exists
        if (!$ticketItem->attachment || !Storage::disk('private')->exists($ticketItem->attachment)) {
            abort(404, 'Attachment not found');
        }

        // Get full path
        $filePath = Storage::disk('private')->path($ticketItem->attachment);

        // Extract original filename
        $pathInfo = pathinfo($ticketItem->attachment);
        $originalName = $pathInfo['basename'] ?? 'attachment';

        // Handle CORS
        $origin = request()->header('Origin');
        $allowedOrigins = config('cors.allowed_origins', []);

        if (!in_array($origin, $allowedOrigins)) {
            $origin = "*";
        }

        // Use Laravel's download method which handles headers properly
        return response()->download($filePath, $originalName, [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
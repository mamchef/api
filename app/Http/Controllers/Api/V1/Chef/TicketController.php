<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\DTOs\Chef\Ticket\ChefStoreTicketDTO;
use App\DTOs\Chef\Ticket\ChefStoreTicketItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chef\Ticket\TicketItemStoreRequest;
use App\Http\Requests\Api\V1\Chef\Ticket\TicketStoreRequest;
use App\Http\Resources\V1\Chef\Ticket\TicketResource;
use App\Http\Resources\V1\SuccessResponse;
use App\Models\Ticket;
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
        $tickets = $this->ticketService->getChefTickets(
            chefId: Auth::id(),
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
        $ticket = $this->ticketService->getChefTicket(
            chefId: Auth::id(),
            ticketId: $ticketId,
            relations: [
                'items.itemable'
            ]
        );
        return TicketResource::make($ticket);
    }

    public function store(TicketStoreRequest $request): SuccessResponse
    {
        $DTO = new ChefStoreTicketDTO(
            title: $request->title,
            chefId: Auth::id(),
            description: $request->description,
            attachment: $request->attachment ?? null,
        );

        $this->ticketService->storeTicketByChef(DTO: $DTO);
        return new SuccessResponse();
    }


    public function storeTicketItem(TicketItemStoreRequest $request , int $ticketId): SuccessResponse
    {
        $DTO = new ChefStoreTicketItemDTO(
            ticketId: $ticketId,
            chefId: Auth::id(),
            description: $request->description,
            attachment: $request->attachment ?? null,
        );

        $this->ticketService->storeTicketItemByChef(DTO: $DTO);
        return new SuccessResponse();
    }


    public function getTicketItemAttachment(int $ticketItemId)
    {
        // Verify ownership and get ticket item
        $ticketItem = TicketItem::forChef(Auth::id())->where('id', $ticketItemId)->firstOrFail();

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
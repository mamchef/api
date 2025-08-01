<?php

namespace App\Http\Resources\V1\Admin\Ticket;

use App\Http\Resources\V1\BaseResource;
use App\Models\Chef;
use App\Models\Ticket;
use App\Models\TicketItem;
use App\Models\User;

class TicketsResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Ticket $ticket */
        $ticket = $this->resource;
        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'ticketable_id' => $ticket->ticketable_id,
            'ticketable_type' => $ticket->ticketable_type,
            'ticketable' => $ticket->ticketable,
            'ticket_number' => $ticket->ticket_number,
            'status' => $ticket->status->value,
            'for' => $ticket->ticketable_type == Chef::class ? 'chef' : 'user',
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
        ];
    }

}
<?php

namespace App\Http\Resources\V1\Chef\Ticket;

use App\Http\Resources\V1\BaseResource;
use App\Models\Ticket;
use App\Models\TicketItem;

class TicketResource extends BaseResource
{

    public function prePareData($request): array
    {
        /** @var Ticket $ticket */
        $ticket = $this->resource;
        $items = [];
        foreach ($ticket->items as $item) {
            $items[] = $this->prepareItems($item);
        }
        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'ticket_number' => $ticket->ticket_number,
            'items' => $items,
            'created_at' => $ticket->created_at,
            'updated_at' => $ticket->updated_at,
        ];
    }

    public function prepareItems(TicketItem $item): array
    {
        return [
            'id' => $item->id,
            'itemable' => $item->itemable,
            'description' => $item->description,
            'attachment' => $item->attachment,
            'created_by' => $item->created_by?->value,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
        ];
    }
}
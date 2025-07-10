<?php

namespace App\Services;

use App\DTOs\Chef\Ticket\ChefStoreTicketDTO;
use App\DTOs\Chef\Ticket\ChefStoreTicketItemDTO;
use App\DTOs\User\Ticket\UserStoreTicketDTO;
use App\DTOs\User\Ticket\UserStoreTicketItemDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Models\Chef;
use App\Models\Ticket;
use App\Models\TicketItem;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TicketService implements TicketServiceInterface
{

    /** @inheritDoc */
    public function getChefTickets(
        int $chefId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $tickets = Ticket::forChef($chefId)->filter($filters)
            ->with($relations);
        return $pagination ? $tickets->paginate($pagination) : $tickets->get();
    }

    /** @inheritDoc */
    public function getUserTickets(
        int $userId,
        array $filters = [],
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $tickets = Ticket::forUser($userId)->filter($filters)
            ->with($relations);
        return $pagination ? $tickets->paginate($pagination) : $tickets->get();
    }


    /** @inheritDoc */
    public function getChefTicket(int $chefId, int $ticketId, array $relations = []): Ticket
    {
        return Ticket::forChef($chefId)->with($relations)->where('id', $ticketId)->firstOrFail();
    }


    /** @inheritDoc */
    public function getUserTicket(int $userId, int $ticketId, array $relations = []): Ticket
    {
        return Ticket::forUser($userId)->with($relations)->where('id', $ticketId)->firstOrFail();
    }

    /** @inheritDoc */
    public function storeTicketByChef(ChefStoreTicketDTO $DTO): Ticket
    {
        DB::beginTransaction();
        try {
            /** @var Ticket $ticket */
            $ticket = Ticket::query()->create($DTO->toArray());

            $ticketItemParams = [
                'ticket_id' => $ticket->id,
                'itemable_id' => $DTO->getChefId(),
                'itemable_type' => Chef::class,
                'description' => $DTO->getDescription(),
                'created_by' => TicketItemCreateByEnum::CHEF,
            ];
            $ticketItem = TicketItem::query()->create($ticketItemParams);

            if ($DTO->getAttachment()) {
                $ticketItem->attachment = $this->uploadAttachment(
                    ticketId: $ticket->id,
                    ticketItemId: $ticketItem->id,
                    file: $DTO->getAttachment()
                );
                $ticketItem->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $ticket->loadMissing('items');
    }


    /** @inheritDoc */
    public function storeTicketByUser(UserStoreTicketDTO $DTO): Ticket
    {
        DB::beginTransaction();
        try {
            /** @var Ticket $ticket */
            $ticket = Ticket::query()->create($DTO->toArray());

            $ticketItemParams = [
                'ticket_id' => $ticket->id,
                'itemable_id' => $DTO->getUserId(),
                'itemable_type' => Chef::class,
                'description' => $DTO->getDescription(),
                'created_by' => TicketItemCreateByEnum::CHEF,
            ];
            $ticketItem = TicketItem::query()->create($ticketItemParams);

            if ($DTO->getAttachment()) {
                $ticketItem->attachment = $this->uploadAttachment(
                    ticketId: $ticket->id,
                    ticketItemId: $ticketItem->id,
                    file: $DTO->getAttachment()
                );
                $ticketItem->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $ticket->loadMissing('items');
    }

    /** @inheritDoc */
    public function storeTicketItemByChef(ChefStoreTicketItemDTO $DTO): Ticket
    {
        DB::beginTransaction();
        try {
            $ticket = $this->getChefTicket(
                chefId: $DTO->getChefId(),
                ticketId: $DTO->getTicketId()
            );

            $ticketItem = TicketItem::query()->create($DTO->toArray());

            if ($DTO->getAttachment()) {
                $ticketItem->attachment = $this->uploadAttachment(
                    ticketId: $ticket->id,
                    ticketItemId: $ticketItem->id,
                    file: $DTO->getAttachment()
                );
                $ticketItem->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $ticket->loadMissing('items');
    }


    /** @inheritDoc */
    public function storeTicketItemByUser(UserStoreTicketItemDTO $DTO): Ticket
    {
        DB::beginTransaction();
        try {
            $ticket = $this->getChefTicket(
                chefId: $DTO->getUserId(),
                ticketId: $DTO->getTicketId()
            );

            $ticketItem = TicketItem::query()->create($DTO->toArray());

            if ($DTO->getAttachment()) {
                $ticketItem->attachment = $this->uploadAttachment(
                    ticketId: $ticket->id,
                    ticketItemId: $ticketItem->id,
                    file: $DTO->getAttachment()
                );
                $ticketItem->save();
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $ticket->loadMissing('items');
    }

    private function uploadAttachment(int $ticketId, int $ticketItemId, UploadedFile $file): string
    {
        return Storage::disk("private")->putFileAs(
            "tickets/{$ticketId}",
            $file,
            "attachment-{$ticketItemId}." . $file->getClientOriginalExtension(),
        );
    }
}
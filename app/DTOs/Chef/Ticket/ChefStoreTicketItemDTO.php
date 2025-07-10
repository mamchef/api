<?php

namespace App\DTOs\Chef\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Models\Chef;
use Illuminate\Http\UploadedFile;

readonly class ChefStoreTicketItemDTO extends BaseDTO
{
    public function __construct(
        protected int $ticketId,
        protected int $chefId,
        protected string $description,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getChefId(): int
    {
        return $this->chefId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAttachment(): ?UploadedFile
    {
        return $this->attachment;
    }


    public function toArray(): array
    {
        return [
            'ticket_id' => $this->getTicketId(),
            'itemable_id' => $this->getChefId(),
            'itemable_type' => Chef::class,
            'description' => $this->getDescription(),
            'created_by' => TicketItemCreateByEnum::CHEF,
        ];
    }

}
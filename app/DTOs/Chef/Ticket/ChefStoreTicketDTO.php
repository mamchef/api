<?php

namespace App\DTOs\Chef\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Enums\Ticket\TicketStatusEnum;
use App\Models\Chef;
use Illuminate\Http\UploadedFile;

readonly class ChefStoreTicketDTO extends BaseDTO
{
    public function __construct(
        protected string $title,
        protected int $chefId,
        protected string $description,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
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
            'title' => $this->getTitle(),
            'ticketable_type' => Chef::class,
            'ticketable_id' => $this->getChefId(),
            'status' => TicketStatusEnum::USER_CREATED,
        ];
    }
}
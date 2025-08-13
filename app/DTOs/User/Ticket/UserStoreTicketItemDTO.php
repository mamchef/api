<?php

namespace App\DTOs\User\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Models\Chef;
use App\Models\User;
use Illuminate\Http\UploadedFile;

readonly class UserStoreTicketItemDTO extends BaseDTO
{
    public function __construct(
        protected int $ticketId,
        protected int $userId,
        protected string $description,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getUserId(): int
    {
        return $this->userId;
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
            'itemable_id' => $this->getUserId(),
            'itemable_type' => User::class,
            'description' => $this->getDescription(),
            'created_by' => TicketItemCreateByEnum::USER,
        ];
    }

}
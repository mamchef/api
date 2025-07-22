<?php

namespace App\DTOs\User\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketStatusEnum;
use App\Models\User;
use Illuminate\Http\UploadedFile;

readonly class UserStoreTicketDTO extends BaseDTO
{
    public function __construct(
        protected string $title,
        protected int $userId,
        protected string $description,
        protected string $priority,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }


    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getAttachment(): ?UploadedFile
    {
        return $this->attachment;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'ticketable_type' => User::class,
            'ticketable_id' => $this->getUserId(),
            'status' => TicketStatusEnum::USER_CREATED,
            "priority" => $this->getPriority(),
        ];
    }
}
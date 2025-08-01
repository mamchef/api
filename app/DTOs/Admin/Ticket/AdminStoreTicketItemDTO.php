<?php

namespace App\DTOs\Admin\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Enums\Ticket\TicketStatusEnum;
use App\Models\Admin;
use App\Models\Chef;
use Illuminate\Http\UploadedFile;

readonly class AdminStoreTicketItemDTO extends BaseDTO
{
    public function __construct(
        protected int $ticketId,
        protected int $adminId,
        protected string $description,
        protected TicketStatusEnum $status,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getAdminId(): int
    {
        return $this->adminId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAttachment(): ?UploadedFile
    {
        return $this->attachment;
    }


    public function getStatus(): TicketStatusEnum
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'ticket_id' => $this->getTicketId(),
            'itemable_id' => $this->getAdminId(),
            'itemable_type' => Admin::class,
            'description' => $this->getDescription(),
            'created_by' => TicketItemCreateByEnum::ADMIN,
        ];
    }

}
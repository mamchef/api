<?php

namespace App\DTOs\Admin\Ticket;

use App\DTOs\BaseDTO;
use App\Enums\Ticket\TicketItemCreateByEnum;
use App\Enums\Ticket\TicketStatusEnum;
use App\Models\Chef;
use Illuminate\Http\UploadedFile;

readonly class AdminStoreTicketDTO extends BaseDTO
{
    public function __construct(
        protected string $title,
        protected string $adminId,
        protected int $targetId,
        protected string $targetClass,
        protected string $description,
        protected TicketStatusEnum $status,
        protected ?UploadedFile $attachment,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAdminId(): int
    {
        return $this->adminId;
    }


    public function getTargetId(): int
    {
        return $this->targetId;
    }


    public function getTargetClass(): string
    {
        return $this->targetClass;
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
            'title' => $this->getTitle(),
            'ticketable_type' => $this->getTargetClass(),
            'ticketable_id' => $this->getTargetId(),
            'status' => $this->getStatus(),
        ];
    }
}
<?php

namespace App\Models;

use App\Enums\Ticket\TicketPriorityEnum;
use App\Enums\Ticket\TicketStatusEnum;
use App\ModelFilters\TicketFilter;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $ticketable_type
 * @property int $ticketable_id
 * @property TicketStatusEnum $status
 * @property string $ticket_number
 * @property TicketPriorityEnum $priority
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 *
 * Relations :
 * @property MorphTo $ticketable
 * @property HasMany | TicketItem[] $items
 */
class Ticket extends Model
{

    use SoftDeletes, Filterable, GetTableColumn;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => TicketStatusEnum::class,
        "priority" => TicketPriorityEnum::class,
    ];

    // ================= Relations ==================== //
    public function ticketable(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(TicketItem::class);
    }

    // ================= SCOPES ==================== //
    public function scopeForChef($query, $chefId)
    {
        return $query->where('ticketable_type', Chef::class)->where('ticketable_id', $chefId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('ticketable_type', User::class)->where('ticketable_id', $userId);
    }


    // ================= QUERIES ==================== //

    public function generateTicketNumber(): string
    {
        return 'TKT-' . now()->format('Ymd') . '-' . str_pad($this->id ?? rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }


    public function getFormattedTicketNumber(): string
    {
        return $this->ticket_number ?? $this->generateTicketNumber();
    }


    // ================= MISC ==================== //

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TEMP-' . uniqid();
            }
        });

        static::created(function ($ticket) {
            if (str_starts_with($ticket->ticket_number, 'TEMP-')) {
                $ticket->update([
                    'ticket_number' => $ticket->generateTicketNumber()
                ]);
            }
        });
    }


    public function getModelFilterClass(): string
    {
        return TicketFilter::class;
    }
}

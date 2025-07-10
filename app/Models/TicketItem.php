<?php

namespace App\Models;

use App\Enums\Ticket\TicketItemCreateByEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $ticket_id
 * @property string $itemable_type
 * @property int $itemable_id
 * @property string $description
 * @property TicketItemCreateByEnum $created_by
 * @property string $attachment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relations:
 * @property  BelongsTo | Ticket $ticket
 * @property MorphTo $itemable
 */
class TicketItem extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'created_by' => TicketItemCreateByEnum::class,
    ];

    // ================= Relations ==================== //
    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // ================= SCOPES ==================== //

    // ================= QUERIES ==================== //

    public function scopeForChef($query, $chefId)
    {
        return $query->where('itemable_type', Chef::class)->where('itemable_id', $chefId);
    }


    // ================= MISC ==================== //
}

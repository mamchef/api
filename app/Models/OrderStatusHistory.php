<?php

namespace App\Models;

use App\Enums\Order\OrderStatusChangeByEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string $new_status
 * @property string $old_status
 * @property OrderStatusChangeByEnum $change_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OrderStatusHistory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'change_by' => OrderStatusChangeByEnum::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

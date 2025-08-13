<?php

namespace App\Models;

use App\Enums\Order\DeliveryTypeEnum;
use App\Enums\Order\OrderCompleteByEnum;
use App\Enums\Order\OrderStatusEnum;
use App\ModelFilters\OrderFilter;
use App\Observers\OrderObserver;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $chef_store_id
 * @property int $user_address
 * @property string $uuid
 * @property string $order_number
 * @property OrderStatusEnum $status
 * @property DeliveryTypeEnum $delivery_type
 * @property DeliveryTypeEnum $original_delivery_type
 * @property float $delivery_cost
 * @property float $subtotal
 * @property float $total_amount
 * @property Carbon $estimated_ready_time
 * @property string $chef_notes
 * @property string $user_notes
 * @property array $delivery_address_snapshot
 * @property string $refused_reason
 * @property Carbon $delivery_change_requested_at
 * @property Carbon $accept_at
 * @property string $delivery_change_reason
 * @property int $rating
 * @property string $rating_review
 * @property Carbon $completed_at
 * @property OrderCompleteByEnum $completed_by
 * @property Carbon $deleted_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 *
 * Relations:
 * @property BelongsTo | User $user
 * @property BelongsTo | ChefStore $chefStore
 * @property HasMany | OrderItem $items
 * @property HasMany | UserTransaction $transactions
 * @property HasMany | OrderStatusHistory $statusHistories
 *
 */

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    use HasFactory, SoftDeletes, GetTableColumn, Filterable;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'delivery_type' => DeliveryTypeEnum::class,
        'original_delivery_type' => DeliveryTypeEnum::class,
        'delivery_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'estimated_ready_time' => 'datetime',
        'delivery_change_requested_at' => 'datetime',
        'accept_at' => 'datetime',
        'completed_at' => 'datetime',
        'completed_by' => OrderCompleteByEnum::class,
        'delivery_address_snapshot' => 'array',
    ];

    // ================= Relations ==================== //

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chefStore(): BelongsTo
    {
        return $this->belongsTo(ChefStore::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(UserTransaction::class);
    }


    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // ================= SCOPES ==================== //
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForChefStore($query, $chefStoreId)
    {
        return $query->where('chef_store_id', $chefStoreId);
    }

    public function scopeByStatus($query, OrderStatusEnum $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatusEnum::PENDING->value);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            OrderStatusEnum::PENDING->value,
            OrderStatusEnum::ACCEPTED->value,
            OrderStatusEnum::DELIVERY_CHANGE_REQUESTED->value,
            OrderStatusEnum::READY_FOR_PICKUP->value,
            OrderStatusEnum::READY_FOR_DELIVERY->value,
        ]);
    }

    // ================= QUERIES ==================== //

    public function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . str_pad($this->id ?? rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function canTransitionTo(OrderStatusEnum $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function isDelivery(): bool
    {
        return $this->delivery_type === DeliveryTypeEnum::DELIVERY;
    }

    public function isPickup(): bool
    {
        return $this->delivery_type === DeliveryTypeEnum::PICKUP;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatusEnum::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === OrderStatusEnum::ACCEPTED;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatusEnum::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return in_array(
            $this->status,
            [OrderStatusEnum::REFUSED_BY_USER, OrderStatusEnum::REFUSED_BY_CHEF, OrderStatusEnum::CANCELLED]
        );
    }

    public function canBeRefunded(): bool
    {
        return in_array($this->status, [
            OrderStatusEnum::REFUSED_BY_USER,
            OrderStatusEnum::REFUSED_BY_CHEF,
            OrderStatusEnum::CANCELLED
        ]);
    }

    public function hasDeliveryChangeRequest(): bool
    {
        return $this->status === OrderStatusEnum::DELIVERY_CHANGE_REQUESTED;
    }

    public function getTotalItemsCount(): int
    {
        return $this->items->sum('quantity');
    }

    public function getFormattedOrderNumber(): string
    {
        return $this->order_number ?? $this->generateOrderNumber();
    }

    public function isPaid(): bool
    {
        return $this->transactions()
            ->where('type', 'order_payment')
            ->where('status', 'completed')
            ->exists();
    }

    public function getPaymentAmount(): float
    {
        return abs(
            $this->transactions()
                ->where('type', 'order_payment')
                ->where('status', 'completed')
                ->sum('amount')
        );
    }

    // ================= MISC ==================== //

    // Boot method for auto-generating order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                // Generate a temporary order number, will be updated after save
                $order->order_number = 'TEMP-' . uniqid();
            }
        });

        static::created(function ($order) {
            if (str_starts_with($order->order_number, 'TEMP-')) {
                $order->update([
                    'order_number' => $order->generateOrderNumber()
                ]);
            }
        });
    }


    public function getModelFilterClass(): string
    {
        return OrderFilter::class;
    }
}
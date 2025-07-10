<?php

namespace App\Models;

use App\Enums\Chef\FoodOption\FoodOptionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id,
 * @property int $order_item_id
 * @property int $food_option_group_id
 * @property int $food_option_id
 * @property string $option_group_name
 * @property string $option_name
 * @property float $option_price
 * @property FoodOptionTypeEnum $option_type
 * @property int $quantity
 * @property float $option_total
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class OrderItemOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'option_type' => FoodOptionTypeEnum::class,
        'option_price' => 'decimal:2',
        'quantity' => 'integer',
        'option_total' => 'decimal:2',
    ];

    // Relationships
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function foodOptionGroup(): BelongsTo
    {
        return $this->belongsTo(FoodOptionGroup::class);
    }

    public function foodOption(): BelongsTo
    {
        return $this->belongsTo(FoodOption::class);
    }

    // Helper Methods
    public function calculateTotal(): float
    {
        return $this->option_price * $this->quantity;
    }

    public function isQuantitative(): bool
    {
        return $this->option_type === FoodOptionTypeEnum::Quantitative;
    }

    public function isQualitative(): bool
    {
        return $this->option_type === FoodOptionTypeEnum::Qualitative;
    }

    public function isFree(): bool
    {
        return $this->option_price == 0;
    }

    public function getFormattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Free';
        }

        return '$' . number_format($this->option_price, 2);
    }

    public function getFormattedTotal(): string
    {
        if ($this->option_total == 0) {
            return 'Free';
        }

        return '$' . number_format($this->option_total, 2);
    }

    public function getDisplayName(): string
    {
        $name = $this->option_name;

        if ($this->isQuantitative() && $this->quantity > 1) {
            $name .= " (x{$this->quantity})";
        }

        return $name;
    }

    // Scopes
    public function scopeForOrderItem($query, $orderItemId)
    {
        return $query->where('order_item_id', $orderItemId);
    }

    public function scopeByOptionGroup($query, $groupId)
    {
        return $query->where('food_option_group_id', $groupId);
    }

    public function scopeQuantitative($query)
    {
        return $query->where('option_type', FoodOptionTypeEnum::Quantitative->value);
    }

    public function scopeQualitative($query)
    {
        return $query->where('option_type', FoodOptionTypeEnum::Qualitative->value);
    }

    public function scopeFree($query)
    {
        return $query->where('option_price', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('option_price', '>', 0);
    }

    // Boot method for auto-calculation
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($option) {
            // Auto-calculate total if not set
            if (!$option->option_total) {
                $option->option_total = $option->calculateTotal();
            }
        });

        static::saved(function ($option) {
            // Recalculate parent order item total
            $option->orderItem->recalculateTotal();
        });

        static::deleted(function ($option) {
            // Recalculate parent order item total
            if ($option->orderItem) {
                $option->orderItem->recalculateTotal();
            }
        });
    }
}
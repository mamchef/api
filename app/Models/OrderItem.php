<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $order_id
 * @property int $food_id
 * @property string $food_name
 * @property float $food_price
 * @property int $quantity
 * @property float $item_subtotal
 * @property float $item_total
 * @property string $note
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 *
 * Relations:
 * @property HasMany | OrderItemOption[] $options
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'food_price' => 'decimal:2',
        'quantity' => 'integer',
        'item_subtotal' => 'decimal:2',
        'item_total' => 'decimal:2',
    ];

    // ================= Relations ==================== //
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class);
    }

    // ================= QUERIES ==================== //
    public function calculateSubtotal(): float
    {
        return $this->food_price * $this->quantity;
    }

    public function calculateTotal(): float
    {
        $optionsTotal = $this->options->sum('option_total');
        return $this->calculateSubtotal() + $optionsTotal;
    }

    public function getOptionsTotal(): float
    {
        return $this->options->sum('option_total');
    }

    public function hasOptions(): bool
    {
        return $this->options->count() > 0;
    }

    public function getOptionsSummary(): array
    {
        return $this->options->map(function ($option) {
            return [
                'group_name' => $option->option_group_name,
                'option_name' => $option->option_name,
                'price' => $option->option_price,
                'quantity' => $option->quantity,
                'total' => $option->option_total,
                'type' => $option->option_type
            ];
        })->toArray();
    }

    // ================= SCOPES ==================== //
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeForFood($query, $foodId)
    {
        return $query->where('food_id', $foodId);
    }

    // ================= MISC ==================== //

    // Boot method for auto-calculation
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            // Auto-calculate subtotal if not set
            if (!$orderItem->item_subtotal) {
                $orderItem->item_subtotal = $orderItem->calculateSubtotal();
            }

            // Auto-calculate total if not set (will be updated after options are saved)
            if (!$orderItem->item_total) {
                $orderItem->item_total = $orderItem->item_subtotal;
            }
        });
    }

    // Method to recalculate totals after options are added/updated
    public function recalculateTotal(): void
    {
        $this->update([
            'item_subtotal' => $this->calculateSubtotal(),
            'item_total' => $this->calculateTotal()
        ]);
    }
}
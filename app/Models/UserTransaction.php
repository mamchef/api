<?php

namespace App\Models;

use App\Enums\User\PaymentMethod;
use App\Enums\User\TransactionStatus;
use App\Enums\User\TransactionType;
use App\ModelFilters\UserTransactionFilter;
use App\Traits\GetTableColumn;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property TransactionType $type
 * @property float $amount
 * @property string $description
 * @property TransactionStatus $status
 * @property PaymentMethod $payment_method
 * @property string $external_transaction_id
 * @property array gateway_response
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @Relations:
 * @property BelongsTo | User $user
 * @property BelongsTo | Order $order
 */
class UserTransaction extends Model
{
    use HasFactory, Filterable, GetTableColumn;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'payment_method' => PaymentMethod::class,
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    // ================= Relations ==================== //

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


    // ================= QUERIES ==================== //
    public function isCredit(): bool
    {
        return $this->type->isCredit() || $this->amount > 0;
    }

    public function isDebit(): bool
    {
        return $this->type->isDebit() || $this->amount < 0;
    }

    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === TransactionStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status == TransactionStatus::FAILED;
    }

    public function getFormattedAmount(): string
    {
        $prefix = $this->isCredit() ? '+' : '';
        return $prefix . '$' . number_format(abs($this->amount), 2);
    }

    public function getAbsoluteAmount(): float
    {
        return abs($this->amount);
    }

    // ================= SCOPES ==================== //
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeByType($query, TransactionType $type)
    {
        return $query->where('type', $type->value);
    }

    public function scopeByStatus($query, TransactionStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::COMPLETED->value);
    }

    public function scopeCredits($query)
    {
        return $query->where('amount', '>', 0);
    }

    public function scopeDebits($query)
    {
        return $query->where('amount', '<', 0);
    }

    public function scopeOrderPayments($query)
    {
        return $query->where('type', TransactionType::ORDER_PAYMENT->value);
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', TransactionType::REFUND->value);
    }

    public function scopeTopups($query)
    {
        return $query->where('type', TransactionType::TOPUP->value);
    }

    public function scopeDeliveryRefunds($query)
    {
        return $query->where('type', TransactionType::DELIVERY_REFUND->value);
    }

    // ================= MISC ==================== //

    // Static Methods for Balance Calculation
    public static function getUserBalance($userId): float
    {
        return static::forUser($userId)
            ->completed()
            ->sum('amount');
    }

    public static function createOrderPayment(
        $userId,
        $orderId,
        $amount,
        $paymentMethod,
        $externalId = null,
        $description = null,
        $gatewayResponse = null
    ): static {
        $order = Order::query()->find($orderId);
        // For external payments, first add credit to wallet
        if ($paymentMethod != PaymentMethod::WALLET) {
            //CHARGE WALLET
            static::create([
                'user_id' => $userId,
                'order_id' => $orderId,
                'type' => TransactionType::CHARGE_WALLET,
                'amount' => abs($amount), // Always positive for charge wallet
                'description' => $description ?? "charge wallet for order #{$order->order_number}",
                'status' => TransactionStatus::COMPLETED,
                'payment_method' => $paymentMethod,
                'external_transaction_id' => $externalId,
                'gateway_response' => $gatewayResponse,
            ]);
        }

        // Then deduct for the order (for both wallet and external payments)
        return static::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => TransactionType::ORDER_PAYMENT,
            'amount' => -abs($amount), // Always negative for payments
            'description' => $description ?? "Payment for order #{$order->order_number}",
            'status' => TransactionStatus::COMPLETED,
            'payment_method' => $paymentMethod,
            'external_transaction_id' => $externalId,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public static function createRefund(
        Order $order,
    ): static {
        $refundedTransactionAmount = UserTransaction::forUser($order->user_id)
            ->forOrder($order->id)
            ->refunds()
            ->completed()
            ->sum('amount');

        if ($refundedTransactionAmount > 0) {
            throw ValidationException::withMessages(
                ['order' => 'Order Already Refunded']
            );
        }

        $payedTransactionAmount = UserTransaction::forUser($order->user_id)
            ->forOrder($order->id)
            ->orderPayments()
            ->completed()
            ->sum('amount');

        $deliveryRefundAmount = UserTransaction::forUser($order->user_id)
            ->forOrder($order->id)
            ->deliveryRefunds()
            ->completed()
            ->sum('amount');

        $remainRefundAmount = abs($payedTransactionAmount) - abs($deliveryRefundAmount);

        if ($remainRefundAmount <= 0) {
            throw ValidationException::withMessages(
                ['order' => 'Order Refund  Amount Is Not Positive']
            );
        }


        return static::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'type' => TransactionType::REFUND,
            'amount' => abs($remainRefundAmount), // Always positive for refunds
            'description' => $description ?? "Refund for order #{$order->order_number}",
            'status' => TransactionStatus::COMPLETED,
        ]);
    }

    public static function createDeliveryRefund($userId, $orderId, $amount, $description = null): static
    {
        $order = Order::query()->find($orderId);
        return static::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => TransactionType::DELIVERY_REFUND,
            'amount' => abs($amount), // Always positive for refunds
            'description' => $description ?? "Delivery cost refund for order #{$order->order_number}",
            'status' => TransactionStatus::COMPLETED,
        ]);
    }

    public static function createTopup(
        $userId,
        $amount,
        $paymentMethod = null,
        $externalId = null,
        $description = null
    ): static {
        return static::create([
            'user_id' => $userId,
            'type' => TransactionType::TOPUP,
            'amount' => abs($amount), // Always positive for topups
            'description' => $description ?? "Wallet top up",
            'status' => TransactionStatus::COMPLETED,
            'payment_method' => $paymentMethod,
            'external_transaction_id' => $externalId,
        ]);
    }


    public function getModelFilterClass(): string
    {
        return UserTransactionFilter::class;
    }
}
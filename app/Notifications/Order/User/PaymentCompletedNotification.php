<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PaymentCompletedNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Payment Successful!',
            'body' => "Your payment for order #{$this->order->order_number} is confirmed",
            'type' => 'payment_completed',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'amount' => $this->order->total_amount,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '💳 Payment Successful!',
                body: "Order #{$this->order->order_number} - Waiting for chef to accept",
            )
        ))
            ->data([
                'type' => $this->notificationType,
                'order_id' => (string)$this->order->uuid,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💳 Payment Successful!')
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Great! Your payment has been processed successfully! 🎉")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("**Amount:** €" . number_format($this->order->total_amount, 2))
            ->line("We're now waiting for the chef to accept your order.")
            ->action('Track Your Order', url("/orders/{$this->order->uuid}"))
            ->line("Get ready for some delicious food! 😋");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
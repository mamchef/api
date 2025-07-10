<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefOrderCompletedNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Order Completed!',
            'body' => "Order #{$this->order->order_number} completed successfully",
            'type' => 'order_completed',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '🎉 Order Completed!',
                body: "Order #{$this->order->order_number} - €{$this->order->total_amount} earned!",
            )
        ))
            ->data([
                'type' => $this->notificationType,
                'order_id' => (string)$this->order->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Order Completed Successfully!')
            ->greeting("Congratulations Chef {$notifiable->first_name}!")
            ->line("You've successfully completed another order! 🏆")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("**Amount Earned:** €" . number_format($this->order->total_amount, 2))
            ->action('View Order Details', url("/orders/{$this->order->id}"))
            ->line('Keep up the excellent work! More hungry customers are waiting! 🍽️');
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
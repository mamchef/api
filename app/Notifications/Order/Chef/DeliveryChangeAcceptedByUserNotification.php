<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DeliveryChangeAcceptedByUserNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Delivery Change Accepted',
            'body' => "Customer accepted delivery change for order #{$this->order->order_number}",
            'type' => 'delivery_accepted',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '✅ Delivery Accepted',
                body: "Order #{$this->order->order_number} - Customer accepted your delivery change",
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
            ->subject('✅ Great News! Delivery Change Accepted')
            ->greeting("Hi Chef {$notifiable->first_name}!")
            ->line("Good news! The customer accepted your delivery change request.")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("You can now proceed with preparing the order! 🍽️")
            ->action('Start Cooking', url("/orders/{$this->order->id}"))
            ->line('Happy cooking! 👨‍🍳');
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
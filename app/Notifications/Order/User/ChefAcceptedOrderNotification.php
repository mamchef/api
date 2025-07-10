<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefAcceptedOrderNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Order Accepted!',
            'body' => "Chef accepted your order #{$this->order->order_number}",
            'type' => 'order_accepted',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '👨‍🍳 Chef Accepted Your Order!',
                body: "Order #{$this->order->order_number} - Your food is being prepared!",
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
            ->subject('👨‍🍳 Your Order is Being Prepared!')
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Awesome! The chef has accepted your order! 🎉")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("Your delicious meal is now being prepared with love! 💕")
            ->action('Track Your Order', url("/orders/{$this->order->uuid}"))
            ->line("Sit back and relax - great food is on the way! 😋");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
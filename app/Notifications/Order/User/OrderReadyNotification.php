<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderReadyNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Order Ready!',
            'body' => "Your order #{$this->order->order_number} is ready for pickup/delivery",
            'type' => 'order_ready',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'delivery_type' => $this->order->delivery_type,
            'action_url' => "/orders?uuid={$this->order->uuid}",
            'sound' => 'order_ready',
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $deliveryText = $this->order->delivery_type === 'pickup' ? 'ready for pickup!' : 'on the way!';

        return (new FcmMessage(
            notification: new FcmNotification(
                title: '🍽️ Your Food is Ready!',
                body: "Order #{$this->order->order_number} is {$deliveryText}",
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
        $deliveryText = $this->order->delivery_type === 'pickup'
            ? 'Your order is ready for pickup!'
            : 'Your order is on the way to you!';

        return (new MailMessage)
            ->subject('🍽️ Your Delicious Food is Ready!')
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("Great news! {$deliveryText} 🎉")
            ->line("**Order:** #{$this->order->order_number}")
            ->when($this->order->delivery_type === 'pickup', function ($mail) {
                return $mail->line("Please come and collect your freshly prepared meal!");
            })
            ->when($this->order->delivery_type === 'delivery', function ($mail) {
                return $mail->line("Your food will arrive shortly - get ready to enjoy!");
            })
            ->action('View Order Details', url("/orders/{$this->order->uuid}"))
            ->line("Enjoy your delicious meal! 😋");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
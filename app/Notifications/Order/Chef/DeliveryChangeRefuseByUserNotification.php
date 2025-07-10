<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DeliveryChangeRefuseByUserNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Delivery Change Refused',
            'body' => "Customer Refused delivery change for order #{$this->order->order_number}",
            'type' => 'order_canceled',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '❌ Order Canceled',
                body: "Order #{$this->order->order_number} - Customer refused delivery change",
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
            ->subject('🚫 Order Canceled')
            ->greeting("Hi Chef {$notifiable->first_name},")
            ->line("Order #{$this->order->order_number} has been canceled.")
            ->line("The customer refused the delivery change and canceled their order.")
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line("Don't worry, more orders are coming! 💪");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
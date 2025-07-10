<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DeliveryChangeRequestNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order,
        protected string $changeReason = ''
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Delivery Change Request',
            'body' => "Chef requests delivery change for order #{$this->order->order_number}",
            'type' => 'delivery_change_request',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'change_reason' => $this->changeReason,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '🚚 Delivery Change Request',
                body: "Chef requests delivery change for order #{$this->order->order_number}",
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
            ->subject('🚚 Delivery Change Request')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("The chef has requested a change to your delivery for order #{$this->order->order_number}.")
            ->when($this->changeReason, function ($mail) {
                return $mail->line("**Reason:** {$this->changeReason}");
            })
            ->line("Please review and respond to this request.")
            ->action('Review Request', url("/orders/{$this->order->uuid}"))
            ->line("We appreciate your understanding! 🙏");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefRefusedOrderNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order,
        protected string $refusalReason = ''
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Order Declined',
            'body' => "Chef declined your order #{$this->order->order_number}",
            'type' => 'order_refused',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'refusal_reason' => $this->refusalReason,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '😔 Order Declined',
                body: "Order #{$this->order->order_number} was declined - Your refund is being processed",
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
            ->subject('😔 Order Declined - Refund Processing')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("We're sorry, but the chef had to decline your order #{$this->order->order_number}.")
            ->when($this->refusalReason, function ($mail) {
                return $mail->line("**Reason:** {$this->refusalReason}");
            })
            ->line("Don't worry! Your refund is being processed and will be back in your account soon.")
            ->action('Browse Other Chefs', url("/"))
            ->line("Try another chef - there are many delicious options waiting for you! 🍽️");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
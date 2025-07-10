<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class UserOrderCompletedNotification extends BaseNotification
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
            'body' => "Hope you enjoyed your meal! Order #{$this->order->order_number}",
            'type' => 'order_completed_user',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'action_url' => "/orders?uuid={$this->order->uuid}&=review=1",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '🎉 Order Completed!',
                body: "Hope you enjoyed your meal! Leave a review? ⭐",
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
            ->subject('🎉 Thanks for Your Order!')
            ->greeting("Hi {$notifiable->first_name}!")
            ->line("We hope you absolutely loved your meal! 😋")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("Your experience matters to us and helps other food lovers discover great chefs!")
            ->action('Leave a Review ⭐', url("/orders/{$this->order->uuid}/review"))
            ->line("Thank you for choosing us - we can't wait to serve you again! 🍽️💕");
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
<?php

namespace App\Notifications\Order\Chef;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class NewOrderNotification extends BaseNotification
{
    protected string $notificationType = 'order_updates';

    public function __construct(
        protected Order $order
    ) {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Order Received!',
            'body' => "New order #{$this->order->order_number}",
            'type' => 'new_order',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'delivery_type' => $this->order->delivery_type,
            'action_url' => "/orders/{$this->order->id}",
            'sound' => 'new_order', // Special sound for chef
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: '🆕 New Order!',
                body: "Order #{$this->order->order_number} - €{$this->order->total_amount}",

            )
        ))
            ->data([
                'type' => $this->notificationType,
                'order_id' => (string)$this->order->id,
                'click_action' => '/orders/active',
            ])
            ->custom([
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'new_order',
                        'click_action' => 'NEW_ORDER',
                        'channel_id' => 'new_order_channel',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                            'alert' => [
                                'title' => '🆕 New Order!',
                                'body' => "Order #{$this->order->order_number}",
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🍽️ New Order Received!')
            ->greeting("Hi Chef {$notifiable->first_name}!")
            ->line("You've got a new order to prepare! 🎉")
            ->line("**Order:** #{$this->order->order_number}")
            ->line("**Amount:** €" . number_format($this->order->total_amount, 2))
            ->line("**Delivery:** " . ucfirst($this->order->delivery_type->value))
            ->action('View Order Details', env('CHEF_PANEL_URL')."/orders/{$this->order->id}")
            ->line('Time to cook something delicious! 👨‍🍳');
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
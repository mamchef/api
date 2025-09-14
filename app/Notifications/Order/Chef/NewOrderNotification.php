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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $subject = $isLithuanian
            ? "🍽️ Gautas naujas užsakymas!"
            : "🍽️ New Order Received!";

        $headerTitle = $isLithuanian
            ? 'Gautas naujas užsakymas!'
            : 'New Order Received!';

        $greeting = $isLithuanian
            ? "Sveiki šefai {$notifiable->first_name}!"
            : "Hi Chef {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Turite naują užsakymą paruošti! 🎉<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br>
               <strong>Suma:</strong> €" . number_format($this->order->total_amount, 2) . "<br>
               <strong>Pristatymas:</strong> " . ucfirst($this->order->delivery_type->value)
            : "You've got a new order to prepare! 🎉<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br>
               <strong>Amount:</strong> €" . number_format($this->order->total_amount, 2) . "<br>
               <strong>Delivery:</strong> " . ucfirst($this->order->delivery_type->value);

        $highlightMessage = $isLithuanian
            ? 'Laikas gaminti kažką skanaus! 👨‍🍳<br><br>Su pagarba,<br>MamChef komanda'
            : 'Time to cook something delicious! 👨‍🍳<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Peržiūrėti užsakymą' : 'View Order Details';

        $footer = $this->mailFooter($notifiable->lang);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'body' => $body,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'success',
                'button_text' => $buttonText,
                'button_url' => config('app.chef_panel', 'https://chef.mamchef.com') . "/orders/{$this->order->id}",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
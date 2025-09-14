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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Užsakymas priimtas!'
            : 'Order Accepted!';

        $body = $isLithuanian
            ? "Šefas priėmė jūsų užsakymą #{$this->order->order_number}"
            : "Chef accepted your order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'order_accepted',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '👨‍🍳 Šefas priėmė jūsų užsakymą!'
            : '👨‍🍳 Chef Accepted Your Order!';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} - Jūsų maistas ruošiamas!"
            : "Order #{$this->order->order_number} - Your food is being prepared!";

        return (new FcmMessage(
            notification: new FcmNotification(
                title: $title,
                body: $body,
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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $subject = $isLithuanian
            ? "👨‍🍳 Jūsų užsakymas ruošiamas!"
            : "👨‍🍳 Your Order is Being Prepared!";

        $headerTitle = $isLithuanian
            ? 'Užsakymas priimtas!'
            : 'Order Accepted!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name}!"
            : "Hi {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Puiku! Šefas priėmė jūsų užsakymą! 🎉<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br><br>
               Jūsų skanus patiekalas dabar ruošiamas su meile! 💕"
            : "Awesome! The chef has accepted your order! 🎉<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br><br>
               Your delicious meal is now being prepared with love! 💕";

        $highlightMessage = $isLithuanian
            ? 'Atsisėskite ir atsipalaiduokite - puikus maistas jau kelyje! 😋<br><br>Su pagarba,<br>MamChef komanda'
            : 'Sit back and relax - great food is on the way! 😋<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Sekti užsakymą' : 'Track Your Order';

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
                'button_url' => config('app.user_panel', 'https://app.mamchef.com') . "/orders",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Užsakymas paruoštas!'
            : 'Order Ready!';

        $body = $isLithuanian
            ? "Jūsų užsakymas #{$this->order->order_number} paruoštas pasiimti/pristatymui"
            : "Your order #{$this->order->order_number} is ready for pickup/delivery";

        return [
            'title' => $title,
            'body' => $body,
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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🍽️ Jūsų maistas paruoštas!'
            : '🍽️ Your Food is Ready!';

        $deliveryText = $this->order->delivery_type === 'pickup'
            ? ($isLithuanian ? 'paruoštas pasiimti!' : 'ready for pickup!')
            : ($isLithuanian ? 'kelyje pas jus!' : 'on the way!');

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} {$deliveryText}"
            : "Order #{$this->order->order_number} is {$deliveryText}";

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
            ? "🍽️ Jūsų skanus maistas paruoštas!"
            : "🍽️ Your Delicious Food is Ready!";

        $headerTitle = $isLithuanian
            ? 'Maistas paruoštas!'
            : 'Food is Ready!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name}!"
            : "Hi {$notifiable->first_name}!";

        $deliveryText = $this->order->delivery_type === 'pickup'
            ? ($isLithuanian ? 'Jūsų užsakymas paruoštas pasiimti!' : 'Your order is ready for pickup!')
            : ($isLithuanian ? 'Jūsų užsakymas kelyje pas jus!' : 'Your order is on the way to you!');

        $additionalInfo = $this->order->delivery_type === 'pickup'
            ? ($isLithuanian ? 'Ateikite ir pasiimkite savo šviežiai paruoštą patiekalą!' : 'Please come and collect your freshly prepared meal!')
            : ($isLithuanian ? 'Jūsų maistas netrukus atvyks - pasiruoškite mėgautis!' : 'Your food will arrive shortly - get ready to enjoy!');

        $body = $isLithuanian
            ? "Puikios žinios! {$deliveryText} 🎉<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br><br>
               {$additionalInfo}"
            : "Great news! {$deliveryText} 🎉<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br><br>
               {$additionalInfo}";

        $highlightMessage = $isLithuanian
            ? 'Mėgaukites skaniais patiekalais! 😋<br><br>Su pagarba,<br>MamChef komanda'
            : 'Enjoy your delicious meal! 😋<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Peržiūrėti užsakymo detales' : 'View Order Details';

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
                'button_url' =>  config('app.user_panel', 'https://app.mamchef.com') . "/orders",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
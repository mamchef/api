<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DeliveryChangeAcceptedByUserNotification extends BaseNotification
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
            ? 'Pristatymo keitimas priimtas'
            : 'Delivery Change Accepted';

        $body = $isLithuanian
            ? "Klientas priėmė pristatymo keitimą užsakymui #{$this->order->order_number}"
            : "Customer accepted delivery change for order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'delivery_accepted',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '✅ Pristatymas priimtas'
            : '✅ Delivery Accepted';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} - Klientas priėmė jūsų pristatymo keitimą"
            : "Order #{$this->order->order_number} - Customer accepted your delivery change";

        return (new FcmMessage(
            notification: new FcmNotification(
                title: $title,
                body: $body,
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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $subject = $isLithuanian
            ? "✅ Puikios žinios! Pristatymo keitimas priimtas"
            : "✅ Great News! Delivery Change Accepted";

        $headerTitle = $isLithuanian
            ? 'Pristatymo keitimas priimtas'
            : 'Delivery Change Accepted';

        $greeting = $isLithuanian
            ? "Sveiki šefe {$notifiable->first_name}!"
            : "Hi Chef {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Puikios žinios! Klientas priėmė jūsų pristatymo keitimo prašymą užsakymui #{$this->order->order_number}."
            : "Good news! The customer accepted your delivery change request for order #{$this->order->order_number}.";

        $highlightMessage = $isLithuanian
            ? 'Dabar galite tęsti užsakymo ruošimą! 🍽️<br><br>Smagaus gaminiē! 👨‍🍳<br><br>Su pagarba,<br>MamChef komanda'
            : 'You can now proceed with preparing the order! 🍽️<br><br>Happy cooking! 👨‍🍳<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Pradėti gaminti' : 'Start Cooking';

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
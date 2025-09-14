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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Pristatymo keitimo prašymas'
            : 'Delivery Change Request';

        $body = $isLithuanian
            ? "Šefas prašo pakeisti pristatymą užsakymui #{$this->order->order_number}"
            : "Chef requests delivery change for order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'delivery_change_request',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'change_reason' => $this->changeReason,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🚚 Pristatymo keitimo prašymas'
            : '🚚 Delivery Change Request';

        $body = $isLithuanian
            ? "Šefas prašo pakeisti pristatymą užsakymui #{$this->order->order_number}"
            : "Chef requests delivery change for order #{$this->order->order_number}";

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

        $reasonText = '';
        if ($this->changeReason) {
            $reasonText = $isLithuanian
                ? "<br><strong>Priežastis:</strong> {$this->changeReason}"
                : "<br><strong>Reason:</strong> {$this->changeReason}";
        }

        $subject = $isLithuanian
            ? "🚚 Pristatymo keitimo prašymas"
            : "🚚 Delivery Change Request";

        $headerTitle = $isLithuanian
            ? 'Pristatymo keitimo prašymas'
            : 'Delivery Change Request';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name},"
            : "Hi {$notifiable->first_name},";

        $body = $isLithuanian
            ? "Šefas prašė pakeisti jūsų pristatymą užsakymui #{$this->order->order_number}.{$reasonText}<br><br>
               Prašome peržiūrėti ir atsakyti į šį prašymą."
            : "The chef has requested a change to your delivery for order #{$this->order->order_number}.{$reasonText}<br><br>
               Please review and respond to this request.";

        $highlightMessage = $isLithuanian
            ? 'Dėkojame už supratimą! 🙏<br><br>Su pagarba,<br>MamChef komanda'
            : 'We appreciate your understanding! 🙏<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Peržiūrėti prašymą' : 'Review Request';

        $footer = $this->mailFooter($notifiable->lang);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'body' => $body,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'info',
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
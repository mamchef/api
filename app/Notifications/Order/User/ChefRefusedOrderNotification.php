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
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Užsakymas atmestas'
            : 'Order Declined';

        $body = $isLithuanian
            ? "Šefas atmetė jūsų užsakymą #{$this->order->order_number}"
            : "Chef declined your order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'order_refused',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'refusal_reason' => $this->refusalReason,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '😔 Užsakymas atmestas'
            : '😔 Order Declined';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} atmestas - Jūsų pinigai grąžinami"
            : "Order #{$this->order->order_number} was declined - Your refund is being processed";

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
        if ($this->refusalReason) {
            $reasonText = $isLithuanian
                ? "<br><strong>Priežastis:</strong> {$this->refusalReason}"
                : "<br><strong>Reason:</strong> {$this->refusalReason}";
        }

        $subject = $isLithuanian
            ? "😔 Užsakymas atmestas - Pinigai grąžinami"
            : "😔 Order Declined - Refund Processing";

        $headerTitle = $isLithuanian
            ? 'Užsakymas atmestas'
            : 'Order Declined';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name},"
            : "Hi {$notifiable->first_name},";

        $body = $isLithuanian
            ? "Atsiprašome, bet šefas turėjo atmesti jūsų užsakymą #{$this->order->order_number}.{$reasonText}<br><br>
               Nesijaudinkite! Jūsų pinigai grąžinami ir greitai atsiras jūsų sąskaitoje."
            : "We're sorry, but the chef had to decline your order #{$this->order->order_number}.{$reasonText}<br><br>
               Don't worry! Your refund is being processed and will be back in your account soon.";

        $highlightMessage = $isLithuanian
            ? 'Išbandykite kitą šefą - yra daug skanūs variantų, kurie jūsų laukia! 🍽️<br><br>Su pagarba,<br>MamChef komanda'
            : 'Try another chef - there are many delicious options waiting for you! 🍽️<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Naršyti kitus šefus' : 'Browse Other Chefs';

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
                'button_url' => config('app.user_panel', 'https://app.mamchef.com'),
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
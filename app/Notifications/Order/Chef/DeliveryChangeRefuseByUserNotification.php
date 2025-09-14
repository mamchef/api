<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class DeliveryChangeRefuseByUserNotification extends BaseNotification
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
            ? 'Pristatymo keitimas atmestas'
            : 'Delivery Change Refused';

        $body = $isLithuanian
            ? "Klientas atmetė pristatymo keitimą užsakymui #{$this->order->order_number}"
            : "Customer refused delivery change for order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'order_canceled',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '❌ Užsakymas atšauktas'
            : '❌ Order Canceled';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} - Klientas atmetė pristatymo keitimą"
            : "Order #{$this->order->order_number} - Customer refused delivery change";

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
            ? "🚫 Užsakymas atšauktas"
            : "🚫 Order Canceled";

        $headerTitle = $isLithuanian
            ? 'Užsakymas atšauktas'
            : 'Order Canceled';

        $greeting = $isLithuanian
            ? "Sveiki šefe {$notifiable->first_name},"
            : "Hi Chef {$notifiable->first_name},";

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} buvo atšauktas. Klientas atmetė pristatymo keitimą ir atšaukė savo užsakymą."
            : "Order #{$this->order->order_number} has been canceled. The customer refused the delivery change and canceled their order.";

        $highlightMessage = $isLithuanian
            ? 'Nesijaudinkite, daugiau užsakymų jau kelyje! 💪<br><br>Su pagarba,<br>MamChef komanda'
            : "Don't worry, more orders are coming! 💪<br><br>Best regards,<br>The MamChef Team";

        $buttonText = $isLithuanian ? 'Peržiūrėti užsakymą' : 'View Order';

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
                'button_url' => config('app.chef_panel', 'https://chef.mamchef.com') . "/orders/{$this->order->id}",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
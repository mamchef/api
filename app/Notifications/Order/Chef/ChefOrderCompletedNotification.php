<?php

namespace App\Notifications\Order\Chef;
use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefOrderCompletedNotification extends BaseNotification
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
            ? 'Užsakymas baigtas!'
            : 'Order Completed!';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} sėkmingai baigtas"
            : "Order #{$this->order->order_number} completed successfully";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'order_completed',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'action_url' => "/orders/{$this->order->id}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🎉 Užsakymas baigtas!'
            : '🎉 Order Completed!';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} - €{$this->order->total_amount} uždirbta!"
            : "Order #{$this->order->order_number} - €{$this->order->total_amount} earned!";

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
            ? "🎉 Užsakymas sėkmingai baigtas!"
            : "🎉 Order Completed Successfully!";

        $headerTitle = $isLithuanian
            ? 'Užsakymas baigtas!'
            : 'Order Completed!';

        $greeting = $isLithuanian
            ? "Sveikiname šefe {$notifiable->first_name}!"
            : "Congratulations Chef {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Jūs sėkmingai baigėte dar vieną užsakymą! 🏆<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br>
               <strong>Uždirbta suma:</strong> €" . number_format($this->order->total_amount, 2)
            : "You've successfully completed another order! 🏆<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br>
               <strong>Amount Earned:</strong> €" . number_format($this->order->total_amount, 2);

        $highlightMessage = $isLithuanian
            ? 'Tęskite puikų darbą! Daugiau alkų klientų laukia! 🍽️<br><br>Su pagarba,<br>MamChef komanda'
            : 'Keep up the excellent work! More hungry customers are waiting! 🍽️<br><br>Best regards,<br>The MamChef Team';

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
                'button_url' => config('app.chef_panel', 'https://chef.mamchef.com') . "/orders/{$this->order->id}",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
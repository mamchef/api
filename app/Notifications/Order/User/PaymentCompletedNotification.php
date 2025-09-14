<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class PaymentCompletedNotification extends BaseNotification
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
            ? 'Mokėjimas sėkmingas!'
            : 'Payment Successful!';

        $body = $isLithuanian
            ? "Jūsų mokėjimas už užsakymą #{$this->order->order_number} patvirtintas"
            : "Your payment for order #{$this->order->order_number} is confirmed";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'payment_completed',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'amount' => $this->order->total_amount,
            'action_url' => "/orders?uuid={$this->order->uuid}",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '💳 Mokėjimas sėkmingas!'
            : '💳 Payment Successful!';

        $body = $isLithuanian
            ? "Užsakymas #{$this->order->order_number} - Laukiame šefo patvirtinimo"
            : "Order #{$this->order->order_number} - Waiting for chef to accept";

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
            ? "💳 Mokėjimas sėkmingas!"
            : "💳 Payment Successful!";

        $headerTitle = $isLithuanian
            ? 'Mokėjimas sėkmingas!'
            : 'Payment Successful!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name}!"
            : "Hi {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Puiku! Jūsų mokėjimas sėkmingai apdorotas! 🎉<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br>
               <strong>Suma:</strong> €" . number_format($this->order->total_amount, 2) . "<br><br>
               Dabar laukiame, kol šefas patvirtins jūsų užsakymą."
            : "Great! Your payment has been processed successfully! 🎉<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br>
               <strong>Amount:</strong> €" . number_format($this->order->total_amount, 2) . "<br><br>
               We're now waiting for the chef to accept your order.";

        $highlightMessage = $isLithuanian
            ? 'Pasiruoškite skaniam maistui! 😋<br><br>Su pagarba,<br>MamChef komanda'
            : 'Get ready for some delicious food! 😋<br><br>Best regards,<br>The MamChef Team';

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
                'button_url' =>  config('app.user_panel', 'https://app.mamchef.com') . "/orders",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
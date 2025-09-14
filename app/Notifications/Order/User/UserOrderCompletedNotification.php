<?php

namespace App\Notifications\Order\User;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class UserOrderCompletedNotification extends BaseNotification
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
            ? "Tikimsime, kad mėgavotės maistu! Užsakymas #{$this->order->order_number}"
            : "Hope you enjoyed your meal! Order #{$this->order->order_number}";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'order_completed_user',
            'order_number' => $this->order->order_number,
            'order_id' => $this->order->uuid,
            'action_url' => "/orders?uuid={$this->order->uuid}&=review=1",
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🎉 Užsakymas baigtas!'
            : '🎉 Order Completed!';

        $body = $isLithuanian
            ? "Tikimsime, kad mėgavotės maistu! Palikti atsiliepimą? ⭐"
            : "Hope you enjoyed your meal! Leave a review? ⭐";

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
            ? "🎉 Ačiū už jūsų užsakymą!"
            : "🎉 Thanks for Your Order!";

        $headerTitle = $isLithuanian
            ? 'Užsakymas baigtas!'
            : 'Order Completed!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name}!"
            : "Hi {$notifiable->first_name}!";

        $body = $isLithuanian
            ? "Tikimsime, kad tikrai mėgavotės savo maistu! 😋<br><br>
               <strong>Užsakymas:</strong> #{$this->order->order_number}<br><br>
               Jūsų patirtis mums svarbi ir padės kitiems maisto mėgėjams atrasti puikius šefus!"
            : "We hope you absolutely loved your meal! 😋<br><br>
               <strong>Order:</strong> #{$this->order->order_number}<br><br>
               Your experience matters to us and helps other food lovers discover great chefs!";

        $highlightMessage = $isLithuanian
            ? 'Ačiū, kad pasirinkote mus - nekantraujame aptarnauti jus vėl! 🍽️💕<br><br>Su pagarba,<br>MamChef komanda'
            : "Thank you for choosing us - we can't wait to serve you again! 🍽️💕<br><br>Best regards,<br>The MamChef Team";

        $buttonText = $isLithuanian ? 'Palikti atsiliepimą ⭐' : 'Leave a Review ⭐';

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
                'button_url' =>  config('app.user_panel', 'https://app.mamchef.com'). "/orders",
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
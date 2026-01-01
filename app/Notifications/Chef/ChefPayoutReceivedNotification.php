<?php

namespace App\Notifications\Chef;

use App\Models\Order;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefPayoutReceivedNotification extends BaseNotification
{
    protected string $notificationType = 'chef_payout_received';

    public function __construct(
        protected Order $order,
        protected float $amount
    ) {}

    private function getTranslations($notifiable): array
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';
        $formattedAmount = number_format($this->amount, 2) . ' EUR';

        return [
            'title' => $isLithuanian
                ? 'Išmokėjimas gautas'
                : 'Payout Received',
            'body' => $isLithuanian
                ? "Gavote {$formattedAmount} išmokėjimą už užsakymą #{$this->order->order_number}."
                : "You received a payout of {$formattedAmount} for order #{$this->order->order_number}.",
            'email_subject' => $isLithuanian
                ? "Išmokėjimas gautas - {$formattedAmount}"
                : "Payout Received - {$formattedAmount}",
            'email_header' => $isLithuanian
                ? 'Išmokėjimas gautas!'
                : 'Payout Received!',
            'email_greeting' => $isLithuanian
                ? "Sveiki {$notifiable->first_name}!"
                : "Hello {$notifiable->first_name}!",
            'email_body' => $isLithuanian
                ? "Džiaugiamės pranešdami, kad gavote išmokėjimą už savo užsakymą.<br><br>
                   <strong>Išmokėjimo informacija:</strong><br>
                   • Užsakymo numeris: #{$this->order->order_number}<br>
                   • Suma: {$formattedAmount}<br>
                   • Data: " . now()->format('Y-m-d H:i') . "<br><br>
                   Lėšos bus pervestos į jūsų Stripe paskyrą per 1-2 darbo dienas."
                : "We're pleased to inform you that you have received a payout for your order.<br><br>
                   <strong>Payout Details:</strong><br>
                   • Order Number: #{$this->order->order_number}<br>
                   • Amount: {$formattedAmount}<br>
                   • Date: " . now()->format('Y-m-d H:i') . "<br><br>
                   The funds will be transferred to your Stripe account within 1-2 business days.",
            'email_highlight' => $isLithuanian
                ? "Ačiū, kad esate MamChef dalis!<br><br>Geriausi linkėjimai,<br>MamChef komanda"
                : "Thank you for being part of MamChef!<br><br>Best regards,<br>The MamChef Team",
            'email_button' => $isLithuanian
                ? 'Peržiūrėti užsakymus'
                : 'View Orders',
        ];
    }

    public function toArray($notifiable): array
    {
        $trans = $this->getTranslations($notifiable);

        return [
            'title' => $trans['title'],
            'body' => $trans['body'],
            'type' => $this->notificationType,
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->amount,
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $trans = $this->getTranslations($notifiable);

        return (new FcmMessage(
            notification: new FcmNotification(
                title: $trans['title'],
                body: $trans['body'],
            )
        ))
            ->data([
                'type' => $this->notificationType,
                'order_id' => (string)$this->order->id,
                'order_number' => $this->order->order_number,
                'amount' => (string)$this->amount,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toMail($notifiable): MailMessage
    {
        $trans = $this->getTranslations($notifiable);
        $footer = $this->mailFooter($notifiable->lang ?? 'en');

        return (new MailMessage)
            ->subject($trans['email_subject'])
            ->view('emails.template', [
                'header_title' => $trans['email_header'],
                'greeting' => $trans['email_greeting'],
                'body' => $trans['email_body'],
                'highlight_message' => $trans['email_highlight'],
                'highlight_type' => 'success',
                'button_text' => $trans['email_button'],
                'button_url' => config('app.chef_panel') . '/orders',
                'footer' => $footer,
            ]);
    }
}

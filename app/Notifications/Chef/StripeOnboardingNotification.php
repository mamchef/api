<?php

namespace App\Notifications\Chef;

use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class StripeOnboardingNotification extends BaseNotification
{
    protected string $notificationType = 'payment_setup';

    public function __construct(
        protected string $onboardingUrl
    ) {
    }

    public function toArray($notifiable): array
    {
        $isLithuanian = $notifiable->lang === 'lt';
        
        return [
            'title' => $isLithuanian ? 'Užbaikite mokėjimų nustatymą' : 'Complete Payment Setup',
            'body' => $isLithuanian 
                ? 'Norėdami pradėti gauti užsakymus, užbaikite Stripe patvirtinimą'
                : 'Complete your Stripe verification to start receiving orders',
            'type' => 'stripe_onboarding',
            'onboarding_url' => $this->onboardingUrl,
            'action_url' => $this->onboardingUrl,
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = $notifiable->lang === 'lt';
        
        return (new FcmMessage(
            notification: new FcmNotification(
                title: $isLithuanian ? '💳 Mokėjimų nustatymas' : '💳 Payment Setup Required',
                body: $isLithuanian 
                    ? 'Užbaikite mokėjimų nustatymą ir pradėkite gauti užsakymus!'
                    : 'Complete your payment setup to start receiving orders!',
            )
        ))
            ->data([
                'type' => $this->notificationType,
                'onboarding_url' => $this->onboardingUrl,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }

    public function toMail($notifiable): MailMessage
    {
        $isLithuanian = $notifiable->lang === 'lt';

        $subject = $isLithuanian
            ? "💳 Užbaikite mokėjimų nustatymą - Mamchef"
            : "💳 Complete Payment Setup - Mamchef";

        $headerTitle = $isLithuanian
            ? 'Mokėjimų nustatymas'
            : 'Payment Setup Required';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->name}!"
            : "Hello {$notifiable->name}!";

        $message = $isLithuanian
            ? "🎉 Jūsų dokumentai patvirtinti!<br><br>
               Dabar reikia užbaigti mokėjimų nustatymą, kad galėtumėte pradėti gauti užsakymus.<br><br>
               <strong>Kodėl reikia Stripe patvirtinimo?</strong><br>
               • Saugūs mokėjimai tiesiai į jūsų banko sąskaitą<br>
               • Automatiniai pervedimai kas savaitę<br>
               • Pilna mokėjimų kontrolė"
            : "🎉 Your documents have been approved!<br><br>
               Now you need to complete payment setup to start receiving orders.<br><br>
               <strong>Why do you need Stripe verification?</strong><br>
               • Secure payments directly to your bank account<br>
               • Automatic weekly transfers<br>
               • Full control over your payments";

        $highlightMessage = $isLithuanian
            ? '⏱️ Šis procesas užtruks tik kelias minutes.<br><br>📞 Jei turite klausimų, susisiekite su mumis.<br><br>Su pagarba,<br>MamChef komanda'
            : '⏱️ This process only takes a few minutes.<br><br>📞 Contact us if you have any questions.<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? '🔗 Užbaigti mokėjimų nustatymą' : '🔗 Complete Payment Setup';

        $footer = $this->mailFooter($notifiable->lang);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'message' => $message,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'info',
                'button_text' => $buttonText,
                'button_url' => $this->onboardingUrl,
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable)
    {
       return[];
    }
}
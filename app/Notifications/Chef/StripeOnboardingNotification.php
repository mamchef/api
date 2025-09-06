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
        protected string $onboardingUrl,
        protected string $lang = 'en'
    ) {
    }

    public function toArray($notifiable): array
    {
        $isLithuanian = $this->lang === 'lt';
        
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
        $isLithuanian = $this->lang === 'lt';
        
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
        $isLithuanian = $this->lang === 'lt';
        
        if ($isLithuanian) {
            return (new MailMessage)
                ->subject('💳 Užbaikite mokėjimų nustatymą - Mamchef')
                ->greeting('Sveiki, ' . $notifiable->name . '!')
                ->line('🎉 Jūsų dokumentai patvirtinti!')
                ->line('Dabar reikia užbaigti mokėjimų nustatymą, kad galėtumėte pradėti gauti užsakymus.')
                ->line('**Kodėl reikia Stripe patvirtinimo?**')
                ->line('• Saugūs mokėjimai tiesiai į jūsų banko sąskaitą')
                ->line('• Automatiniai pervedimai kas savaitę')
                ->line('• Pilna mokėjimų kontrolė')
                ->action('🔗 Užbaigti mokėjimų nustatymą', $this->onboardingUrl)
                ->line('⏱️ Šis procesas užtruks tik kelias minutes.')
                ->line('📞 Jei turite klausimų, susisiekite su mumis.')
                ->salutation('Mamchef komanda');
        } else {
            return (new MailMessage)
                ->subject('💳 Complete Payment Setup - Mamchef')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('🎉 Your documents have been approved!')
                ->line('Now you need to complete payment setup to start receiving orders.')
                ->line('**Why do you need Stripe verification?**')
                ->line('• Secure payments directly to your bank account')
                ->line('• Automatic weekly transfers')
                ->line('• Full control over your payments')
                ->action('🔗 Complete Payment Setup', $this->onboardingUrl)
                ->line('⏱️ This process only takes a few minutes.')
                ->line('📞 Contact us if you have any questions.')
                ->salutation('Mamchef Team');
        }
    }

    public function toDatabase($notifiable)
    {
       return[];
    }
}
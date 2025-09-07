<?php

namespace App\Notifications\Chef;

use App\Models\Chef;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Chef Approved Notification
 * 
 * Sent when admin approves a chef's account
 */
class ChefApprovedNotification extends BaseNotification
{
    public function __construct(
        protected Chef $chef
    ) {
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $lang = $this->chef->lang ?? 'en';
        
        if ($lang === 'lt') {
            return $this->buildLithuanianEmail($notifiable);
        }
        
        return $this->buildEnglishEmail($notifiable);
    }

    /**
     * Build English email
     */
    private function buildEnglishEmail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Congratulations! Your Chef Account has been Approved - MamChef')
            ->greeting('Hello ' . $this->chef->first_name . '!')
            ->line('Congratulations! We are excited to inform you that your chef account has been successfully approved.')
            ->line('You can now start creating your menu, managing orders, and serving delicious food to customers in your area.')
            ->line('Here are your next steps:')
            ->line('• Complete your kitchen profile and menu setup')
            ->line('• Upload appealing photos of your dishes')
            ->line('• Set your availability and delivery preferences')
            ->line('• Start receiving and managing orders')
            ->action('Access Your Kitchen Dashboard', config('app.frontend_url') . '/kitchen')
            ->line('Welcome to the MamChef family! We look forward to seeing your culinary creations.')
            ->salutation('Best regards, The MamChef Team');
    }

    /**
     * Build Lithuanian email
     */
    private function buildLithuanianEmail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sveikiname! Jūsų virėjo paskyra patvirtinta - MamChef')
            ->greeting('Sveiki, ' . $this->chef->first_name . '!')
            ->line('Sveikiname! Džiugiai pranešame, kad jūsų virėjo paskyra sėkmingai patvirtinta.')
            ->line('Dabar galite pradėti kurti savo meniu, valdyti užsakymus ir patiekti skanius patiekalus klientams savo rajone.')
            ->line('Štai jūsų kiti žingsniai:')
            ->line('• Užbaikite savo virtuvės profilį ir meniu nustatymą')
            ->line('• Įkelkite patrauklias patiekalų nuotraukas')
            ->line('• Nustatykite savo darbo laiką ir pristatymo nuostatas')
            ->line('• Pradėkite gauti ir valdyti užsakymus')
            ->action('Patekti į virtuvės valdymo panelį', config('app.frontend_url') . '/kitchen')
            ->line('Sveiki atvykę į MamChef šeimą! Laukiame pamatyti jūsų kulinarijos kūrinius.')
            ->salutation('Geriausi linkėjimai, MamChef komanda');
    }

    /**
     * Get FCM data for push notification
     */
    public function getFcmData(): array
    {
        $lang = $this->chef->lang ?? 'en';
        
        if ($lang === 'lt') {
            return [
                'title' => 'Paskyra patvirtinta!',
                'body' => 'Sveikiname! Jūsų virėjo paskyra sėkmingai patvirtinta.',
                'type' => 'chef_approved',
                'chef_id' => $this->chef->id,
                'action_url' => config('app.frontend_url') . '/kitchen'
            ];
        }

        return [
            'title' => 'Account Approved!',
            'body' => 'Congratulations! Your chef account has been successfully approved.',
            'type' => 'chef_approved',
            'chef_id' => $this->chef->id,
            'action_url' => config('app.frontend_url') . '/kitchen'
        ];
    }

    /**
     * Get database notification data
     */
    public function toDatabase($notifiable): array
    {
        $lang = $this->chef->lang ?? 'en';
        
        if ($lang === 'lt') {
            return [
                'title' => 'Paskyra patvirtinta',
                'message' => 'Sveikiname! Jūsų virėjo paskyra sėkmingai patvirtinta. Galite pradėti kurti savo meniu ir priimti užsakymus.',
                'type' => 'chef_approved',
                'chef_id' => $this->chef->id,
                'action_url' => config('app.frontend_url') . '/kitchen'
            ];
        }

        return [
            'title' => 'Account Approved',
            'message' => 'Congratulations! Your chef account has been successfully approved. You can now start creating your menu and accepting orders.',
            'type' => 'chef_approved',
            'chef_id' => $this->chef->id,
            'action_url' => config('app.frontend_url') . '/kitchen'
        ];
    }

    public function toFcm($notifiable)
    {
        return [];
    }
}
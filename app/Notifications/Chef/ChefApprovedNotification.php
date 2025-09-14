<?php

namespace App\Notifications\Chef;

use App\Models\Chef;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefApprovedNotification extends BaseNotification
{
    protected string $notificationType = 'chef_approved';

    public function __construct(
        protected Chef $chef
    )
    {
    }

    public function toArray($notifiable): array
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Paskyra patvirtinta'
            : 'Account Approved';

        $body = $isLithuanian
            ? 'Sveikiname! Jūsų virėjo paskyra sėkmingai patvirtinta. Galite pradėti kurti savo meniu ir priimti užsakymus.'
            : 'Congratulations! Your chef account has been successfully approved. You can now start creating your menu and accepting orders.';

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'chef_approved',
            'chef_id' => $this->chef->id,
            'action_url' => '/kitchen',
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🎉 Paskyra patvirtinta!'
            : '🎉 Account Approved!';

        $body = $isLithuanian
            ? "Sveikiname {$notifiable->first_name}! Jūsų virėjo paskyra sėkmingai patvirtinta."
            : "Congratulations {$notifiable->first_name}! Your chef account has been successfully approved.";

        return (new FcmMessage(
            notification: new FcmNotification(
                title: $title,
                body: $body,
            )
        ))
            ->data([
                'type' => $this->notificationType,
                'chef_id' => (string)$this->chef->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);
    }

    public function toMail($notifiable): MailMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        // Prepare all variables first
        $subject = $isLithuanian
            ? "Sveikiname! Jūsų virėjo paskyra patvirtinta"
            : "Congratulations! Your Chef Account has been Approved";

        $headerTitle = $isLithuanian
            ? 'Paskyra patvirtinta!'
            : 'Account Approved!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name}!"
            : "Hello {$notifiable->first_name}!";

        $message = $isLithuanian
            ? "Sveikiname! Džiugiai pranešame, kad jūsų virėjo paskyra sėkmingai patvirtinta.<br><br>
               Dabar galite pradėti kurti savo meniu, valdyti užsakymus ir patiekti skanius patiekalus klientams savo rajone.<br><br>
               <strong>Štai jūsų kiti žingsniai:</strong><br>
               • Užbaikite savo virtuvės profilį ir meniu nustatymą<br>
               • Įkelkite patrauklias patiekalų nuotraukas<br>
               • Nustatykite savo darbo laiką ir pristatymo nuostatas<br>
               • Pradėkite gauti ir valdyti užsakymus"
            : "Congratulations! We are excited to inform you that your chef account has been successfully approved.<br><br>
               You can now start creating your menu, managing orders, and serving delicious food to customers in your area.<br><br>
               <strong>Here are your next steps:</strong><br>
               • Complete your kitchen profile and menu setup<br>
               • Upload appealing photos of your dishes<br>
               • Set your availability and delivery preferences<br>
               • Start receiving and managing orders";

        $highlightMessage = $isLithuanian
            ? 'Sveiki atvykę į MamChef šeimą! Laukiame pamatyti jūsų kulinarijos kūrinius.<br><br>Geriausi linkėjimai,<br>MamChef komanda'
            : 'Welcome to the MamChef family! We look forward to seeing your culinary creations.<br><br>Best regards,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Patekti į virtuvės valdymo panelį' : 'Access Your Kitchen Dashboard';

        $footer = $this->mailFooter($notifiable->lang);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'message' => $message,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'success',
                'button_text' => $buttonText,
                'button_url' => config('app.chef_panel') . '/kitchen',
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
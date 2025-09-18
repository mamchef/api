<?php

namespace App\Notifications\Chef;

use App\Models\Chef;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefWelcomeNotification extends BaseNotification
{
    protected string $notificationType = 'chef_welcome';

    public function __construct(
        protected Chef $chef
    )
    {
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Welcome to MamChef!',
            'body' => "Welcome {$notifiable->first_name}! Your kitchen is ready to start earning.",
            'type' => 'chef_welcome',
            'chef_id' => $this->chef->id,
            'action_url' => '/dashboard',
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '🍳 Sveiki atvykę į MamChef!'
            : '🍳 Welcome to MamChef!';

        $body = $isLithuanian
            ? "Sveiki {$notifiable->first_name}! Jūsų kulinarijos kelionė prasideda dabar!"
            : "Hi {$notifiable->first_name}! Your culinary journey starts now!";

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
            ? "Sveiki atvykę į MamChef, {$notifiable->first_name}! Jūsų virtuvė paruošta."
            : "Welcome to MamChef, {$notifiable->first_name}! Your Kitchen Is Ready.";

        $headerTitle = $isLithuanian ? 'Sveiki atvykę į MamChef!' : 'Welcome to MamChef!';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name},"
            : "Hi {$notifiable->first_name},";

        $body = $isLithuanian
            ? "Sveiki atvykę į MamChef bendruomenę! Esame sužavėti, kad prisijungėte prie mūsų misijos - atnešti autentišką, namie gamintą maistą prie kiekvieno kaimyno stalo.<br><br>
               Jūs esate vos keliais žingsniais nuo to, kad pavertumėte savo virtuvę verslu. Štai kas toliau:<br><br>
               <strong>1. Užbaikite savo profilį:</strong> Nustatykite savo biografiją ir meniu.<br>
               <strong>2. Sutvarkykie teisės aktus:</strong> Peržiūrėkite ir pasirašykite partnerystės sutartį.<br>
               <strong>3. Gaminkite!</strong> Patvirtinus galėsite pradėti priimti užsakymus.<br><br>
               Mes čia, kad padėtume jums kiekviename žingsnyje."
            : "Welcome to the MamChef community! We're thrilled to have you join our mission to bring authentic, home-cooked food to every neighbor's table.<br><br>
               You're just a few steps away from turning your kitchen into a business. Here's what's next:<br><br>
               <strong>1. Complete Your Profile:</strong> Set up your bio and menu.<br>
               <strong>2. Get Legal:</strong> Review and sign the partnership agreement.<br>
               <strong>3. Cook!</strong> Once approved, you can start accepting orders.<br><br>
               We're here to help you every step of the way.";

        $highlightMessage = $isLithuanian
            ? 'Su pagarba,<br>MamChef komanda'
            : 'Best,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Pradėti' : 'Get Started';


        $footer = $this->mailFooter($notifiable->lang);

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.template', [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'body' => $message,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'info',
                'button_text' => $buttonText,
                'button_url' => env('CHEF_PANEL_URL', 'https://app.mamchef.com') . '/dashboard',
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
<?php

namespace App\Notifications\Chef;

use App\Models\Chef;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ChefGuideNotification extends BaseNotification
{
    protected string $notificationType = 'chef_guide';

    public function __construct(
        protected Chef $chef
    )
    {
    }

    public function toArray($notifiable): array
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? 'Greitas teisinio atitikimo vadovas'
            : 'A Quick Guide to Legal Compliance';

        $body = $isLithuanian
            ? "Sveiki {$notifiable->first_name}! Čia yra vadovas verslo registracijai."
            : "Hi {$notifiable->first_name}! Here's your business registration guide.";

        return [
            'title' => $title,
            'body' => $body,
            'type' => 'chef_guide',
            'chef_id' => $this->chef->id,
            'action_url' => '/legal-guide',
        ];
    }

    public function toFcm($notifiable): FcmMessage
    {
        $isLithuanian = ($notifiable->lang ?? 'en') === 'lt';

        $title = $isLithuanian
            ? '📋 Teisinio atitikimo vadovas'
            : '📋 Legal Compliance Guide';

        $body = $isLithuanian
            ? "Sveiki {$notifiable->first_name}! Peržiūrėkite mūsų paprastą vadovą verslo registracijai."
            : "Hi {$notifiable->first_name}! Check out our simple guide to business registration.";

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
            ? "Greitas teisinio atitikimo vadovas"
            : "A Quick Guide to Legal Compliance";

        $headerTitle = $isLithuanian
            ? 'Teisinio atitikimo vadovas'
            : 'Legal Compliance Guide';

        $greeting = $isLithuanian
            ? "Sveiki {$notifiable->first_name},"
            : "Hi {$notifiable->first_name},";

        $message = $isLithuanian
            ? 'Kaip patikima namų gamybos maisto platforma, mes reikalaujame, kad visi mūsų šefai būtų teisiškai atitinkantys reikalavimus. Nesijaudinkite, mes padarėme tai lengvą jums.<br><br>
               Štai pagrindiniai žingsniai, kuriuos turite atlikti registruodami savo verslą Lietuvoje:<br><br>
               <strong>1. Registruotis VMI:</strong> Įsteigkite savo „individualią veiklą" mokesčių valdymui.<br>
               <strong>2. Registruotis VMVT:</strong> Laikykitės maisto saugos įstatymų ir patvirtinkite savo virtuvę.<br><br>
               Mes sukūrėme paprastą žingsnis po žingsnio vadovą, kuris nuves jus per abu procesus.'
            : "As a trusted platform for homemade food, we require all our chefs to be legally compliant. Don't worry, we've made it easy for you.<br><br>
               Here are the essential steps you need to take to register your business in Lithuania:<br><br>
               <strong>1. Register with VMI:</strong> Set up your \"individual activity\" to manage taxes.<br>
               <strong>2. Register with VMVT:</strong> Comply with food safety laws and get your kitchen approved.<br><br>
               We've created a simple, step-by-step guide to walk you through both processes.";

        $highlightMessage = $isLithuanian
            ? 'Praneškite mums, jei turite klausimų!<br><br>Su pagarba,<br>MamChef komanda'
            : 'Let us know if you have any questions!<br><br>Best,<br>The MamChef Team';

        $buttonText = $isLithuanian ? 'Peržiūrėti teisės vadovą' : 'View our Legal Guide';

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
                'button_url' => $isLithuanian ? 'https://api.mamchef.com/assets/combine-guid-lt.pdf':'https://api.mamchef.com/assets/combine-guid-en.pdf',
                'footer' => $footer
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
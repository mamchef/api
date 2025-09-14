<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Fcm\FcmChannel;


abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $notificationType;
    protected array $additionalChannels = [];

    // Define the channels to send through
    public function via($notifiable): array
    {
        $channels[] = 'mail';
        return $channels;
        $channels = ['database', 'broadcast'];

        // Only add FCM if user has any tokens
        if ($notifiable->fcmTokens()->exists()) {
            $channels[] = FcmChannel::class;
        }

        // Only add mail if user has email
        if ($notifiable->email) {
            $channels[] = 'mail';
        }

        return $channels;

    }


    /**
     * Get the notification's database type
     */
    public function databaseType(): string
    {
        return $this->notificationType;
    }


    abstract public function toMail($notifiable);

    abstract public function toDatabase($notifiable);

    /**
     * Get the broadcastable representation (for Reverb)
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        $data = $this->toDatabase($notifiable);

        Log::info('Broadcasting notification', [
            'channel' => $notifiable->receivesBroadcastNotificationsOn(),
            'payload' => $data,
        ]);

        return new BroadcastMessage([
            ...$data,
            'notification_type' => $this->notificationType,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    abstract public function toFcm($notifiable);



    public function mailFooter(string $lang)
    {

        $facebook = config('app.social_facebook');
        $instagram = config('app.social_instagram');
        return strtolower($lang) == 'lt'
            ? '<small style="color: #6c757d;">Šis el. laiškas yra skirtas informaciniams tikslams. Išsamiai informacijai žr. mūsų partnerių nuostatas ir sąlygas.</small><br><br>
               <div style="text-align: center; color: #6c757d; font-size: 12px;">
                 MamChef UAB | Vilnius<br>
                 <a href="" style="color: #ff6b6b;">Svetainė</a> |
                 <a href="'.  $facebook .'" style="color: #ff6b6b;">Facebook</a> |
                 <a href="'.$instagram.'" style="color: #ff6b6b;">Instagram</a>
               </div>'
            : '<small style="color: #6c757d;">This email is intended for informational purposes. Please refer to our Partner Terms & Conditions for full details.</small><br><br>
               <div style="text-align: center; color: #6c757d; font-size: 12px;">
                 MamChef UAB | Krivių g. 5, LT-01204, Vilnius<br>
                 <a href="https://mamchef.com" style="color: #ff6b6b;">Website</a> |
                 <a href="'.  $facebook .'" style="color: #ff6b6b;">Facebook</a> |
                 <a href="'.$instagram.'" style="color: #ff6b6b;">Instagram</a>
               </div>';
    }

}
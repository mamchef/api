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

}
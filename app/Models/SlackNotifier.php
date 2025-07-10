<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Messages\SlackMessage;

class SlackNotifier
{
    use Notifiable;

    public function routeNotificationForSlack()
    {
        return env('SLACK_WEBHOOK_URL');
    }
}
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class OtpSlackNotification extends Notification
{
    use Queueable;

    protected $otpCode;
    protected $method;
    protected $target;

    public function __construct($otpCode, $method, $target)
    {
        $this->otpCode = $otpCode;
        $this->method = $method; // 'email' or 'sms'
        $this->target = $target; // email address or phone number
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $methodText = $this->method === 'email' ? 'Email' : 'SMS';

        return (new SlackMessage)
            ->from('OTP Service', ':key:')
            ->to('#otp') // or your preferred channel
            ->content("OTP Code Sent via {$methodText}")
            ->attachment(function ($attachment) use ($methodText) {
                $attachment->title("OTP Notification")
                    ->fields([
                        'Method' => $methodText,
                        'Target' => $this->target,
                        'Code' => $this->otpCode,
                        'Time' => now()->format('Y-m-d H:i:s')
                    ])
                    ->color($this->method === 'email' ? 'good' : 'warning');
            });
    }
}
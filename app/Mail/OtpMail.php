<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private string  $otpCode,
        private ?string $lang = null,
        private ?string $userName = null
    )
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $isLithuanian = ($this->lang ?? 'en') === 'lt';
        $subject = $isLithuanian ? 'Jūsų OTP kodas' : 'Your OTP Code';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $isLithuanian = ($this->lang ?? 'en') === 'lt';

        $headerTitle = $isLithuanian ? 'Jūsų OTP kodas' : 'Your OTP Code';

        $greeting = $this->userName
            ? ($isLithuanian ? "Sveiki {$this->userName}!" : "Hi {$this->userName}!")
            : ($isLithuanian ? 'Sveiki!' : 'Hello!');

        $body = $isLithuanian
            ? "Jūsų OTP kodas yra:<br><br><div style='font-size: 24px; font-weight: bold; text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px dashed #ff6b6b; color: #ff6b6b;'>{$this->otpCode}</div>"
            : "Your OTP code is:<br><br><div style='font-size: 24px; font-weight: bold; text-align: center; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px dashed #ff6b6b; color: #ff6b6b;'>{$this->otpCode}</div>";

        $highlightMessage = $isLithuanian
            ? 'Šis kodas baigs galioti po 2 minučių. Prašome jį naudoti greitai.'
            : 'This code will expire in 2 minutes. Please use it promptly.';

        $footer = $this->mailFooter($this->lang ?? 'en');

        return new Content(
            view: 'emails.template',
            with: [
                'header_title' => $headerTitle,
                'greeting' => $greeting,
                'body' => $body,
                'highlight_message' => $highlightMessage,
                'highlight_type' => 'warning',
                'footer' => $footer
            ],
        );
    }

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

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

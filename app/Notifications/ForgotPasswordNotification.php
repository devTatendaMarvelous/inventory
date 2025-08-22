<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class ForgotPasswordNotification extends Notification
{
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];  // You can also use database, SMS, etc. if you wish
    }

    public function toMail($notifiable)
    {
        $url = env('FRONT_END_URL').'/reset-password?token=' . $this->token . '&email=' . $this->email;

        return (new MailMessage)
            ->subject('Password Reset Request')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}

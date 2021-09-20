<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends ResetPassword
{

    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token)
            . '?email=' . urlencode($notifiable->email);
        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset.')
            ->action('Reset password', $url)
            ->line('Thank you for using our application!');
    }
}

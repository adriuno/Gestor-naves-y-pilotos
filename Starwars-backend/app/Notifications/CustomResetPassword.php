<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = 'http://localhost:3000/reset-password/' . $this->token;

        return (new MailMessage)
            ->subject(Lang::get('Restablecer contraseña'))
            ->line(Lang::get('Has solicitado un enlace para restablecer tu contraseña.'))
            ->action(Lang::get('Cambiar contraseña'), $frontendUrl)
            ->line(Lang::get('Si no solicitaste este cambio, puedes ignorar este mensaje.'));
    }
}

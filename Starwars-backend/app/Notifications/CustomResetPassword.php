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
        // URL del frontend con el token y el email como query param
        $frontendUrl = 'http://localhost:3000/reset-password/' . $this->token .
            '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        $logoImg = '<div style="text-align:center; margin-bottom:12px;">
                    <img src="' . asset('storage/images/logo/starwars.jpg') . '" alt="Logo del Gestor de Naves" width="100" height="100"
                       style="border-radius: 50%; object-fit: cover;">
                    </div>';

        return (new MailMessage)
            ->greeting('Hola 🛸,')
            ->subject(Lang::get('Restablecer contraseña'))
            ->line(Lang::get('Has solicitado un enlace para restablecer tu contraseña.'))
            ->action(Lang::get('Cambiar contraseña'), $frontendUrl)
            ->line(Lang::get('Si no solicitaste este cambio, puedes ignorar este mensaje.'))
            ->line(new \Illuminate\Support\HtmlString($logoImg))
            ->salutation('Que la fuerza te acompañe!!'); 
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Documento;

class LicitacionAvisoNotification extends Notification
{
    use Queueable;

    protected $documento;

    public function __construct(Documento $documento)
    {
        $this->documento = $documento;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Aviso: Licitación Próxima a Vencer')
                    ->greeting('Estimado usuario,')
                    ->line('La licitación "' . $this->documento->nombre_documento . '" está próxima a vencer en ' . $this->documento->dias_restantes . ' días.')
                    ->action('Iniciar sesión', url('/login' )) 
                    ->line('Por favor, tome las acciones necesarias antes de la fecha límite.')
                    ->salutation('Atentamente, Licitaciones.com');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'documento_id' => $this->documento->id,
            'nombre_documento' => $this->documento->nombre_documento,
            'dias_restantes' => $this->documento->dias_restantes,
        ];
    }
}

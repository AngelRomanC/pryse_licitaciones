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
        $fecha = \Carbon\Carbon::parse($this->documento->fecha_vigencia)->locale('es');
        $nombreDia = ucfirst($fecha->isoFormat('dddd'));
        $fechaVencimiento = $nombreDia . $fecha->isoFormat(', D [de] MMMM [de] YYYY');
        
        return (new MailMessage)
                    ->subject('Aviso Importante: Licitación Próxima a Vencer')
                    ->greeting('Estimado/a Responsable,')
                    ->line('Le informamos que la licitación:')
                    ->line('**"' . $this->documento->nombre_documento . '"**')
                    ->line('del departamento **' . $this->documento->departamento->nombre_departamento . '**')
                    ->line('vencerá en **' . $this->documento->dias_restantes . ' días** ('.$fechaVencimiento.')')
                    ->action('Acceder al Sistema', url('/login'))
                    ->line('**Acción requerida:**')
                    ->line('Por favor, revise los documentos y realice las gestiones necesarias antes de la fecha límite indicada.')
                    ->salutation('Atentamente, Equipo de Licitaciones.com ');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'documento_id' => $this->documento->id,
            'nombre_documento' => $this->documento->nombre_documento,
            'dias_restantes' => $this->documento->dias_restantes,
            'nombre_departamento' => $this->documento->nombre_departamento,
            'fecha_vigencia' => $this->documento->fecha_vigencia,
            

        ];
    }
}

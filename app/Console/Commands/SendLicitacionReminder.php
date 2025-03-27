<?php

namespace App\Console\Commands;

use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\User;
use App\Mail\LicitacionAviso; // Asegúrate de tener el Mailable
use App\Notifications\LicitacionAvisoNotification; // La notificación
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;


class SendLicitacionReminder extends Command
{
    protected $signature = 'documents:send-licitacion-reminder';
    protected $description = 'Envía recordatorios de licitación y notificaciones a los usuarios.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $documentos = Documento::with(['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades'])
            ->where('nombre_documento', 'Documento Técnico')
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        $documentosLegal = DocumentoLegal::with(['empresa', 'tipoDocumento', 'departamento'])
            ->where('nombre_documento', 'Documento Legal')
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        foreach ($documentos as $documento) {
            $fechaVigencia = Carbon::parse($documento->fecha_vigencia); // Convierte a Carbon
            $documento->dias_restantes = $fechaVigencia->diffInDays(now());

            if ($documento->dias_restantes === 7) {
                $this->enviarCorreoLicitacion($documento);
            }
        }


        // Calcular los días restantes para cada documento
        foreach ($documentosLegal as $documentoLegal) {
            $fechaVigencia = Carbon::parse($documentoLegal->fecha_vigencia); // Convierte a Carbon
            $documentoLegal->dias_restantes = $fechaVigencia->diffInDays(now());

            if ($documentoLegal->dias_restantes === 7) {
                $this->enviarCorreoLicitacion($documentoLegal);
            }
        }


        return 0;
    }
    private function enviarCorreoLicitacion($documento)
    {
        $usuarios = User::all(); // Obtener todos los usuarios

        foreach ($usuarios as $usuario) {
           // Mail::to($usuario->email)->send(new LicitacionAviso($documento));
            $usuario->notify(new LicitacionAvisoNotification($documento));


        }
    }
}

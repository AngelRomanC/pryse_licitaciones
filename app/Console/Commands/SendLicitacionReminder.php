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
            ->get(); // Cambia paginate por get para procesar todos
    
        $documentosLegal = DocumentoLegal::with(['empresa', 'tipoDocumento', 'departamento'])
            ->where('nombre_documento', 'Documento Legal')
            ->get();
    
        foreach ($documentos as $documento) {
            $diasRestantes = now()->startOfDay()->diffInDays(
                Carbon::parse($documento->fecha_vigencia)->startOfDay(), 
                false
            );
    
            if ($diasRestantes === 7) {
                $this->enviarCorreoLicitacion($documento);
            }
        }
    
        foreach ($documentosLegal as $documentoLegal) {
            $diasRestantes = now()->startOfDay()->diffInDays(
                Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), 
                false
            );
    
            if ($diasRestantes === 7) {
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

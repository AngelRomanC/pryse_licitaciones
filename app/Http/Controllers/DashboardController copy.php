<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use App\Mail\LicitacionAviso;
use Illuminate\Support\Facades\Mail;

use App\Notifications\LicitacionAvisoNotification; //notify



class DashboardController extends Controller
{
    /* public function index(): Response
     {
         return Inertia::render('Dashboard', [
             'users' => User::count(), // Número total de usuarios
             'latestUsers' => User::latest()->take(5)->get(), // Últimos 5 usuarios registrados
         ]);
     } */
    public function index(): Response
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) {
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
            // Calcular los días restantes para cada documento
            foreach ($documentos as $documento) {
                $fechaVigencia = Carbon::parse($documento->fecha_vigencia); // Convierte a Carbon
                $documento->dias_restantes = $fechaVigencia->diffInDays(now());

                if ($documento->dias_restantes === 7) {
                    //$this->enviarCorreoLicitacion($documento);
                }
            }

            foreach ($documentosLegal as $documentoLegal) {
                $fechaVigencia = Carbon::parse($documentoLegal->fecha_vigencia); // Convierte a Carbon
                $documentoLegal->dias_restantes = $fechaVigencia->diffInDays(now());

                if ($documentoLegal->dias_restantes === 7) {
                    $this->enviarCorreoLicitacion($documentoLegal);
                }
            }       

          
            return Inertia::render('Dashboard/Admin', [
                'users' => User::count(),
                'latestUsers' => User::latest()->take(5)->get(),
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",


            ]);

        } else {
            $documentos = Documento::with(['empresa', 'tipoDocumento', 'estado', 'departamento', 'modalidades'])
                ->orderBy('id')
                ->paginate(8)
                ->withQueryString();

            $documentosLegal = DocumentoLegal::with(['empresa', 'tipoDocumento', 'departamento'])
                ->orderBy('id')
                ->paginate(8)
                ->withQueryString();
            // Calcular los días restantes para cada documento
            foreach ($documentos as $documento) {
                $fechaVigencia = Carbon::parse($documento->fecha_vigencia); // Convierte a Carbon
                $documento->dias_restantes = $fechaVigencia->diffInDays(now());
            }

            foreach ($documentosLegal as $documentoLegal) {
                $fechaVigencia = Carbon::parse($documentoLegal->fecha_vigencia); // Convierte a Carbon
                $documentoLegal->dias_restantes = $fechaVigencia->diffInDays(now());
            }
            return Inertia::render('Dashboard/Usuario', [
                'message' => 'Bienvenido al Dashboard de Usuario',
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",
            ]);
        }

    }
    // Función para enviar correo a todos los usuarios
    private function enviarCorreoLicitacion($documento)
    {
        $usuarios = User::all(); // Obtener todos los usuarios

        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new LicitacionAviso($documento));
            $usuario->notify(new LicitacionAvisoNotification($documento));


        }
    }
}

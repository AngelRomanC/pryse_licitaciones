<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            $documentos = Documento::with(['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades'])
                -> where('nombre_documento', 'Documento Técnico')
                ->orderBy('id')
                ->paginate(10)
                ->through(fn($documento) => $documento->setAttribute(
                    'dias_restantes',
                    now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false) 

                ))
                ->withQueryString();

            $documentosLegal = DocumentoLegal::with(['empresa', 'tipoDeDocumento', 'departamento'])
                ->where('nombre_documento', 'Documento Legal')
                ->orderBy('id')
                ->paginate(10)
                ->through(fn($documentoLegal) => $documentoLegal->setAttribute(
                    'dias_restantes',
                    now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false) 
                    
                ))
                ->withQueryString();
         

            return Inertia::render('Dashboard/Admin', [
                'users' => User::count(),
                'latestUsers' => User::latest()->take(3)->get(),
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",

            ]);

        } else {
            // Definir relaciones comunes para cargar
            $relacionesDocumentos = ['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades'];
            $relacionesDocumentosLegales = ['empresa', 'tipoDeDocumento', 'departamento'];

            // Obtener documentos paginados con días restantes
            $documentos = Documento::with($relacionesDocumentos)
                ->where('nombre_documento', 'Documento Técnico')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(8)
                ->through(fn($documento) => $documento->setAttribute(
                    'dias_restantes',
                   // Carbon::parse($documento->fecha_vigencia)->diffInDays(now())
                   now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false) 
                 
                ))
                ->withQueryString();

            $documentosLegal = DocumentoLegal::with($relacionesDocumentosLegales)
                ->where('nombre_documento', 'Documento Legal')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(8)
                ->through(fn($documentoLegal) => $documentoLegal->setAttribute(
                    'dias_restantes',
                    //Carbon::parse($documentoLegal->fecha_vigencia)->diffInDays(now())
                    now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false) 
                    
                    
                ))
                ->withQueryString();

            // Obtener todos los documentos (para la gráfica) y calcular días restantes
            $documentosAll = Documento::with($relacionesDocumentos)
                ->where('nombre_documento', 'Documento Técnico')
                ->orderBy('id')
                ->get()
                ->map(fn($documento) => $documento->setAttribute(
                    'dias_restantes',                
                   now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false) 

                ));

            $documentosLegalAll = DocumentoLegal::with($relacionesDocumentosLegales)
                ->where('nombre_documento', 'Documento Legal')
                ->orderBy('id')
                ->get()
                ->map(fn($documentoLegal) => $documentoLegal->setAttribute(
                    'dias_restantes',
                    now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false) 

                ));

            return Inertia::render('Dashboard/Usuario', [
                'message' => 'Bienvenido al Dashboard de Usuario',
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",
                'd1' => $documentosAll,
                'd2' => $documentosLegalAll,
            ]);
        }

    }
}

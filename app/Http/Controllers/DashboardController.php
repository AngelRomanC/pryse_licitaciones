<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\Licitacion;
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

        if (session('active_role') === 'Admin')  {
            $documentos = Documento::with(['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades', 'archivos'])
                ->where('nombre_documento', 'Documento Técnico')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], 'page_tecnico')
                ->through(
                    fn($documento) => $documento->setAttribute(
                        'dias_restantes',
                        now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false)

                    )
                        ->setAttribute(
                            'dias_restantes_revalidacion',
                            now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_revalidacion)->startOfDay(), false)
                        )

                )
                ->withQueryString();

            $documentosLegal = DocumentoLegal::with(['empresa', 'tipoDeDocumento', 'departamento', 'archivos'])
                ->where('nombre_documento', 'Documento Legal')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], pageName: 'page_legal')
                ->through(
                    fn($documentoLegal) => $documentoLegal->setAttribute(
                        'dias_restantes',
                        now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false)

                    )
                        ->setAttribute(
                            'dias_restantes_revalidacion',
                            now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_revalidacion)->startOfDay(), false)
                        )
                )
                ->withQueryString();

            $documentosVencidos = Documento::with(['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades', 'archivos'])
                ->where('nombre_documento', 'Documento Técnico')
                ->where('fecha_vigencia', '<', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], 'page_tecnico_vencidos')
                ->through(fn($doc) => $doc->setAttribute('dias_restantes', now()->startOfDay()->diffInDays(Carbon::parse($doc->fecha_vigencia)->startOfDay(), false))     ->setAttribute(
                            'dias_restantes_revalidacion',
                            now()->startOfDay()->diffInDays(Carbon::parse($doc->fecha_revalidacion)->startOfDay(), false)
                        ));

            $documentosLegalVencidos = DocumentoLegal::with(['empresa', 'tipoDeDocumento', 'departamento', 'archivos'])
                ->where('nombre_documento', 'Documento Legal')
                ->where('fecha_vigencia', '<', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], 'page_legal_vencidos')
                ->through(fn($doc) => $doc->setAttribute('dias_restantes', now()->startOfDay()->diffInDays(Carbon::parse($doc->fecha_vigencia)->startOfDay(), false))
              ->setAttribute(
                            'dias_restantes_revalidacion',
                            now()->startOfDay()->diffInDays(Carbon::parse($doc->fecha_revalidacion)->startOfDay(), false)
                        ));



            return Inertia::render('Dashboard/Admin', [
                'users' => User::count(),
                'latestUsers' => User::latest()->take(3)->get(),
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",
                'licitaciones' => Licitacion::count(),
                'documentosVencidos' => $documentosVencidos,
                'documentosLegalVencidos' => $documentosLegalVencidos,

            ]);

        } else {
            // Definir relaciones comunes para cargar
            $user = Auth::user()->load('departamento.infoDepartamento');

            $relacionesDocumentos = ['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades', 'archivos'];
            $relacionesDocumentosLegales = ['empresa', 'tipoDeDocumento', 'departamento', 'archivos'];

            // Obtener documentos paginados con días restantes
            $documentos = Documento::with($relacionesDocumentos)
                ->where('nombre_documento', 'Documento Técnico')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], 'page_tecnico')
                ->through(fn($documento) => $documento->setAttribute(
                    'dias_restantes',
                    now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false)

                )
                    ->setAttribute(
                        'dias_restantes_revalidacion',
                        now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_revalidacion)->startOfDay(), false)
                    ))
                ->withQueryString();

            $documentosLegal = DocumentoLegal::with($relacionesDocumentosLegales)
                ->where('nombre_documento', 'Documento Legal')
                ->where('fecha_vigencia', '>=', now())
                ->orderBy('fecha_vigencia', 'asc')
                ->paginate(5, ['*'], pageName: 'page_legal')
                ->through(fn($documentoLegal) => $documentoLegal->setAttribute(
                    'dias_restantes',
                    //Carbon::parse($documentoLegal->fecha_vigencia)->diffInDays(now())
                    now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false)


                )
                    ->setAttribute(
                        'dias_restantes_revalidacion',
                        now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_revalidacion)->startOfDay(), false)
                    ))
                ->withQueryString();


            return Inertia::render('Dashboard/Usuario', [
                'titulo' => "Bienvenido al Dashboard de Usuario - {$user->departamento?->infoDepartamento?->nombre_departamento}",
                'documentos' => $documentos,
                'documentosLegal' => $documentosLegal,
                'titulo1' => "Documentos Técnicos",
                'titulo2' => "Documentos Legales",
            ]);
        }

    }

    public function vencidos(): Response
    {
        $user = Auth::user()->load('departamento.infoDepartamento');

        $relacionesDocumentos = ['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades', 'archivos'];
        $relacionesDocumentosLegales = ['empresa', 'tipoDeDocumento', 'departamento', 'archivos'];

        // Documentos técnicos vencidos
        $documentosVencidos = Documento::with($relacionesDocumentos)
            ->where('nombre_documento', 'Documento Técnico')
            ->where('fecha_vigencia', '<', now())
            ->orderBy('fecha_vigencia', 'asc')
            ->paginate(5, ['*'], 'page_tecnico_vencidos')
            ->through(fn($documento) => $documento->setAttribute(
                'dias_restantes',
                now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_vigencia)->startOfDay(), false)
            )
                ->setAttribute(
                    'dias_restantes_revalidacion',
                    now()->startOfDay()->diffInDays(Carbon::parse($documento->fecha_revalidacion)->startOfDay(), false)
                ))
            ->withQueryString();

        // Documentos legales vencidos
        $documentosLegalVencidos = DocumentoLegal::with($relacionesDocumentosLegales)
            ->where('nombre_documento', 'Documento Legal')
            ->where('fecha_vigencia', '<', now())
            ->orderBy('fecha_vigencia', 'asc')
            ->paginate(5, ['*'], 'page_legal_vencidos')
            ->through(fn($documentoLegal) => $documentoLegal->setAttribute(
                'dias_restantes',
                now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_vigencia)->startOfDay(), false)
            )
                ->setAttribute(
                    'dias_restantes_revalidacion',
                    now()->startOfDay()->diffInDays(Carbon::parse($documentoLegal->fecha_revalidacion)->startOfDay(), false)
                ))
            ->withQueryString();

        return Inertia::render('Dashboard/Vencidos', [
            'titulo' => "Bienvenido al Dashboard de Usuario - {$user->departamento?->infoDepartamento?->nombre_departamento}",
            'titulo1' => 'Documentos Técnicos Vencidos',
            'titulo2' => 'Documentos Legales Vencidos',
            'documentos' => $documentosVencidos,
            'documentosLegal' => $documentosLegalVencidos,
        ]);
    }

}

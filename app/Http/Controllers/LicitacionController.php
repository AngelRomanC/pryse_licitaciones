<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\Licitacion;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Estado;
use Inertia\Inertia;


class LicitacionController extends Controller
{
    private string $routeName;
    protected string $module = 'licitacion';
    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'licitacion.';
        // $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        // $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        // $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        // $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }
    public function index()
    {
        $licitaciones = Licitacion::with(['empresa', 'estado'])
            ->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Licitacion/Index', [
            'titulo' => 'Lista de Licitaciones',
            'licitaciones' => $licitaciones,
            'routeName' => $this->routeName,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();

        $documentosTecnicos = Documento::where('nombre_documento', 'Documento Técnico')
            ->select('id', 'nombre_documento as name')
            ->get();

        $documentosLegales = DocumentoLegal::where('nombre_documento', 'Documento Legal')
            ->select('id', 'nombre_documento as name')
            ->get();

        return Inertia::render('Licitacion/Create', [
            'empresas' => $empresas,
            'estados' => $estados,
            'documentos_tecnicos' => $documentosTecnicos,
            'documentos_legales' => $documentosLegales,
        ]);
    }

    public function getDocumentosByEmpresa(Empresa $empresa)
    {
        $documentosTecnicos = $empresa->documentosTecnicos()
            ->where('nombre_documento', 'Documento Técnico')
            ->with(['archivos:id,documento_id,nombre_original,ruta_archivo','tipoDeDocumento:id,nombre_documento'])
            ->select('id', 'tipo_de_documento_id')
            ->get();

        $documentosLegales = $empresa->documentosLegales()
            ->where('nombre_documento', 'Documento Legal')
            ->with(['archivos:id,documento_id,nombre_original,ruta_archivo', 'tipoDeDocumento:id,nombre_documento'])
            ->select('id', 'tipo_de_documento_id')
            ->get();

        return response()->json([
            'documentos_tecnicos' => $documentosTecnicos,
            'documentos_legales' => $documentosLegales,
        ]);
    }

    



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Licitacion $licitacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Licitacion $licitacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Licitacion $licitacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licitacion $licitacion)
    {
        //
    }
}

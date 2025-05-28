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
        $licitaciones = Licitacion::orderBy('id', 'desc')
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
            'routeName' => $this->routeName,

        ]);
    }

    public function getDocumentosByEmpresa(Empresa $empresa)
    {
        $documentosTecnicos = $empresa->documentosTecnicos()
            ->where('nombre_documento', 'Documento Técnico')
            ->with(['archivos:id,documento_id,nombre_original,ruta_archivo', 'tipoDeDocumento:id,nombre_documento'])
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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'empresa_id' => 'required|array|min:1',
            'estados' => 'required|array|min:1',
            'archivos_legales' => 'nullable|array',
            'archivos_tecnicos' => 'nullable|array',
        ]);

        $licitacion = Licitacion::create([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
        ]);

        // Relacionar empresas
        $licitacion->empresas()->attach($request->empresa_id);

        // Relacionar estados
        $licitacion->estados()->attach($request->estados);

        // Relacionar archivos legales
        if ($request->filled('archivos_legales')) {
            foreach ($request->archivos_legales as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'legal']);
            }
        }

        // Relacionar archivos técnicos
        if ($request->filled('archivos_tecnicos')) {
            foreach ($request->archivos_tecnicos as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'tecnico']);
            }
        }

        return redirect()->route('licitacion.index')->with('success', 'Licitación creada correctamente.');


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

<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\Licitacion;
use App\Models\Modalidad;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Estado;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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
    public function index(Request $request)
    {
        $query = Licitacion::query();

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $licitaciones = $query->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Licitacion/Index', [
            'titulo' => 'Lista de Licitaciones',
            'licitaciones' => $licitaciones,
            'routeName' => $this->routeName,
            'filters' => $request->only('search'), // <- para que Vue sepa el valor actual del filtro

        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();
        $modalidades = Modalidad::select('id', 'nombre_modalidad as name')->get();

        return Inertia::render('Licitacion/Create', [
            'empresas' => $empresas,
            'estados' => $estados,
            'routeName' => $this->routeName,
            'modalidades' => $modalidades,

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
            'modalidades_id' => 'required|array|min:1',

        ]);

        $licitacion = Licitacion::create([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'ruta_expediente' => '', // o algo como 'pendiente'

        ]);

        // Relacionar empresas
        $licitacion->empresas()->attach($request->empresa_id);

        // Relacionar estados
        $licitacion->estados()->attach($request->estados);

        $licitacion->modalidades()->attach($request->modalidades_id);

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

        $this->generarZipLicitacion($licitacion);

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

    public function generarZipLicitacion(Licitacion $licitacion)
    {
        $folderName = "expedientes/licitacion_{$licitacion->nombre}";
        Storage::makeDirectory($folderName);

        $zip = new ZipArchive;
        // $folderName = "expedientes/licitacion_{$licitacion->nombre}";
        $zipPath = storage_path("app/{$folderName}.zip");

        // Crear ZIP
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($licitacion->empresas as $empresa) {
                foreach (['legal', 'tecnico'] as $tipo) {
                    $archivos = $licitacion->archivos()->wherePivot('tipo', $tipo)->get();

                    foreach ($archivos as $archivo) {
                        $ruta = $archivo->ruta_archivo;
                        $nombreEmpresa = $empresa->nombre;
                        $tipoDocumento = optional(optional($archivo->documento)->tipoDeDocumento)->nombre_documento ?? 'Desconocido';

                        $subtipo = $archivo->tipo === 'principal' ? 'principales' : 'anexos';




                        //$pathDentroZip = "{$nombreEmpresa}/{$tipo}/{$tipoDocumento}/{$subtipo}/" . basename($ruta);
                        $pathDentroZip = "{$nombreEmpresa}/{$tipoDocumento}/{$tipo}/{$subtipo}/" . basename($ruta);

                        $fullPath = storage_path("app/public/{$ruta}");



                        if (file_exists($fullPath)) {
                            $zip->addFile($fullPath, $pathDentroZip);
                        }
                    }
                }
            }

            $zip->close();
        }

        // Guardar ruta en la base de datos
        $licitacion->update(['ruta_expediente' => $folderName . '.zip']);
    }

    public function descargarExpediente($nombre)
    {
        $path = "expedientes/licitacion_{$nombre}.zip";

        if (!Storage::exists($path)) {
            abort(404, 'El archivo ZIP no existe.');
        }

        return Storage::download($path, "expediente_licitacion_{$nombre}.zip");
    }

  /*  public function descargarExpediente($nombre)
    {
        $nombreNormalizado = Str::slug($nombre, '_');
        $path = "expedientes/licitacion_{$nombreNormalizado}.zip";

        if (!Storage::exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::download($path, "expediente_{$nombreNormalizado}.zip");
    }*/

}

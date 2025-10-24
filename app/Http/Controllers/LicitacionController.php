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
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
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
        ],
        [
            'modalidades_id.required' => 'El campo modalidades es obligatorio.',
            'modalidades_id.min' => 'Debes seleccionar al menos una modalidad.',
            'empresa_id.required' => 'El campo empresa es obligatorio.',

        ]
    );

        $modalidadesSeleccionadas = $request->modalidades_id;
        $empresasSeleccionadas = $request->empresa_id;
        $errores = [];

        foreach ($empresasSeleccionadas as $empresaId) {
            $empresa = Empresa::find($empresaId);

            foreach ($modalidadesSeleccionadas as $modalidadId) {
                $tieneDocumento = Documento::where('empresa_id', $empresaId)
                    ->where('nombre_documento', 'Documento Técnico')
                    ->whereHas('modalidades', function ($query) use ($modalidadId) {
                        $query->where('modalidad_id', $modalidadId);
                    })
                    ->exists();

                if (!$tieneDocumento) {
                    $nombreEmpresa = $empresa->nombre;
                    $nombreModalidad = Modalidad::find($modalidadId)?->nombre_modalidad ?? 'Desconocida';
                    $errores[] = "La empresa '{$nombreEmpresa}' no tiene documentos técnicos con la modalidad '{$nombreModalidad}'.";
                }
            }
        }

        // Aquí asignamos los errores a la sesión para que el frontend los reciba
        if (!empty($errores)) {
            session()->flash('alerta_modalidades', $errores);
        }
        //dd(session()->all());



        $licitacion = Licitacion::create([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
            'ruta_expediente' => '',
        ]);

        $licitacion->empresas()->attach($request->empresa_id);
        $licitacion->estados()->attach($request->estados);
        $licitacion->modalidades()->attach($request->modalidades_id);

        if ($request->filled('archivos_legales')) {
            foreach ($request->archivos_legales as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'legal']);
            }
        }

        if ($request->filled('archivos_tecnicos')) {
            foreach ($request->archivos_tecnicos as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'tecnico']);
            }
        }

        $this->generarZipLicitacion($licitacion);

        return redirect()->route('licitacion.index')->with('success', 'Licitación creada correctamente.');
    }

    public function verificarModalidades(Request $request)
    {
        $modalidadesSeleccionadas = $request->modalidades_id;
        $empresasSeleccionadas = $request->empresa_id;
        $errores = [];

        foreach ($empresasSeleccionadas as $empresaId) {
            $empresa = Empresa::find($empresaId);
            foreach ($modalidadesSeleccionadas as $modalidadId) {
                $tieneDocumento = Documento::where('empresa_id', $empresaId)
                    ->where('nombre_documento', 'Documento Técnico')
                    ->whereHas('modalidades', function ($query) use ($modalidadId) {
                        $query->where('modalidad_id', $modalidadId);
                    })
                    ->exists();

                if (!$tieneDocumento) {
                    $nombreEmpresa = $empresa->nombre;
                    $nombreModalidad = Modalidad::find($modalidadId)?->nombre_modalidad ?? 'Desconocida';
                    $errores[] = "La empresa '{$nombreEmpresa}' no tiene documentos técnicos con la modalidad '{$nombreModalidad}'.";
                }
            }
        }

        return response()->json([
            'errores' => $errores
        ]);
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
        $licitacion->load(['empresas', 'estados', 'modalidades', 'archivos']);

        // Obtener IDs de archivos por tipo
        $archivosLegales = $licitacion->archivos
            ->where('pivot.tipo', 'legal')
            ->pluck('id')
            ->all();

        $archivosTecnicos = $licitacion->archivos
            ->where('pivot.tipo', 'tecnico')
            ->pluck('id')
            ->all();

        $empresas = Empresa::select('id', 'nombre as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();
        $modalidades = Modalidad::select('id', 'nombre_modalidad as name')->get();

        return Inertia::render('Licitacion/Edit', [
            'licitacion' => $licitacion,
            'empresas' => $empresas,
            'estados' => $estados,
            'modalidades' => $modalidades,
            'routeName' => $this->routeName,
            'archivos_legales_initial' => $archivosLegales,
            'archivos_tecnicos_initial' => $archivosTecnicos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Licitacion $licitacion)
    {
       // dd($request->all());

        $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'empresa_id' => 'required|array|min:1',
            'estados' => 'required|array|min:1',
            'modalidades_id' => 'required|array|min:1',
            'archivos_legales' => 'nullable|array',
            'archivos_tecnicos' => 'nullable|array',
            'archivos_a_eliminar' => 'nullable|array',

           ],
        [
            'modalidades_id.required' => 'El campo modalidades es obligatorio.',
            'modalidades_id.min' => 'Debes seleccionar al menos una modalidad.',
            'empresa_id.required' => 'El campo empresa es obligatorio.',

        ]
    );

        $licitacion->update([
            'nombre' => $request->nombre,
            'fecha' => $request->fecha,
        ]);

        $licitacion->empresas()->sync($request->empresa_id);
        $licitacion->estados()->sync($request->estados);
        $licitacion->modalidades()->sync($request->modalidades_id);

        $licitacion->archivos()->detach();

        // Sincronizar archivos legales
        $licitacion->archivos()->wherePivot('tipo', 'legal')->detach();
        if ($request->filled('archivos_legales')) {
            foreach ($request->archivos_legales as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'legal']);
            }
        }

        // Sincronizar archivos técnicos
        $licitacion->archivos()->wherePivot('tipo', 'tecnico')->detach();
        if ($request->filled('archivos_tecnicos')) {
            foreach ($request->archivos_tecnicos as $archivoId) {
                $licitacion->archivos()->attach($archivoId, ['tipo' => 'tecnico']);
            }
        }

        $this->generarZipLicitacion($licitacion);

        return redirect()->route('licitacion.index')->with('success', 'Licitación actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licitacion $licitacion)
    {
        // Eliminar relaciones
        $licitacion->empresas()->detach();
        $licitacion->estados()->detach();
        $licitacion->modalidades()->detach();
        $licitacion->archivos()->detach();

        // Eliminar ZIP si existe
        if ($licitacion->ruta_expediente && Storage::exists($licitacion->ruta_expediente)) {
            Storage::delete($licitacion->ruta_expediente);
        }

        $licitacion->delete();

        return redirect()->route('licitacion.index')->with('success', 'Licitación eliminada correctamente.');

    }

    public function generarZipLicitacion(Licitacion $licitacion)
    {
        // $folderName = "expedientes/licitacion_{$licitacion->nombre}";
        // Storage::makeDirectory($folderName);

        // $zip = new ZipArchive;
        // $zipPath = storage_path("app/{$folderName}.zip");
        $zip = new ZipArchive;
        $zipFileName = "expedientes/licitacion_{$licitacion->nombre}.zip";
        $zipPath = storage_path("app/{$zipFileName}");

        // Crear directorio padre si no existe
        Storage::makeDirectory(dirname($zipFileName));


        // Crear ZIP
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($licitacion->empresas as $empresa) {
                foreach (['legal', 'tecnico'] as $tipo) {
                    //$archivos = $licitacion->archivos()->wherePivot('tipo', $tipo)->get();
                    $archivos = $licitacion->archivos()
                        ->wherePivot('tipo', $tipo)
                        ->whereHas('documento', function ($query) use ($empresa) {
                            $query->where('empresa_id', $empresa->id);
                        })
                        ->get();

                    foreach ($archivos as $archivo) {
                        $ruta = $archivo->ruta_archivo;
                        $nombreEmpresa = $empresa->nombre;
                        $tipoDocumento = optional(optional($archivo->documento)->tipoDeDocumento)->nombre_documento ?? 'Desconocido';

                        $subtipo = $archivo->tipo === 'principal' ? 'principales' : 'anexos';


                        //$pathDentroZip = "{$nombreEmpresa}/{$tipo}/{$tipoDocumento}/{$subtipo}/" . basename($ruta);
                        //$pathDentroZip = "{$nombreEmpresa}/{$tipo}/{$tipoDocumento}/{$subtipo}/" . basename($ruta);

                        $pathDentroZip = "{$nombreEmpresa}/{$tipo}/" . basename($ruta);


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
        //$licitacion->update(['ruta_expediente' => $zipFileName . '.zip']);
        $licitacion->update(['ruta_expediente' => $zipFileName]);

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

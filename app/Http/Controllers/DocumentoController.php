<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Modalidad;
use App\Models\TipoDeDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use ZipArchive;




class DocumentoController extends Controller
{
    private string $routeName;
    protected string $module = 'documento';


    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'documento.';
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }
    public function index(Request $request)
    {

        $query = Documento::with(['empresa', 'tipoDeDocumento', 'departamento']);

        // if ($request->filled('search')) {
        //     $query->where('nombre_documento', 'like', '%' . $request->search . '%');
        // }
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre_documento', 'like', $searchTerm)
                    ->orWhereHas('empresa', function ($q) use ($searchTerm) {
                        $q->where('nombre', 'like', $searchTerm);
                    })
                    ->orWhereHas('tipoDeDocumento', function ($q) use ($searchTerm) {
                        $q->where('nombre_documento', 'like', $searchTerm);
                    })
                    ->orWhereHas('departamento', function ($q) use ($searchTerm) {
                        $q->where('nombre_departamento', 'like', $searchTerm);
                    });
            });
        }

        if ($request->filled('empresa')) {
            $query->where('empresa_id', $request->empresa);
        }

        if ($request->filled('tipo_de_documento')) {
            $query->where('tipo_de_documento_id', $request->tipo_de_documento);
        }

        if ($request->filled('departamento')) {
            $query->where('departamento_id', $request->departamento);
        }

        $documentos = $query->paginate(7)->withQueryString();

        return Inertia::render('Documento/Index', [
            'titulo' => 'Lista de Documentos Técnicos',
            'documentos' => $documentos,
            'routeName' => $this->routeName,
            'loadingResults' => false,
            'empresas' => Empresa::select('id', 'nombre as name')->get(), // Añadido as name
            'tipos_documento' => TipoDeDocumento::select('id', 'nombre_documento as name')->get(), // Añadido as name
            'departamentos' => Departamento::select('id', 'nombre_departamento as name')->get(), // Añadido as name
            'filters' => $request->all(['search', 'empresa', 'tipo_de_documento', 'departamento']),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $tipos_documento = TipoDeDocumento::select('id', 'nombre_documento as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();
        $departamentos = Departamento::select('id', 'nombre_departamento as name')->get();
        $modalidades = Modalidad::select('id', 'nombre_modalidad as name')->get();

        return Inertia::render('Documento/Create', [
            'titulo' => 'Crear Documento Técnico',
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'tipos_documento' => $tipos_documento,
            'estados' => $estados,
            'departamentos' => $departamentos,
            'modalidades' => $modalidades,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_de_documento_id' => 'required|exists:tipo_de_documentos,id',
            'estado_id' => 'required|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'required|array',
            'modalidad_id.*' => 'exists:modalidads,id',
            'ruta_documento' => 'required|array',
            'ruta_documento.*' => 'file|mimes:pdf|max:10240',
            'ruta_documento_anexo' => 'nullable|array',
            'ruta_documento_anexo.*' => 'file|mimes:pdf|max:10240',
        ]);

        // Definir carpeta base para documentos técnicos
        $folder = 'documentos_tecnicos';
        $folderAnexos = "$folder/documentos_anexos";

        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if (!Storage::disk('public')->exists("$folder/documentos_anexos")) {
            Storage::disk('public')->makeDirectory("$folder/documentos_anexos");
        }

        // Guardar documentos
        // $rutaDocumento = $this->storeFile($request->file('ruta_documento'), "$folder");
        // $rutaDocumentoAnexo = $this->storeFile($request->file('ruta_documento_anexo'), "$folder/documentos_anexos");

        // Crear documento en la base de datos
        $documento = Documento::create([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'] ?? null,
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
            // 'ruta_documento' => $rutaDocumento,
            // 'ruta_documento_anexo' => $rutaDocumentoAnexo,
        ]);

        // Guardar documentos principales
        foreach ($request->file('ruta_documento') as $file) {
            $documento->archivos()->create([
                'ruta_archivo' => $this->storeFile($file, $folder),
                'tipo' => 'principal',
                'nombre_original' => $file->getClientOriginalName(),

            ]);
        }

        // Guardar documentos anexos (si existen)
        if ($request->hasFile('ruta_documento_anexo')) {
            foreach ($request->file('ruta_documento_anexo') as $file) {
                $documento->archivos()->create([
                    'ruta_archivo' => $this->storeFile($file, $folderAnexos),
                    'tipo' => 'anexo',
                    'nombre_original' => $file->getClientOriginalName(),

                ]);
            }
        }

        // Guardar las fechas en la tabla 'fechas' relacionada
        $documento->fechas()->create([
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
        ]);

        // Asociar modalidades si existen
        if (!empty($validated['modalidad_id'])) {
            $documento->modalidades()->attach($validated['modalidad_id']);
        }

        if ($request->filled('redirect')) {
            return redirect($request->input('redirect'))
                ->with('success', 'Documento Técnico creado correctamente');
        }

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento creado con éxito.');
    }

    /**
     * Guardar un archivo en la carpeta especificada
     */

    private function storeFile($file, $folder)
    {
        // return $file ? $file->storeAs($folder, time() . '_' . $file->getClientOriginalName(), 'public') : null;
        $originalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . $originalName . '.' . $extension;

        return $file->storeAs($folder, $fileName, 'public');
    }

    /**
     * Display the specified resource.
     */
    public function show(Documento $documento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Documento $documento)
    {
        //dd($documento);  // Esto te permite ver qué contiene el objeto.

        $empresas = Empresa::select('id', 'nombre as name')->get();
        $tipos_documento = TipoDeDocumento::select('id', 'nombre_documento as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();
        $departamentos = Departamento::select('id', 'nombre_departamento as name')->get();
        // Cargar las modalidades asociadas al documento
        $modalidadesAsociadas = Modalidad::select('id', 'nombre_modalidad as name')
            ->whereIn('id', $documento->modalidades->pluck('id')) // Obtener solo las modalidades asociadas
            ->get();

        $modalidades = Modalidad::select('id', 'nombre_modalidad as name')->get();
        // Asegurar que las modalidades asociadas estén al inicio y eliminar duplicados
        $modalidades = $modalidadesAsociadas->merge($modalidades)->unique('id');
        $modalidades = $modalidades->sortBy('id')->values(); // Puedes cambiar 'id' por 'name' si quieres orden alfabético
        // Separar archivos principales y anexos
        $archivosPrincipales = $documento->archivos->where('tipo', 'principal')->values();
        $archivosAnexos = $documento->archivos->where('tipo', 'anexo')->values(); //forzar para empezar indice des 0 y ser array




        return Inertia::render('Documento/Edit', [
            'titulo' => 'Editar Documento',
            'documento' => $documento,
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'tipos_documento' => $tipos_documento,
            'estados' => $estados,
            'departamentos' => $departamentos,
            'modalidades' => $modalidades,
            'archivosPrincipales' => $archivosPrincipales,
            'archivosAnexos' => $archivosAnexos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        // logger('Archivos pdf en updateControlador:', $request->allFiles());
        // Log::info('Datos recibidos en update:', $request->all());

        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_de_documento_id' => 'required|exists:tipo_de_documentos,id',
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|array|exists:modalidads,id',
            'nuevos_documentos_principales' => 'nullable|array',
            'nuevos_documentos_principales.*' => 'file|mimes:pdf|max:10240',
            'nuevos_documentos_anexos' => 'nullable|array',
            'nuevos_documentos_anexos.*' => 'file|mimes:pdf|max:10240',
            'archivos_a_eliminar' => 'nullable|array', // Para manejar eliminación de archivos existentes
            'archivos_a_eliminar.*' => 'integer|exists:documento_archivos,id'
        ]);

        // Si no se sube archivo, conservar el archivo actual
        $documento->update([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'],
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],

        ]);

        // Definir carpetas base
        // $folder = 'documentos_tecnicos';
        // $folderAnexos = "$folder/documentos_anexos";

        // Eliminar archivos marcados para eliminación
        if (!empty($validated['archivos_a_eliminar'])) {
            $archivosAEliminar = $documento->archivos()->whereIn('id', $validated['archivos_a_eliminar'])->get();

            foreach ($archivosAEliminar as $archivo) {
                if (Storage::disk('public')->exists($archivo->ruta_archivo)) {
                    Storage::disk('public')->delete($archivo->ruta_archivo);
                }
                $archivo->delete();
            }
        }

        // Guardar nuevos archivos principales
        if ($request->hasFile('nuevos_documentos_principales')) {
            foreach ($request->file('nuevos_documentos_principales') as $file) {
                $documento->archivos()->create([
                    'ruta_archivo' => $this->storeFile($file, 'documentos_tecnicos'),
                    'tipo' => 'principal',
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        }

        // Guardar nuevos archivos anexos
        if ($request->hasFile('nuevos_documentos_anexos')) {
            foreach ($request->file('nuevos_documentos_anexos') as $file) {
                $documento->archivos()->create([
                    'ruta_archivo' => $this->storeFile($file, 'documentos_tecnicos/documentos_anexos'),
                    'tipo' => 'anexo',
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        }

        // Actualizar fechas en la tabla 'fechas' relacionada
        $documento->fechas()->updateOrCreate(
            ['documento_id' => $documento->id], // Buscar el registro por documento_id
            [
                'fecha_revalidacion' => $validated['fecha_revalidacion'],
                'fecha_vigencia' => $validated['fecha_vigencia'],
            ]
        );

        // Actualizar modalidades si existen
        if (!empty($validated['modalidad_id'])) {
            $documento->modalidades()->sync($validated['modalidad_id']);
        }

        // Redirigir con mensaje de éxito
        return redirect()->route($this->routeName . 'index')->with('success', 'Documento actualizado con éxito.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Documento $documento)
    {
        // Eliminar archivos asociados (principal y anexo)
        foreach ($documento->archivos as $archivo) {
            if ($archivo->ruta_archivo && Storage::disk('public')->exists($archivo->ruta_archivo)) {
                Storage::disk('public')->delete($archivo->ruta_archivo);
            }

            // Eliminar el registro del archivo en la base de datos
            $archivo->delete();
        }

        // Eliminar el documento en sí
        $documento->delete();

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento y archivos eliminados con éxito.');
    }

    public function descargarTodos(Documento $documento)
    {
        return $this->procesarDescarga($documento);
    }

    protected function procesarDescarga($documento)
    {
        $zip = new ZipArchive;
        $zipFileName = "documento-{$documento->id}-archivos-" . now()->format('YmdHis') . ".zip";
        $zipPath = storage_path("app/public/temp/{$zipFileName}");

        // Crear directorio temporal si no existe
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Carpeta para archivos principales
            foreach ($documento->archivos->where('tipo', 'principal') as $archivo) {
                $filePath = storage_path("app/public/{$archivo->ruta_archivo}");
                if (file_exists($filePath)) {
                    $nombreArchivo = $archivo->nombre_original ?: basename($archivo->ruta_archivo);
                    $zip->addFile($filePath, "Principales/{$nombreArchivo}");
                }
            }

            // Carpeta para archivos anexos
            foreach ($documento->archivos->where('tipo', 'anexo') as $archivo) {
                $filePath = storage_path("app/public/{$archivo->ruta_archivo}");
                if (file_exists($filePath)) {
                    $nombreArchivo = $archivo->nombre_original ?: basename($archivo->ruta_archivo);
                    $zip->addFile($filePath, "Anexos/{$nombreArchivo}");
                }
            }

            $zip->close();

            return response()
                ->download($zipPath)
                ->deleteFileAfterSend(true);
        }

        return back()->with('error', 'No se pudo crear el archivo ZIP');
    }

}

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
    public function index()
    {

        $documentos = Documento::with(['empresa', 'tipoDeDocumento', 'estado', 'departamento', 'modalidades'])
            ->where('nombre_documento', 'Documento Técnico') // Filtra solo los documentos técnicos
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Documento/Index', [
            'titulo' => 'Lista de Documentos Técnicos',
            'documentos' => $documentos,
            'routeName' => $this->routeName,
            'loadingResults' => false
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
            'ruta_documento' => 'nullable|file|mimes:pdf|max:5120',
            'ruta_documento_anexo' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // Definir carpeta base para documentos técnicos
        $folder = 'documentos_tecnicos';
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if (!Storage::disk('public')->exists("$folder/documentos_anexos")) {
            Storage::disk('public')->makeDirectory("$folder/documentos_anexos");
        }

        // Guardar documentos
        $rutaDocumento = $this->storeFile($request->file('ruta_documento'), "$folder");
        $rutaDocumentoAnexo = $this->storeFile($request->file('ruta_documento_anexo'), "$folder/documentos_anexos");

        // Crear documento en la base de datos
        $documento = Documento::create([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'] ?? null,
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
            'ruta_documento' => $rutaDocumento,
            'ruta_documento_anexo' => $rutaDocumentoAnexo,
        ]);
        // Guardar las fechas en la tabla 'fechas' relacionada
        $documento->fechas()->create([
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
        ]);

        // Asociar modalidades si existen
        if (!empty($validated['modalidad_id'])) {
            $documento->modalidades()->attach($validated['modalidad_id']);
        }

    

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento creado con éxito.');
    }

    /**
     * Guardar un archivo en la carpeta especificada
     */
    private function storeFile($file, $folder)
    {
        return $file ? $file->storeAs($folder, time() . '_' . $file->getClientOriginalName(), 'public') : null;
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




        return Inertia::render('Documento/Edit', [
            'titulo' => 'Editar Documento',
            'documento' => $documento,
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'tipos_documento' => $tipos_documento,
            'estados' => $estados,
            'departamentos' => $departamentos,
            'modalidades' => $modalidades,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        logger('Archivos pdf en updateControlador:', $request->allFiles());
        Log::info('Datos recibidos en update:', $request->all());

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
            'ruta_documento' => 'nullable|file|mimes:pdf|max:5120',
            'ruta_documento_anexo' => 'nullable|file|mimes:pdf|max:5120',
        ]);
        Log::info('Datos recibidos en validated--------:', $validated);
        // Crear las carpetas si no existen
        $folder = 'documentos_tecnicos';
        $rutaDocumento = $documento->ruta_documento;
        $rutaDocumentoAnexo = $documento->ruta_documento_anexo;


        if ($request->hasFile('ruta_documento')) {
            // Borrar archivo anterior si existe
            if ($rutaDocumento && Storage::disk('public')->exists($rutaDocumento)) {
                Storage::disk('public')->delete($rutaDocumento);
            }
            $rutaDocumento = $this->storeFile($request->file('ruta_documento'), "$folder");
        }

        if ($request->hasFile('ruta_documento_anexo')) {
            // Borrar archivo anterior si existe
            if ($rutaDocumentoAnexo && Storage::disk('public')->exists($rutaDocumentoAnexo)) {
                Storage::disk('public')->delete($rutaDocumentoAnexo);
            }
            $rutaDocumentoAnexo = $this->storeFile($request->file('ruta_documento_anexo'), "$folder/documentos_anexos");
        }

        // Si no se sube archivo, conservar el archivo actual
        $documento->update([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'],
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
            'ruta_documento' => $rutaDocumento,
            'ruta_documento_anexo' => $rutaDocumentoAnexo,
        ]);
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
        // Borrar los archivos asociados si existen
        if ($documento->ruta_documento && Storage::disk('public')->exists($documento->ruta_documento)) {
            Storage::disk('public')->delete($documento->ruta_documento);
        }

        if ($documento->ruta_documento_anexo && Storage::disk('public')->exists($documento->ruta_documento_anexo)) {
            Storage::disk('public')->delete($documento->ruta_documento_anexo);
        }

        // Eliminar el documento de la base de datos
        $documento->delete();

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento eliminado con éxito.');
    }

}

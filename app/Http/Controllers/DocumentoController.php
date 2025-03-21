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

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'documento.';
    }
    public function index()
    {

        $documentos = Documento::with(['empresa', 'tipoDocumento', 'estado', 'departamento', 'modalidades'])
            ->orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('Documento/Index', [
            'titulo' => 'Lista de Documentos',
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
            'titulo' => 'Crear Documento',
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
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|array|exists:modalidads,id',
            'ruta_documento' => 'nullable|file|mimes:pdf|max:5120', 
            'ruta_documento_anexo' => 'nullable|file|mimes:pdf|max:5120', 
        ]);

        // Detección de tipo de documento (legal o técnico)
        $tipoDocumento = strtolower(trim($request->input('nombre_documento')));

        if (str_contains($tipoDocumento, 'documento legal')) {
            $folder = 'documentos_legales';
        } elseif (str_contains($tipoDocumento, 'documento técnico')) {
            $folder = 'documentos_tecnicos';
        } else {
            $folder = 'otros_documentos';
        }
       
        Storage::disk('public')->makeDirectory("$folder");
        Storage::disk('public')->makeDirectory("$folder/documentos_anexos");

        $rutaDocumento = null;
        if ($request->hasFile('ruta_documento')) {
            
            $rutaDocumento = $request->file('ruta_documento')->storeAs(
                "$folder", 
                time() . '_' . $request->file('ruta_documento')->getClientOriginalName(), 
                'public'
            );
        }
        
        $rutaDocumentoAnexo = null;
        if ($request->hasFile('ruta_documento_anexo')) {
            // Generar nombre único y guardar el archivo
            $rutaDocumentoAnexo = $request->file('ruta_documento_anexo')->storeAs(
                "$folder/documentos_anexos", 
                time() . '_' . $request->file('ruta_documento_anexo')->getClientOriginalName(), 
                'public'
            );
        }
        
        $documento = Documento::create([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'],
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
            'ruta_documento' => $rutaDocumento ?? null,
            'ruta_documento_anexo' => $rutaDocumentoAnexo ?? null,
        ]);
        
        if ($validated['modalidad_id']) {
            $documento->modalidades()->attach($validated['modalidad_id']);
        }
        return redirect()->route($this->routeName . 'index')->with('success', 'Documento creado con éxito.');
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
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $tipos_documento = TipoDeDocumento::select('id', 'nombre_documento as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();
        $departamentos = Departamento::select('id', 'nombre_departamento as name')->get();
        // Cargar las modalidades asociadas al documento
        $modalidadesAsociadas  = Modalidad::select('id', 'nombre_modalidad as name')
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

    // Determinar el tipo de documento y carpeta
    $tipoDocumento = strtolower(trim($request->input('nombre_documento')));
    $folder = str_contains($tipoDocumento, 'documento legal') ? 'documentos_legales' :
             (str_contains($tipoDocumento, 'documento técnico') ? 'documentos_tecnicos' : 'otros_documentos');

    // Crear las carpetas si no existen
    $folderPath = "public/$folder";
    $subFolderPath = "$folderPath/documentos_anexos";
    if (!Storage::disk('public')->exists($folderPath)) {
        Storage::disk('public')->makeDirectory($folderPath);
    }
    if (!Storage::disk('public')->exists($subFolderPath)) {
        Storage::disk('public')->makeDirectory($subFolderPath);
    }

    // Actualizar documento principal si se sube un nuevo archivo
    if ($request->hasFile('ruta_documento')) {
        if ($documento->ruta_documento) {
            Storage::disk('public')->delete($documento->ruta_documento);
        }
        $documento->ruta_documento = $request->file('ruta_documento')->storeAs($folderPath, time() . '_' . $request->file('ruta_documento')->getClientOriginalName(), 'public');
    }

    // Actualizar documento anexo si se sube un nuevo archivo
    if ($request->hasFile('ruta_documento_anexo')) {
        if ($documento->ruta_documento_anexo) {
            Storage::disk('public')->delete($documento->ruta_documento_anexo);
        }
        $documento->ruta_documento_anexo = $request->file('ruta_documento_anexo')->storeAs($subFolderPath, time() . '_' . $request->file('ruta_documento_anexo')->getClientOriginalName(), 'public');
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
    ]);

    // Sincronizar las modalidades si existe alguna
    if (isset($validated['modalidad_id'])) {
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
        $documento->delete();

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento eliminado con éxito.');
    }
}

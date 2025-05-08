<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\DocumentoLegal;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Modalidad;
use App\Models\TipoDeDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\DocumentoArchivo;



class DocumentoLegalController extends Controller
{
    private string $routeName;
    protected string $module = 'documentoLegal';


    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'documento-legal.';
        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);

    }
    public function index()
    {

        $documentos = DocumentoLegal::with(['empresa', 'tipoDeDocumento', 'departamento'])
            ->where('nombre_documento', 'Documento Legal') // Filtra solo los documentos técnicos
            ->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render('DocumentoLegal/Index', [
            'titulo' => 'Lista de Documentos Legales',
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
        // $estados = Estado::select('id', 'nombre as name')->get();
        $departamentos = Departamento::select('id', 'nombre_departamento as name')->get();
        //$modalidades = Modalidad::select('id', 'nombre_modalidad as name')->get();

        return Inertia::render('DocumentoLegal/Create', [
            'titulo' => 'Crear Documento Legal',
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'tipos_documento' => $tipos_documento,
            //'estados' => $estados,
            'departamentos' => $departamentos,
            //'modalidades' => $modalidades,
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
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'ruta_documento' => 'required|array',
            'ruta_documento.*' => 'file|mimes:pdf|max:10240',
            'ruta_documento_anexo' => 'nullable|array',
            'ruta_documento_anexo.*' => 'file|mimes:pdf|max:10240',
        ]);

        // Definir carpeta base para documentos técnicos
        $folder = 'documentos_legales';
        $folderAnexos = "$folder/documentos_anexos";

        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
        if (!Storage::disk('public')->exists("$folder/documentos_anexos")) {
            Storage::disk('public')->makeDirectory("$folder/documentos_anexos");
        }

        // // Guardar documentos
        // $rutaDocumento = $this->storeFile($request->file('ruta_documento'), "$folder");
        // $rutaDocumentoAnexo = $this->storeFile($request->file('ruta_documento_anexo'), "$folder/documentos_anexos");

        // Crear documento en la base de datos
        $documento = DocumentoLegal::create([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
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
    public function edit(DocumentoLegal $documentoLegal)
    {
        //dd($documento);  // Esto te permite ver qué contiene el objeto.
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $tipos_documento = TipoDeDocumento::select('id', 'nombre_documento as name')->get();
        $departamentos = Departamento::select('id', 'nombre_departamento as name')->get();

        // Separar archivos principales y anexos
        $archivosPrincipales = $documentoLegal->archivos->where('tipo', 'principal')->values();
        $archivosAnexos = $documentoLegal->archivos->where('tipo', 'anexo')->values(); //forzar para empezar indice des 0 y ser array



        return Inertia::render('DocumentoLegal/Edit', [
            'titulo' => 'Editar Documento Legal',
            'documento' => $documentoLegal,
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'tipos_documento' => $tipos_documento,
            'departamentos' => $departamentos,
            'archivosPrincipales' => $archivosPrincipales,
            'archivosAnexos' => $archivosAnexos
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DocumentoLegal $documentoLegal)
    {
        // logger('Archivos pdf en updateControlador:', $request->allFiles());
        // Log::info('Datos recibidos en update:', $request->all());

        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_de_documento_id' => 'required|exists:tipo_de_documentos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'nuevos_documentos_anexos' => 'nullable|array',
            'nuevos_documentos_anexos.*' => 'file|mimes:pdf|max:10240',
            'archivos_a_eliminar' => 'nullable|array', // Para manejar eliminación de archivos existentes
            'archivos_a_eliminar.*' => 'integer|exists:documento_archivos,id'
        ]);
        
        // Si no se sube archivo, conservar el archivo actual
        $documentoLegal->update([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
          
        ]);

         // Eliminar archivos marcados para eliminación
         if (!empty($validated['archivos_a_eliminar'])) {
            $archivosAEliminar = $documentoLegal->archivos()->whereIn('id', $validated['archivos_a_eliminar'])->get();

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
                $documentoLegal->archivos()->create([
                    'ruta_archivo' => $this->storeFile($file, 'documentos_legales'),
                    'tipo' => 'principal',
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        }

        // Guardar nuevos archivos anexos
        if ($request->hasFile('nuevos_documentos_anexos')) {
            foreach ($request->file('nuevos_documentos_anexos') as $file) {
                $documentoLegal->archivos()->create([
                    'ruta_archivo' => $this->storeFile($file, 'documentos_legales/documentos_anexos'),
                    'tipo' => 'anexo',
                    'nombre_original' => $file->getClientOriginalName()
                ]);
            }
        }

        // Actualizar fechas en la tabla 'fechas' relacionada
        $documentoLegal->fechas()->updateOrCreate(
            ['documento_id' => $documentoLegal->id], // Buscar el registro por documento_id
            [
                'fecha_revalidacion' => $validated['fecha_revalidacion'],
                'fecha_vigencia' => $validated['fecha_vigencia'],
            ]
        );



        // Redirigir con mensaje de éxito
        return redirect()->route($this->routeName . 'index')->with('success', 'Documento actualizado con éxito.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentoLegal $documentoLegal)
    {
        // Eliminar archivos asociados (principal y anexo)
        foreach ($documentoLegal->archivos as $archivo) {
            if ($archivo->ruta_archivo && Storage::disk('public')->exists($archivo->ruta_archivo)) {
                Storage::disk('public')->delete($archivo->ruta_archivo);
            }

            // Eliminar el registro del archivo en la base de datos
            $archivo->delete();
        }

        // Eliminar el documento en sí
        $documentoLegal->delete();

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento eliminado con éxito.');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Modalidad;
use App\Models\TipoDeDocumento;
use Illuminate\Http\Request;
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
        $empresas = Empresa::all();
        $tipos_documento = TipoDeDocumento::all();
        $estados = Estado::all();
        $departamentos = Departamento::all();
        $modalidades = Modalidad::all();

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
        /*  $validated = $request->validate([
              'nombre_documento' => 'required|string|max:255',
              'empresa_id' => 'required|exists:empresas,id',
              'tipo_documento_id' => 'required|exists:tipo_de_documentos,id',
              'estado_id' => 'nullable|exists:estados,id',
              'departamento_id' => 'required|exists:departamentos,id',
              'fecha_revalidacion' => 'required|date',
              'fecha_vigencia' => 'required|date',
              'modalidad_id' => 'nullable|exists:modalidades,id',
              'ruta_documento' => 'required|string|max:255',
              'ruta_documento_anexo' => 'required|string|max:255',
          ]);

          Documento::create($validated); */

        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_de_documento_id' => 'required|exists:tipo_de_documentos,id',
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|exists:modalidads,id',
            'documento' => 'nullable|file|mimes:pdf|max:2048', // Asegúrate de validar el archivo
            'documento_anexo' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        // 🔎 Detección de tipo de documento (legal o técnico)
        $tipoDocumento = strtolower($request->input('nombre_documento'));

        if (str_contains($tipoDocumento, 'Documento Legal')) {
            $folder = 'documentos_legales';
        } elseif (str_contains($tipoDocumento, 'Documento Técnico')) {
            $folder = 'documentos_tecnicos';
        } else {
            $folder = 'otros_documentos';
        }

        // Crear carpeta si no existe
        Storage::makeDirectory("$folder/documentos_anexos");

        // ✅ Guardar documento principal
        if ($request->hasFile('documento')) {
            $rutaDocumento = $request->file('documento')->storeAs(
                "$folder",
                time() . '_' . $request->file('documento')->getClientOriginalName()
            );
        }

        // ✅ Guardar documento anexo (si existe)
        $rutaDocumentoAnexo = null;
        if ($request->hasFile('documento_anexo')) {
            $rutaDocumentoAnexo = $request->file('documento_anexo')->storeAs(
                "$folder/documentos_anexos",
                time() . '_' . $request->file('documento_anexo')->getClientOriginalName()
            );
        }

        // ✅ Crear el registro en la base de datos
        Documento::create([
            'nombre_documento' => $validated['nombre_documento'],
            'empresa_id' => $validated['empresa_id'],
            'tipo_de_documento_id' => $validated['tipo_de_documento_id'],
            'estado_id' => $validated['estado_id'],
            'departamento_id' => $validated['departamento_id'],
            'fecha_revalidacion' => $validated['fecha_revalidacion'],
            'fecha_vigencia' => $validated['fecha_vigencia'],
            'modalidad_id' => $validated['modalidad_id'],
            'ruta_documento' => $rutaDocumento ?? null,
            'ruta_documento_anexo' => $rutaDocumentoAnexo ?? null,
        ]);

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
        $empresas = Empresa::all();
        $tipos_documento = TipoDeDocumento::all();
        $estados = Estado::all();
        $departamentos = Departamento::all();
        $modalidades = Modalidad::all();

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
        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_documento_id' => 'required|exists:tipo_de_documentos,id',
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|exists:modalidades,id',
            'ruta_documento' => 'required|string|max:255',
            'ruta_documento_anexo' => 'required|string|max:255',
        ]);

        $documento->update($validated);

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

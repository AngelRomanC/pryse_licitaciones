<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Modalidad;
use App\Models\TipoDeDocumento;
use Illuminate\Http\Request;
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

        return Inertia::render('Documentos/Index', [
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

        return Inertia::render('Documentos/Create', [
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
            'tipo_documento_id' => 'required|exists:tipo_de_documentos,id',
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|exists:modalidades,id',
            'ruta_documento' => 'required|string|max:255',
            'ruta_documento_anexo' => 'required|string|max:255',
        ]);

        Documento::create($validated);

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

        return Inertia::render('Documentos/Edit', [
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

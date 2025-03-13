<?php

namespace App\Http\Controllers;

use App\Models\TipoDeDocumento;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TipoDeDocumentoController extends Controller
{
    private string $routeName;

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'tipo-de-documento.';
    }
    public function index()
    {
        // Obtener todas las empresas ordenadas por ID y paginadas
        $tipoDeDocumentos = TipoDeDocumento::orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render("TipoDeDocumento/Index", [
            'titulo' => 'Lista de Documentos',
            'tipoDeDocumentos' => $tipoDeDocumentos,
            'routeName' => $this->routeName,
            'loadingResults' => false
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("TipoDeDocumento/Create", [
            'titulo' => 'Detalles de la Documento',
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:50',
        ]);

        TipoDeDocumento::create($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoDeDocumento $tipoDeDocumento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoDeDocumento $tipoDeDocumento)
    {
        return Inertia::render("TipoDeDocumento/Edit", [
            'titulo' => 'Editar Documento',
            'tipoDeDocumento' => $tipoDeDocumento,
            'routeName' => $this->routeName
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoDeDocumento $tipoDeDocumento)
    {
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:50',           
        ]);
        $tipoDeDocumento->update($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Documento actualizado con éxito.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoDeDocumento $tipoDeDocumento)
    {
        $tipoDeDocumento->delete();
    }
}

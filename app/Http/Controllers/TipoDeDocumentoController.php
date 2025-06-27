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
    public function index(Request $request)
    {
        $query = TipoDeDocumento::query();

        // Si se mandó texto para buscar, aplica el filtro por nombre
        if ($request->filled('search')) {
            $query->where('nombre_documento', 'like', '%' . $request->search . '%');
        }

        // Paginar y ordenar
        $tipoDeDocumentos = $query->orderBy('id', "desc")
            ->paginate(8)
            ->withQueryString();

        return Inertia::render("TipoDeDocumento/Index", [
            'titulo' => 'Lista de Documentos',
            'tipoDeDocumentos' => $tipoDeDocumentos,
            'routeName' => $this->routeName,
            'filters' => $request->only('search'), // 👈 importante para que el filtro persista en el input
            'loadingResults' => false
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("TipoDeDocumento/Create", [
            'titulo' => 'Detalles del Documento',
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
            'nombre_documento' => 'required|string|max:100',
        ]);

        TipoDeDocumento::create($validated);
        if ($request->filled('redirect')) {
            return redirect($request->input('redirect'))
                ->with('success', 'Empresa creada correctamente');
        }

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

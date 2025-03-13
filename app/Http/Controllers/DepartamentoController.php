<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartamentoController extends Controller
{
    private string $routeName;

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'departamento.';
    }
    public function index()
    {
        // Obtener todas las empresas ordenadas por ID y paginadas
        $departamentos = Departamento::orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render("Departamento/Index", [
            'titulo' => 'Lista de Áreas',
            'departamentos' => $departamentos,
            'routeName' => $this->routeName           
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Departamento/Create", [
            'titulo' => 'Detalles de Área',
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
            'nombre_departamento' => 'required|string|max:50',
        ]);

        Departamento::create($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Área creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Departamento $departamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departamento $departamento)
    {
        return Inertia::render("Departamento/Edit", [
            'titulo' => 'Editar área',
            'departamento' => $departamento,
            'routeName' => $this->routeName
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'nombre_departamento' => 'required|string|max:50',           
        ]);
        $departamento->update($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Área actualizada con éxito.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
    }
}

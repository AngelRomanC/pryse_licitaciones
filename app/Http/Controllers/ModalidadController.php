<?php

namespace App\Http\Controllers;

use App\Models\Modalidad;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ModalidadController extends Controller
{
    private string $routeName;

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'modalidad.';
    }
    public function index(Request $request)
    {
        $query = Modalidad::query();

        // Aplicar filtro si se proporciona texto de búsqueda
        if ($request->filled('search')) {
            $query->where('nombre_modalidad', 'like', '%' . $request->search . '%');
        }

        $modalidades = $query->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render("Modalidad/Index", [
            'titulo' => 'Lista de Modalidades',
            'modalidades' => $modalidades,
            'routeName' => $this->routeName,
            'filters' => $request->only('search'),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Modalidad/Create", [
            'titulo' => 'Detalles de Modalidades',
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
            'nombre_modalidad' => 'required|string|max:100',
        ]);

        Modalidad::create($validated);

        if ($request->filled('redirect')) {
            return redirect($request->input('redirect'))
                ->with('success', 'Empresa creada correctamente');
        }

        return redirect()->route($this->routeName . 'index')->with('success', 'Modalidad creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Modalidad $modalidad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Modalidad $modalidad)
    {
        return Inertia::render("Modalidad/Edit", [
            'titulo' => 'Editar modalidad',
            'modalidad' => $modalidad,
            'routeName' => $this->routeName
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Modalidad $modalidad)
    {
        $validated = $request->validate([
            'nombre_modalidad' => 'required|string|max:50',
        ]);
        $modalidad->update($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Modalidad actualizada con éxito.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modalidad $modalidad)
    {
        $modalidad->delete();
    }
}

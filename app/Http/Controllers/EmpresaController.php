<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;


class EmpresaController extends Controller
{
    private string $routeName; //si sirve

    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'empresa.';
    }
    public function index()
    {
        // Obtener todas las empresas ordenadas por ID y paginadas
        $empresas = Empresa::orderBy('id')
            ->paginate(8)
            ->withQueryString();

        return Inertia::render("Empresa/Index", [
            'titulo' => 'Lista de Empresas',  // Título de la vista
            'empresas' => $empresas,  // Datos de las empresas
            'routeName' => $this->routeName,
            'loadingResults' => false
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("Empresa/Create", [
            'titulo' => 'Detalles de la Empresa',
            'routeName' => $this->routeName,
        ]);
    }


    public function store(Request $request)
    {
        //logger('Redirect recibido:', ['redirect' => $request->input('redirect')]);

        // Validar los datos recibidos
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:100',
            'direccion' => 'required|string|max:100',
            //'telefono' => 'required|string|max:50|unique:empresas,telefono',
            'telefono' => 'required|digits:10|unique:empresas,telefono',
            'email' => 'required|email|max:50|unique:empresas,email',
        ], [
            'telefono.unique' => 'El número de teléfono ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
        ]);

        Empresa::create($validated);

        if ($request->filled('redirect')) { // Usa "filled" verificar que no esté vacío
            return redirect($request->input('redirect'))
                ->with('success', 'Empresa creada correctamente');
        }

        return redirect()->route($this->routeName . 'index')->with('success', 'Empresa creada con éxito.');
    }


    public function show(Empresa $empresa)
    {
        //
    }


    public function edit(Empresa $empresa)
    {
        return Inertia::render("Empresa/Edit", [
            'titulo' => 'Editar Empresa',
            'empresa' => $empresa,
            'routeName' => $this->routeName
        ]);
    }


    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'descripcion' => 'required|string|max:50',
            'direccion' => 'required|string|max:50',
            'telefono' => 'required|string|max:50',
            'email' => 'required|string|max:50',
        ]);
        $empresa->update($validated);

        return redirect()->route($this->routeName . 'index')->with('success', 'Empresa actualizada con éxito.');
    }


    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route($this->routeName . 'index')->with('success', 'Empresa eliminada con éxito.');
    }
}

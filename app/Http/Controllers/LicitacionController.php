<?php

namespace App\Http\Controllers;

use App\Models\Licitacion;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\Estado;
use Inertia\Inertia;


class LicitacionController extends Controller
{
    private string $routeName;
    protected string $module = 'licitacion';
    public function __construct()
    {
        $this->middleware('auth');
        $this->routeName = 'documento.';
        // $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        // $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        // $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
        // $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }
    public function index()
    {
        $licitaciones = Licitacion::with(['empresa', 'estado'])->get();

        return Inertia::render('Licitacion/Index', [
            'titulo' => 'Lista de Licitaciones',
            'documentos' => $licitaciones,
            'routeName' => $this->routeName,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $empresas = Empresa::select('id', 'nombre as name')->get();
        $estados = Estado::select('id', 'nombre as name')->get();

        return Inertia::render('Licitacion/Create', [
            'titulo' => 'Crear Documento Técnico',
            'routeName' => $this->routeName,
            'empresas' => $empresas,
            'estados' => $estados,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Licitacion $licitacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Licitacion $licitacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Licitacion $licitacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Licitacion $licitacion)
    {
        //
    }
}

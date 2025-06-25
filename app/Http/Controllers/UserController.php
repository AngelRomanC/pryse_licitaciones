<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUsuarioRequest;

use App\Notifications\CredencialesEstudianteNotification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
//use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Modulo;
use Inertia\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    protected string $routeName;
    protected string $source;
    protected string $module = 'usuarios';
    protected User $model;

    public function __construct()
    {
        $this->routeName = "usuarios.";
        $this->source    = "Seguridad/Usuarios/";
        $this->model     = new User();
         $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
         $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
         $this->middleware("permission:{$this->module}.update")->only(['edit', 'update']);
         $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
        
    }

    public function index(Request $request): Response
    {

        
        //$admin = User::where('role', 'Admin')->get();
        $admin = User::where('role', 'Admin')->paginate(8); // Puedes ajustar el 10 al número de elementos por página que desees


        $usuarios = $this->model::with('roles')
            ->orderBy('id')
            ->paginate(30)
            ->withQueryString();



        return Inertia::render("{$this->source}Index", [
            'titulo'   => ' Usuarios Admin',
            'usuarios' => $usuarios,
            'admin' => $admin,
            'profiles' => Role::get(['id', 'name']),
            'routeName' => $this->routeName,
            'loadingResults' => false,
        ]);
    }

    public function create()
    {
        return Inertia::render("Seguridad/Usuarios/Create", [
            'titulo'      => 'Agregar Usuario Admin',
            'routeName'      => $this->routeName,
            'roles' => Role::pluck('name'),
        ]);
    }

    public function store(StoreUserRequest $request)

    {
        
        $newUser = user::create([
            'name' => $request->input('name'),
            'apellido_paterno' => $request->input('apellido_paterno'),
            'apellido_materno' => $request->input('apellido_materno'),
            'numero' => $request->input('numero'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),


        ])->assignRole($request['role']);

        $newUser->notify(instance: new CredencialesEstudianteNotification($request->email, $request->password));

        return redirect()->route("usuarios.index")->with('success', 'Usuario Admin generado con éxito');
    }






    public function edit($id)
    {

        $usuario = User::find($id);

        return Inertia::render("Seguridad/Usuarios/Edit", [
            'titulo'   => 'Modificar Usuario Admin',
            'usuario'  => $usuario,
            'routeName' => $this->routeName,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            abort(404, 'Usuario no encontrado');
        }

        // Actualiza los datos del usuario
        $usuario->update($request->all());

      

        return redirect()->route("usuarios.index")->with('success', '¡Usuario Admin actualizado correctamente!');
    }



    public function destroy($id)
    {

        $usuario = User::find($id);
        $usuario->delete();



        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado con éxito');
    }
}

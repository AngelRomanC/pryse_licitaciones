<?php
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DocumentoLegalController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\LicitacionController;
use App\Http\Controllers\ModalidadController;
use App\Http\Controllers\TipoDeDocumentoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioGeneralController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DashboardController;

//use App\Http\Controllers\ModuleController; //checar si se ocupan borrado
//use App\Http\Controllers\PerfilesController;  borardpo

//use App\Http\Controllers\PermissionController;

use App\Http\Controllers\ProfileController; //perfil de usuario


use App\Http\Controllers\RespaldoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\NotificationController;



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
// Route::get('/dashboard', function () {

//     return Inertia::render('Dashboard', [
//         'users' => User::all(),

//     ]);
// })->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');



Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

   
    // Seguridad
   // Route::resource('module', ModuleController::class)->parameters(['module' => 'module']);
    //Route::resource('permissions', PermissionController::class)->names('permissions');
    //Route::resource('perfiles', PerfilesController::class)->parameters(['perfiles' => 'perfiles']);

    //Usuarios
    Route::resource('usuarios', controller: UserController::class)->parameters(['usuarios' => 'usuarios']);
    Route::get('/perfil', [UserController::class, 'perfil'])->name('usuarios.perfil');
    Route::post('actualizarPerfil', [UserController::class, 'updatePerfil'])->name('usuarios.update-perfil');

  
    //Alumno
    Route::resource('alumno', AlumnoController::class)->parameters(['alumno' => 'alumno']);

   //BACKUP
    Route::resource('respaldo', RespaldoController::class)->parameters(['respaldo' => 'respaldo']);
    Route::get('respaldo-restauracion/{filename}', [RespaldoController::class, 'restaurarRespaldo'])->name('restaurarRespaldo');
    Route::get('respaldo-descarga/{filename}', [RespaldoController::class, 'descargaRespaldo'])->name('descargaRespaldo');
    Route::get('respaldo-eliminar/{filename}', [RespaldoController::class, 'eliminarRespaldo'])->name('eliminarRespaldo');

    
    //Notificaciones 
    //Route::get('/notificaciones', [NotificationController::class, 'index']);
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notificaciones/{id}/marcar-como-leida', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notificaciones/count-no-leidas', [NotificationController::class, 'NotificationCount'])->name('notifications.unreadCount');   
   
   //Licitaciones
    Route::resource('empresa', controller: EmpresaController::class);    
    Route::resource('tipo-de-documento', controller: TipoDeDocumentoController::class);
    Route::resource('departamento', controller: DepartamentoController::class);
    Route::resource('modalidad', ModalidadController::class);   
    Route::resource('documento', controller: DocumentoController::class);
    Route::resource('documento-legal', controller: DocumentoLegalController::class);
    Route::resource('usuarios-sistema', controller: UsuarioGeneralController::class);  

    // routes/web.php
    Route::get('/documento/{documento}/descargar-todos', [DocumentoController::class, 'descargarTodos'])->name('documento.descargar-todos');
    Route::get('/documento-legal/{documento}/descargar-todos', [DocumentoLegalController::class, 'descargarTodos'])->name('documento-legal.descargar-todos');
    
    Route::resource('licitacion', LicitacionController::class);
    Route::get('/empresa/{empresa}/documentos', [LicitacionController::class, 'getDocumentosByEmpresa']);
    
    Route::get('/licitaciones/{licitacion}/descargar-expediente', [LicitacionController::class, 'descargarExpediente'])->name('licitaciones.descargar');


});

require __DIR__ . '/auth.php';

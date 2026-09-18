<?php

use App\Exports\GuardavidasExport;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Auth\ForcedPasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CambioDeTurnoController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FrancoExcepcionController;
use App\Http\Controllers\FrancoIntercambioController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/ping', function () {
    return response('', 204);
});

Route::middleware(['auth', 'force.password'])->group(function () {
    /* Fuerzo a que actualice la contraseña la 1era vez que se loguea */
    Route::get('/force-password', [ForcedPasswordController::class, 'edit'])
        ->middleware('auth')
        ->name('password.force');

    Route::post('/force-password', [ForcedPasswordController::class, 'update'])
        ->middleware('auth')
        ->name('password.force.update');

    // Ruta que actualiza los datos del guardavida (turno, puesto, etc.)
    Route::post('/guardavida/setup', [GuardavidaController::class, 'setup'])
        ->name('guardavida.setup.store');
    /**/

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/activeCamera', [QrController::class, 'activeCamera'])->name('activeCamera');

    Route::resource('bandera', App\Http\Controllers\BanderaController::class);
    Route::resource('intervencion', App\Http\Controllers\IntervencionController::class);
    Route::resource('novedad-de-material', App\Http\Controllers\NovedadMaterialController::class);

    Route::resource('guardavida', App\Http\Controllers\GuardavidaController::class);
    Route::patch('usuario-toggle/{user}', [UserController::class, 'toggle'])->name('user.toggle');
    Route::get('guardavidas-deshabilitados', [GuardavidaController::class, 'getAllDisabled'])->name('guardavidas.disabled');
    Route::get('/get-all-guardavidas', [GuardavidaController::class, 'getAll']);

    Route::resource('licencia', App\Http\Controllers\LicenciaController::class)->parameters(['licencia' => 'licencia']);
    Route::resource('cambio-de-turno', App\Http\Controllers\CambioDeTurnoController::class);

    // Excel
    Route::get('/guardavidas/export', function () {
        return Excel::download(new GuardavidasExport, 'guardavidas.xlsx');
    })->name('guardavidas.export');
    Route::get('/export/playas', [ExportController::class, 'exportPorPlaya'])
        ->name('export.playas');

    /* Nuevas rutas */
    Route::get('/my-profile', [GuardavidaController::class, 'myProfile'])->name('guardavida.myProfile');
    Route::put('/my-profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');
    Route::get('/guardavida/{guardavida}/perfil', [GuardavidaController::class, 'showProfile'])->name('guardavida.profile');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // pasar a moddleware admin
    // Route::middleware(['auth', 'can:admin'])
    Route::put('/update-user/{user}', [RegisteredUserController::class, 'updateUserByAdmin'])->name('user.update');
    Route::put('/update-rol/{user}', [GuardavidaController::class, 'updateUserRol'])->name('rol.update');

    //  NUEVAS RUTAS PARA PERFILES (dentro del middleware)
    Route::get('/profile', [GuardavidaController::class, 'myProfile'])->name('guardavida.myProfile');

    Route::get('/guardavida/{guardavida}/perfil', [GuardavidaController::class, 'showProfile'])->name('guardavida.profile');
    Route::put('/profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');

    // listado de cambios de turno
    Route::get('turnos', [CambioDeTurnoController::class, 'indexAdmin'])->name('cambio-de-turno.index');

    // obtener puestos para renderizar con balnearios en la vista del template al momneto de seleccionar o modificar
    /* Route::get('/puestos-por-playa/{playa_id}', [GuardavidaController::class, 'obtenerPuestos'])
         ->name('puestos.por.playa');*/
    Route::get('/puestos-por-playa/{id}', [GuardavidaController::class, 'obtenerPuestos']);

    // Listado general (admin)
    Route::get('asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');

    // Historial individual por guardavida
    Route::get('asistencias/{id}', [AsistenciaController::class, 'asistenciasPorGuardavida'])->name('asistencias.guardavida');

    // Cambios puntuales de franco cargados directo por encargado/admin (caso
    // excepcional/corrección) — el día franco fijo lo configura el propio
    // guardavida desde su perfil (guardavida.updateProfile).
    Route::post('guardavida/{guardavida}/franco-excepcion', [FrancoExcepcionController::class, 'store'])->name('franco-excepcion.store');
    Route::delete('franco-excepcion/{francoExcepcion}', [FrancoExcepcionController::class, 'destroy'])->name('franco-excepcion.destroy');

    // Intercambio de franco entre guardavidas (self-service, requiere que el
    // compañero acepte). Es puntual: no modifica el dia_franco fijo de nadie.
    Route::get('/mis-cambios-de-franco', [FrancoIntercambioController::class, 'index'])->name('franco-intercambio.index');
    Route::post('/franco-intercambio', [FrancoIntercambioController::class, 'store'])->name('franco-intercambio.store');
    Route::post('/franco-intercambio/{francoIntercambio}/aceptar', [FrancoIntercambioController::class, 'aceptar'])->name('franco-intercambio.aceptar');
    Route::post('/franco-intercambio/{francoIntercambio}/rechazar', [FrancoIntercambioController::class, 'rechazar'])->name('franco-intercambio.rechazar');
    Route::delete('/franco-intercambio/{francoIntercambio}', [FrancoIntercambioController::class, 'cancelar'])->name('franco-intercambio.cancelar');

    // para la seccion de "mis asistencias" cerca de "ver perfil"
    Route::get('/mis-asistencias', [AsistenciaController::class, 'misAsistencias'])
        ->name('guardavida.misAsistencias');

    // Gestión de permisos por rol. Gateado por el permiso abm_roles_y_permisos,
    // que hoy solo tiene el rol superadmin (ver RolesYPermisosSeeder) — un admin
    // normal no lo tiene y por lo tanto no puede acceder a estas rutas.
    Route::middleware('can:abm_roles_y_permisos')->group(function () {
        Route::get('/permisos', [PermissionController::class, 'index'])->name('permisos.index');
        Route::put('/permisos', [PermissionController::class, 'update'])->name('permisos.update');
    });
});

// Ruta para obtener el Token Bearer para ser usado en el QR
// Ademas guarda Id_user para casos sin wifi.
Route::post('/loginIdUser', [ApiAuthController::class, 'login'])->name('loginIdUser');

Route::get('/clear-laravel-cache', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'CACHE LIMPIADA ✔';
});

require __DIR__.'/auth.php';

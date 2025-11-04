<?php
use App\Http\Controllers\QrController;
use App\Exports\GuardavidasExport;
use App\Exports\IntervencionesExport;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\CambioDeTurnoController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');


Route::middleware('auth')->group(function () {
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

    //Excel
    Route::get('/guardavidas/export', function () {
        return Excel::download(new GuardavidasExport, 'guardavidas.xlsx');
    })->name('guardavidas.export');
    Route::get('/export/playas', [ExportController::class, 'exportPorPlaya'])
        ->name('export.playas');

    /*Nuevas rutas*/
    Route::get('/my-profile', [GuardavidaController::class, 'myProfile'])->name('guardavida.myProfile');
    Route::put('/my-profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');
    Route::get('/guardavida/{guardavida}/perfil', [GuardavidaController::class, 'showProfile'])->name('guardavida.profile');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //pasar a moddleware admin
    //Route::middleware(['auth', 'can:admin'])
    Route::put('/update-user/{user}', [RegisteredUserController::class, 'updateUserByAdmin'])->name('user.update');
    Route::put('/update-rol/{user}', [GuardavidaController::class, 'updateUserRol'])->name('rol.update');

    //  NUEVAS RUTAS PARA PERFILES (dentro del middleware)
    Route::get('/profile', [GuardavidaController::class, 'myProfile']) ->name('guardavida.myProfile');

    Route::put('/profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');

    Route::get('/guardavida/{guardavida}/perfil', [GuardavidaController::class, 'showProfile'])->name('guardavida.profile');


    //listado de cambios de turno
    Route::get('/admin/turnos', [CambioDeTurnoController::class, 'indexAdmin'])->name('cambio-de-turno.index');

        // Listado general (admin)
    Route::get('/admin/asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index');

    // Historial individual por guardavida
    Route::get('/admin/asistencias/{id}', [AsistenciaController::class, 'asistenciasPorGuardavida'])->name('asistencias.guardavida');

    //para la seccion de "mis asistencias" cerca de "ver perfil"
    Route::get('/mis-asistencias', [AsistenciaController::class, 'misAsistencias'])
        ->name('guardavida.misAsistencias');

    //para  ir a la seccion de descarga  del excel de asistencias y aplicar filtros(puestos,dias,todos)
    Route::get("guardavidas/excel", [AsistenciaController::class, 'guardavidasPanelExcelAsistencias'])->name('guardavidas.excel');
    //para exportar excel de asistencias desde el panel de asistencias
    //Route::get("/excel", [AsistenciaController::class, 'descargar']);
   // Route::get('/admin/asistencias/export-dia', [AsistenciaController::class, 'index'])
     //   ->name('asistencias.exportDia');
});

// Ruta para obtener el Token Bearer para ser usado en el QR
// Ademas guarda Id_user para casos sin wifi.
Route::post('/loginIdUser', [ApiAuthController::class, 'login'])->name('loginIdUser');



require __DIR__.'/auth.php';


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
use App\Http\Controllers\PostulacionAdminController;
use App\Http\Controllers\PostulacionController;
use App\Http\Controllers\PostulacionSeleccionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/ping', function () {
    return response('', 204);
});

Route::middleware(['auth', 'force.password', 'postulante.redirect'])->group(function () {
    /* Fuerzo a que actualice la contraseña la 1era vez que se loguea */
    Route::get('/force-password', [ForcedPasswordController::class, 'edit'])
        ->middleware('auth')
        ->name('password.force');

    Route::post('/force-password', [ForcedPasswordController::class, 'update'])
        ->middleware('auth')
        ->name('password.force.update');

    // Área del postulante — un postulante no tiene ningún permiso asignado
    // (ver RolesYPermisosSeeder), así que ya queda bloqueado de casi todo
    // por el propio sistema de @can/Policy; postulante.redirect (arriba)
    // se encarga de mandarlo para acá si intenta ir a cualquier otro lado.
    Route::get('/postulacion/paso/{paso}', [PostulacionController::class, 'paso'])->whereNumber('paso')->name('postulacion.paso');
    Route::post('/postulacion/paso/{paso}', [PostulacionController::class, 'guardar'])->whereNumber('paso')->name('postulacion.guardar');
    Route::post('/postulacion/enviar', [PostulacionController::class, 'enviar'])->name('postulacion.enviar');
    Route::get('/postulacion/{postulacion}/documento/{tipo}', [PostulacionController::class, 'documento'])->name('postulacion.documento');
    Route::get('/postulacion/modelo-declaracion/{temporada}', [PostulacionController::class, 'modeloDeclaracion'])->name('postulacion.modelo-declaracion');

    // Revisión de inscripciones por el admin. Nombres `postulaciones.*` (plural)
    // a propósito: NO coinciden con `postulacion.*`, así RedirectPostulante no
    // deja pasar a un postulante por estas rutas.
    Route::get('/postulaciones', [PostulacionAdminController::class, 'index'])->name('postulaciones.index');
    // Selección en lote (Fase 5). Van ANTES de /postulaciones/{postulacion} para que "seleccion" no se tome como un id.
    Route::get('/postulaciones/seleccion', [PostulacionSeleccionController::class, 'index'])->name('postulaciones.seleccion');
    Route::get('/postulaciones/seleccion/revisar', [PostulacionSeleccionController::class, 'revisar'])->name('postulaciones.seleccion.revisar');
    Route::post('/postulaciones/seleccion/confirmar', [PostulacionSeleccionController::class, 'confirmar'])->name('postulaciones.seleccion.confirmar');
    Route::get('/postulaciones/seleccion/cierre', [PostulacionSeleccionController::class, 'cierre'])->name('postulaciones.seleccion.cierre');
    Route::post('/postulaciones/seleccion/cierre', [PostulacionSeleccionController::class, 'cerrar'])->name('postulaciones.seleccion.cerrar');
    Route::post('/postulaciones/{postulacion}/deseleccionar', [PostulacionSeleccionController::class, 'deseleccionar'])->name('postulaciones.deseleccionar');
    Route::get('/postulaciones/{postulacion}', [PostulacionAdminController::class, 'show'])->name('postulaciones.show');
    Route::patch('/postulaciones/{postulacion}/revisar', [PostulacionAdminController::class, 'revisar'])->name('postulaciones.revisar');
    Route::get('/postulaciones/{postulacion}/editar', [PostulacionAdminController::class, 'edit'])->name('postulaciones.edit');
    Route::put('/postulaciones/{postulacion}', [PostulacionAdminController::class, 'update'])->name('postulaciones.update');
    Route::get('/postulaciones/{postulacion}/documento/{tipo}', [PostulacionAdminController::class, 'documento'])->name('postulaciones.documento');

    // Ruta que actualiza los datos del guardavida (turno, puesto, etc.)
    Route::post('/guardavida/setup', [GuardavidaController::class, 'setup'])
        ->name('guardavida.setup.store');
    /**/

    // El guardavida logueado configura su día franco fijo (usado desde el
    // popup en "Cambios de Franco" cuando todavía no lo cargó).
    Route::patch('/mi-dia-franco', [GuardavidaController::class, 'actualizarDiaFranco'])->name('guardavida.actualizarDiaFranco');

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('can:ver_panel_inicio');

    // Datos para los gráficos del dashboard (dashboard-charts.js). Antes
    // vivía en routes/api.php sin ningún middleware de autenticación —
    // estaba totalmente público — pero en realidad se consume con un
    // fetch() normal de la página (cookie de sesión, sin token Bearer), no
    // como el resto de las rutas de api.php (pensadas para el token
    // Sanctum del flujo QR offline). Por eso va acá, autenticado por
    // sesión igual que la propia página del dashboard.
    Route::get('/api/dashboard', [HomeController::class, 'getData'])->middleware('can:ver_panel_inicio');

    Route::get('/activeCamera', [QrController::class, 'activeCamera'])->name('activeCamera')->middleware('can:ver_asistencia_propia');

    // Escritura (crear/editar/borrar) bloqueada para guardavida/encargado
    // fuera de una temporada con ventana operativa activa — ver
    // EnsureTemporadaActiva. Admin/superadmin no tienen esta restricción.
    // No se aplica a Asistencia (fichaje): ver nota en el middleware.
    Route::resource('bandera', App\Http\Controllers\BanderaController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'temporada.activa');
    Route::resource('intervencion', App\Http\Controllers\IntervencionController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'temporada.activa');
    Route::resource('novedad-de-material', App\Http\Controllers\NovedadMaterialController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'temporada.activa');

    // Gestión de guardavidas: cada acción pide su permiso.
    Route::post('guardavida/{guardavida}/baja', [GuardavidaController::class, 'baja'])->name('guardavida.baja')->middleware('can:eliminar_guardavida');
    Route::post('guardavida/{guardavida}/reincorporar', [GuardavidaController::class, 'reincorporar'])->name('guardavida.reincorporar')->middleware('can:agregar_guardavida');
    Route::get('guardavidas-dados-de-baja', [GuardavidaController::class, 'bajas'])->name('guardavidas.bajas')->middleware('can:ver_guardavida');
    Route::resource('guardavida', App\Http\Controllers\GuardavidaController::class)
        ->middlewareFor(['index', 'show'], 'can:ver_guardavida')
        ->middlewareFor(['create', 'store'], 'can:agregar_guardavida')
        ->middlewareFor(['edit', 'update'], 'can:editar_guardavida')
        ->middlewareFor('destroy', 'can:eliminar_guardavida');
    Route::get('/get-all-guardavidas', [GuardavidaController::class, 'getAll'])->middleware('can:ver_guardavida');

    // Constancia médica de una licencia: la sirve la app (login + permiso), no una URL pública de storage.
    Route::get('licencia/{licencia}/constancia', [App\Http\Controllers\LicenciaController::class, 'constancia'])->name('licencia.constancia');
    Route::resource('licencia', App\Http\Controllers\LicenciaController::class)
        ->parameters(['licencia' => 'licencia'])
        ->middlewareFor(['store', 'update', 'destroy'], 'temporada.activa');
    Route::resource('cambio-de-turno', App\Http\Controllers\CambioDeTurnoController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'temporada.activa');

    Route::post('temporada/reasignar', [App\Http\Controllers\TemporadaController::class, 'reasignar'])->name('temporada.reasignar');
    Route::resource('temporada', App\Http\Controllers\TemporadaController::class)->except('show');
    Route::get('temporada/{temporada}/modelo-declaracion', [App\Http\Controllers\TemporadaController::class, 'modeloDeclaracion'])->name('temporada.modelo-declaracion');

    // Excel
    Route::get('/guardavidas/export', function () {
        return Excel::download(new GuardavidasExport, 'guardavidas.xlsx');
    })->middleware('can:ver_guardavida')->name('guardavidas.export');
    Route::get('/export/playas', [ExportController::class, 'exportPorPlaya'])
        ->name('export.playas'); // cada tipo de Excel valida el permiso de su vista (ExportController)

    /* Nuevas rutas */
    Route::get('/my-profile', [GuardavidaController::class, 'myProfile'])->name('guardavida.myProfile');
    Route::put('/my-profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // pasar a moddleware admin
    // Route::middleware(['auth', 'can:admin'])
    Route::put('/update-user/{user}', [RegisteredUserController::class, 'updateUserByAdmin'])->name('user.update')->middleware('can:editar_guardavida');
    Route::put('/update-rol/{user}', [GuardavidaController::class, 'updateUserRol'])->name('rol.update')->middleware('can:editar_guardavida');

    //  NUEVAS RUTAS PARA PERFILES (dentro del middleware)
    Route::get('/profile', [GuardavidaController::class, 'myProfile'])->name('guardavida.myProfile');

    Route::put('/profile/{guardavida}', [GuardavidaController::class, 'updateProfile'])->name('guardavida.updateProfile');

    // Foto personal del guardavida para el perfil (la ve él mismo, el admin o el encargado).
    Route::get('/guardavida/{guardavida}/foto', [GuardavidaController::class, 'foto'])->name('guardavida.foto');

    // listado de cambios de turno
    Route::get('turnos', [CambioDeTurnoController::class, 'indexAdmin'])->name('cambio-de-turno.index');

    // obtener puestos para renderizar con balnearios en la vista del template al momneto de seleccionar o modificar
    /* Route::get('/puestos-por-playa/{playa_id}', [GuardavidaController::class, 'obtenerPuestos'])
         ->name('puestos.por.playa');*/
    Route::get('/puestos-por-playa/{id}', [GuardavidaController::class, 'obtenerPuestos']);

    // Listado general (admin)
    Route::get('asistencias', [AsistenciaController::class, 'index'])->name('asistencias.index')->middleware('can:ver_asistencia');

    // Historial individual por guardavida
    Route::get('asistencias/{id}', [AsistenciaController::class, 'asistenciasPorGuardavida'])->name('asistencias.guardavida')->middleware('can:ver_asistencia');

    // Cambios puntuales de franco cargados directo por encargado/admin (caso
    // excepcional/corrección) — el día franco fijo lo configura el propio
    // guardavida desde su perfil (guardavida.updateProfile).
    Route::post('guardavida/{guardavida}/franco-excepcion', [FrancoExcepcionController::class, 'store'])->name('franco-excepcion.store')->middleware('can:editar_guardavida');
    Route::delete('franco-excepcion/{francoExcepcion}', [FrancoExcepcionController::class, 'destroy'])->name('franco-excepcion.destroy')->middleware('can:editar_guardavida');

    // Intercambio de franco entre guardavidas (self-service, requiere que el
    // compañero acepte). Es puntual: no modifica el esquema de franco fijo de nadie.
    Route::middleware('can:solicitar_cambio_franco')->group(function () {
        Route::get('/mis-cambios-de-franco', [FrancoIntercambioController::class, 'index'])->name('franco-intercambio.index');
        Route::post('/franco-intercambio', [FrancoIntercambioController::class, 'store'])->name('franco-intercambio.store');
        Route::post('/franco-intercambio/{francoIntercambio}/aceptar', [FrancoIntercambioController::class, 'aceptar'])->name('franco-intercambio.aceptar');
        Route::post('/franco-intercambio/{francoIntercambio}/rechazar', [FrancoIntercambioController::class, 'rechazar'])->name('franco-intercambio.rechazar');
        Route::delete('/franco-intercambio/{francoIntercambio}', [FrancoIntercambioController::class, 'cancelar'])->name('franco-intercambio.cancelar');
    });

    // para la seccion de "mis asistencias" cerca de "ver perfil"
    Route::get('/mis-asistencias', [AsistenciaController::class, 'misAsistencias'])
        ->name('guardavida.misAsistencias')->middleware('can:ver_asistencia_propia');

    // Guía de ayuda para administradores (temporada, postulaciones, selección).
    // Reutiliza ver_postulacion: lo tienen admin/superadmin, no guardavida ni encargado.
    Route::get('/ayuda/temporada', fn () => view('ayuda.temporada'))
        ->middleware('can:ver_postulacion')
        ->name('ayuda.temporada');

    // Gestión de permisos por rol. Gateado por el permiso abm_roles_y_permisos,
    // que hoy solo tiene el rol superadmin (ver RolesYPermisosSeeder) — un admin
    // normal no lo tiene y por lo tanto no puede acceder a estas rutas.
    Route::middleware('can:abm_roles_y_permisos')->group(function () {
        Route::get('/permisos', [PermissionController::class, 'index'])->name('permisos.index');
        Route::put('/permisos', [PermissionController::class, 'update'])->name('permisos.update');
        Route::post('/permisos/roles', [PermissionController::class, 'storeRol'])->name('permisos.roles.store');
    });
});

// Ruta para obtener el Token Bearer para ser usado en el QR
// Ademas guarda Id_user para casos sin wifi.
Route::post('/loginIdUser', [ApiAuthController::class, 'login'])->name('loginIdUser');

// Route::get('/clear-laravel-cache', function () {

//     Artisan::call('cache:clear');
//     Artisan::call('config:clear');
//     Artisan::call('view:clear');
//     Artisan::call('route:clear');

//     return 'CACHE LIMPIADA ✔';
// });

require __DIR__.'/auth.php';

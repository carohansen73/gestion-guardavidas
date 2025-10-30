<?php

use App\Exports\GuardavidasExport;
use App\Exports\IntervencionesExport;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');



// Route::get('/home', function () {
//     return view('ui.home');
// })->name('home');


Route::get('/template', function () {
    return view('ui.template');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

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


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //pasar a moddleware admin
    //Route::middleware(['auth', 'can:admin'])
    Route::put('/update-user/{user}', [RegisteredUserController::class, 'updateUserByAdmin'])->name('user.update');
    Route::put('/update-rol/{user}', [GuardavidaController::class, 'updateUserRol'])->name('rol.update');

});

Route::post('/loginIdUser', [ApiAuthController::class, 'login']);



require __DIR__.'/auth.php';


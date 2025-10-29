<?php


use App\Http\Controllers\QrController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

    Route::resource('intervencion', App\Http\Controllers\IntervencionController::class);
    Route::resource('novedad-de-material', App\Http\Controllers\NovedadMaterialController::class);
    Route::resource('bandera', App\Http\Controllers\BanderaController::class);
    Route::resource('guardavida', App\Http\Controllers\GuardavidaController::class);
    Route::patch('usuario-toggle/{user}', [UserController::class, 'toggle'])->name('user.toggle');
    Route::get('guardavidas-deshabilitados', [GuardavidaController::class, 'getAllDisabled'])->name('guardavidas.disabled');
    Route::get('/get-all-guardavidas', [GuardavidaController::class, 'getAll']);

    Route::get('/activeCamera', [QrController::class, 'activeCamera'])->name('activeCamera');



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
});

Route::post('/loginIdUser', [ApiAuthController::class, 'login']);



require __DIR__.'/auth.php';


<?php

use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\qrController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.welcome');
})->name('welcome');

Route::get('/tailwind', function () {
    return view('ui.tailwind');
});

// Route::get('/home', function () {
//     return view('ui.home');
// })->name('home');



Route::get('/home-options', function () {
    return view('ui.home-options');
});

Route::get('/template', function () {
    return view('ui.template');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
     Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('intervencion', App\Http\Controllers\IntervencionController::class);

    Route::resource('bandera', App\Http\Controllers\BanderaController::class);


    Route::get('/get-all-guardavidas', [GuardavidaController::class, 'getAll']);


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // QR
    Route::post("/desencriptar-qr", [qrController::class, 'desencriptarQr'])->name('desencriptar.qr');
});

Route::post('api/login', [ApiAuthController::class, 'login']);

require __DIR__.'/auth.php';


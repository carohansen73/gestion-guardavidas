<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas API
Route::middleware(['auth:sanctum', 'account.enabled'])->group(function () {
    Route::post('/desencriptar-qr', [QrController::class, 'desencriptarQr']);
    Route::post('/verPuesto', [UserController::class, 'verPuestoUsuario'])->name('puesto.usuario');
    Route::post('/cargarAsistencia', [AsistenciaController::class, 'cargarAsistencia'])->name('asistencia.guardar');
    Route::post('/obtenerFueraDeZona', [GuardavidaController::class, 'obtenerFueraDeZona'])->name('guardavida.fuera_de_zona');
});

Route::get('/dashboard', [HomeController::class, 'getData']);

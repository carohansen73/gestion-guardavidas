<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\HomeController;

// Rutas API
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/desencriptar-qr', [QrController::class, 'desencriptarQr']);
        Route::post('/verPuesto', [UserController::class, 'verPuestoUsuario'])->name('puesto.usuario');
        Route::post('/cargarAsistencia',[AsistenciaController::class, 'cargarAsistencia'])->name('asistencia.guardar');
        Route::post('/obtenerFueraDeZona',[GuardavidaController::class, 'obtenerFueraDeZona'])->name('guardavida.fuera_de_zona');
    });

    Route::get('/dashboard', [HomeController::class, 'getData']);




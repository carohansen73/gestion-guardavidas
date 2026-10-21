<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\GuardavidaController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas API
// Todo esto es el flujo de fichar: lo hacen quienes tienen `ver_asistencia_propia` (guardavida, encargado, personal).
Route::middleware(['auth:sanctum', 'account.enabled', 'can:ver_asistencia_propia'])->group(function () {
    Route::post('/desencriptar-qr', [QrController::class, 'desencriptarQr']);
    Route::post('/verPuesto', [UserController::class, 'verPuestoUsuario'])->name('puesto.usuario');
    Route::post('/cargarAsistencia', [AsistenciaController::class, 'cargarAsistencia'])->name('asistencia.guardar');
    Route::post('/obtenerFueraDeZona', [GuardavidaController::class, 'obtenerFueraDeZona'])->name('guardavida.fuera_de_zona');
});

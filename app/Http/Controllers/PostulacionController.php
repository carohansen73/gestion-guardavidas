<?php

namespace App\Http\Controllers;

class PostulacionController extends Controller
{
    /**
     * Pantalla principal del área de postulante. Por ahora es un
     * placeholder - el formulario multi-paso real y el estado de la
     * postulación (pendiente/aceptada/rechazada, seleccionado) se agregan
     * en la Fase 4, cuando exista la tabla `postulaciones`.
     */
    public function index()
    {
        return view('ui.postulacion.index');
    }
}

<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardavida;
use App\Models\Asistencia;
use Laravel\Sanctum\PersonalAccessToken;

class AsistenciaController extends Controller
{
    public function cargarAsistencia(Request $request){
        $user = Auth::check() ? Auth::user() : null;

        if (!$user && $request->bearerToken()) {
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            if ($accessToken) {
                $user = $accessToken->tokenable; // Usuario asociado al token
            }
        }

        if (!$user) {
            return response()->json([
                'success' =>false,
                'data' => 'Debe loguearse para guardar la asistencia',
            ], 401);
        }

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'playa_id' => 'required|integer|exists:playas,id',
            'puesto_id' => 'required|integer|exists:puestos,id',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between: -180,180',
            'precision' => 'required|numeric|min:0',
            'fecha_hora' => 'required|date_format:Y-m-d H:i:s'
        ]);
        $idUser = $validated['user_id'];
        $idPlaya = $validated['playa_id'];

        $guardavidas = Guardavida::obtenerGuardavidas($idUser, $idPlaya);
        if (is_null($guardavidas)) {
            return response()->json([
                'success' => false,
                'data' => 'No se pudo registrar la asistencia'
            ], 400);
        }
        $fecha_hora = $validated['fecha_hora'];
        $lat = $validated['lat'];
        $lng = $validated['lng'];
        $precision = $validated['precision'];
        $idPuesto = $validated['puesto_id'];
        $guardavidas_id = $guardavidas->id;
        $asistencia = Asistencia::nuevaAsistencia($lng, $lat, $precision, $idPuesto, $guardavidas_id, $fecha_hora);
        return response()->json([
            'success' => true,
            'data' => $asistencia
        ], 200);
    }


    /**
     * Muestra el listado completo de asistencias
     * (usado en la vista admin/usuarios/asistencias.blade.php)
     */
    public function index()
    {
        //  Cargamos relaciones completas para evitar consultas N+1
        $asistencias = Asistencia::with(['guardavida.puesto.playa', 'puesto.playa'])
            ->orderByDesc('fecha_hora')
            ->get();

        return view('admin.usuarios.asistencias', compact('asistencias'));
    }
    /**
     * Muestra todas las asistencias de un guardavida específico
     * (usado en profile/profile.blade.php)
     */
    public function asistenciasPorGuardavida($id)
    {
        $guardavida = Guardavida::with([
            'puesto.playa',
            'asistencias.puesto.playa'
        ])->findOrFail($id);

        return view('profile.profile', compact('guardavida'));
    }


    /**
     * Muestra todas las asistencias de un puesto específico
     */
    public function asistenciasPorPuesto($idPuesto)
    {
        $asistencias = Asistencia::with(['guardavida', 'puesto.playa'])
            ->where('puesto_id', $idPuesto)
            ->orderByDesc('fecha_hora')
            ->get();

        return view('admin.usuarios.asistencias', compact('asistencias'));
    }
}

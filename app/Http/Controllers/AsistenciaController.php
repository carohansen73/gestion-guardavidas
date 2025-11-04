<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardavida;
use App\Models\Asistencia;
use App\Models\Playa;
use App\Models\Puesto;
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
     * Listado general de asistencias (vista admin)
     * Muestra todas las asistencias de todos los guardavidas.
     */
    public function index()
    {


        if (!auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            // Si no tiene permiso, devolvemos vista vacía o redirige (eso no me acuerdo como se veia en la interfaz)
            return view('admin.usuarios.asistencias', ['guardavidas' => collect()]);
        }
        $guardavidas = Guardavida::with(['puesto.playa'])
            ->paginate(10);

        return view('admin.usuarios.asistencias', compact('guardavidas'));
    }
    /**
     * Muestra todas las asistencias de un guardavida en especifico*cuando el admin lo selecciona o cuando el propio usuario
     * ingresa a la seccion "mis asistencias o asistencia"
     * (usado en admin/asistenciaPorPerfil.blade.php)
     */
    public function asistenciasPorGuardavida($id)
    {
        $guardavida = Guardavida::with(['puesto.playa', 'asistencias.puesto.playa'])->findOrFail($id);
         /**si falla aca en anyrole es porque
         * no me dejaba ejecutar
         *  la migracion.
         El composer require spatie/laravel-permission
         de permissions ya lo instale
         * */
        $esAdmin = auth()->user()->hasAnyRole(['admin', 'encargado']); // o el método que uses para validar admin

        // Solo si es admin, mandamos balnearios y puestos
        $balnearios = $esAdmin ?Playa::all() : null;
        $puestos = $esAdmin ?Puesto::all() : null;

        return view('admin.asistenciaPorPerfil', compact('guardavida', 'esAdmin', 'balnearios', 'puestos'));
    }


    /**
     * metodo para que muestre en la seccion "mis asistencias" las asistencias del usuario logueado (solo las ve no puede descargar ni nada
     * como administradores)
     */

    public function misAsistencias()
    {
        $guardavida = auth()->user()->guardavida;
        if (!$guardavida) {
            abort(403, 'No tiene un perfil de guardavida asignado.');
        }

        // Cargar relaciones
        $guardavida->load('asistencias.puesto.playa');

        // Pasamos $esAdmin = false para que el Blade detecte que no es vista administrativa
        $esAdmin = false;
        // No necesitamos filtros ni balnearios/puestos para este caso
        return view('admin.asistenciaPorPerfil', compact('guardavida','esAdmin'));
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

        // Extraigo los guardavidas únicos
        $guardavidas = $asistencias->pluck('guardavida')->unique('id')->values();

        return view('admin.usuarios.asistencias', compact('guardavidas'));
    }



    public function guardavidasPanelExcelAsistencias(){
        return view('admin.DescargaDelExcelAsistencias');


    }



}

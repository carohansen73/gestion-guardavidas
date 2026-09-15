<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardavida;
use App\Models\Asistencia;
use App\Models\Playa;
use App\Models\Puesto;
use App\Services\HistorialAsistenciaService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Jenssegers\Agent\Agent;
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


        $guardavidas = Guardavida::obtenerGuardavidas($idUser);
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

        $puesto = Puesto::findOrFail($idPuesto);
        $estadoValidacion = $this->validarDistanciaAlPuesto($puesto, $lat, $lng, $precision);

        $asistencia = Asistencia::nuevaAsistencia($lng, $lat, $precision, $idPuesto, $guardavidas_id, $fecha_hora, $estadoValidacion);
        return response()->json([
            'success' => true,
            'data' => $asistencia
        ], 200);
    }

    /**
     * Determina si un fichaje quedó dentro del radio esperado del puesto
     * (200m), sin bloquear el registro: solo lo marca para revisión.
     *
     * Le da el beneficio de la duda al margen de error propio del GPS
     * (`$precisionMetros`, lo que el dispositivo reporta como incertidumbre
     * de su propia ubicación) — un GPS con poca señal o el toggle de
     * "Ubicación exacta" desactivado en iOS puede devolver una posición con
     * cientos/miles de metros de margen de error; en esos casos no tiene
     * sentido usar la distancia calculada como si fuera exacta.
     *
     * Los puestos "móviles" (fuera de zona de baño) no tienen radio fijo,
     * así que siempre quedan como válidos.
     */
    private function validarDistanciaAlPuesto(Puesto $puesto, float $lat, float $lng, float $precisionMetros): string
    {
        $esMovil = Puesto::getMovil()->contains('id', $puesto->id);
        if ($esMovil) {
            return 'valido';
        }

        $distancia = $this->calcularDistanciaMetros($lat, $lng, (float) $puesto->latitud, (float) $puesto->longitud);

        $distanciaConTolerancia = $distancia - $precisionMetros;

        return $distanciaConTolerancia > 200 ? 'fuera_de_rango' : 'valido';
    }

    /**
     * Fórmula de Haversine — distancia en metros entre dos coordenadas.
     */
    private function calcularDistanciaMetros(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radioTierra = 6371000; // metros
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2
            + cos($radLat1) * cos($radLat2) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
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
        $playas = Playa::all();
        $agent = new Agent();

        if($agent->isMobile()){
            return view('admin.usuarios.asistencias', compact('guardavidas', 'playas'));
        }
        else{
            return view('admin.usuarios.asistencias-desktop', compact('guardavidas', 'playas'));
        }



    }
    /**
     * Muestra todas las asistencias de un guardavida en especifico*cuando el admin lo selecciona o cuando el propio usuario
     * ingresa a la seccion "mis asistencias o asistencia"
     * (usado en admin/asistenciaPorPerfil.blade.php)
     */
    public function asistenciasPorGuardavida(Request $request, $id)
    {
        $guardavida = Guardavida::with(['puesto.playa', 'asistencias.puesto.playa'])->findOrFail($id);

        $esAdmin = auth()->user()->hasAnyRole(['admin', 'encargado']);

        // Solo si es admin, mandamos balnearios y puestos
        $balnearios = $esAdmin ?Playa::all() : null;
        $puestos = $esAdmin ?Puesto::all() : null;
        $historial = $esAdmin ? $this->getAttendanceHistory($request, $id) : null;

        return view('admin.usuarios.asistencia-show-desktop', compact('guardavida', 'esAdmin', 'balnearios', 'puestos', 'historial'));
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



    /**
     * Historial de asistencias día por día para el guardavida seleccionado.
     * La construcción del historial (ASISTIÓ/FALTA/LICENCIA, fuera_de_rango)
     * vive en HistorialAsistenciaService — la misma lógica que usan los
     * excels de asistencia, para que pantalla y Excel nunca se desincronicen.
     */
    public function getAttendanceHistory($request, $guardavidaId){

        //Toma el filtro de fechas, y si no se selecciono fecha, toma desde hace 30 dias atras.
        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $fin = $request->filled('fin')
            ? Carbon::parse($request->input('fin'))->endOfDay()
            : Carbon::now()->endOfDay();

        $historial = (new HistorialAsistenciaService())->generar($guardavidaId, $inicio, $fin);

        // Paginación (el historial se arma completo en memoria, día por día)
        $page = request()->input('page', 1);
        $perPage = 10;

        $items = array_slice($historial, ($page - 1) * $perPage, $perPage);

        return new LengthAwarePaginator(
            $items,
            count($historial),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }



}

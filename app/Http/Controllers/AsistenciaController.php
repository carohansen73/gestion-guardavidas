<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guardavida;
use App\Models\Asistencia;
use App\Models\Licencia;
use App\Models\Playa;
use App\Models\Puesto;
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
        $playas = Playa::all();
        $agent = new Agent();

        if($agent->isMobile()){
            return view('admin.usuarios.asistencias', compact('guardavidas'));
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



    public function guardavidasPanelExcelAsistencias(){
        return view('admin.DescargaDelExcelAsistencias');


    }

    /**
     * Agrego funcionalidad para crear un historial de asistencias para el guardavida seleccionado
     */
    public function getAttendanceHistory($request, $guardavidaId){

        $guardavida = Guardavida::findOrFail($guardavidaId);

        //Toma el filtro de fechas, y si no se selecciono fecha, toma desde hace 30 dias atras.
        $inicio = $request->filled('inicio')
            ? Carbon::parse($request->input('inicio'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $fin = $request->filled('fin')
            ? Carbon::parse($request->input('fin'))->endOfDay()
            : Carbon::now()->endOfDay();

        // FECHA DE INICIO FIJA (30 dias atras)
        // $inicio = Carbon::now()->subDays(30)->startOfDay();
        // $fin = Carbon::now()->endOfDay();

        // Asistencias del rango
        $asistencias = Asistencia::where('guardavidas_id', $guardavidaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(fn($a) => Carbon::parse($a->fecha_hora)->toDateString());

        // Licencias del rango
        $licencias = Licencia::where('guardavida_id', $guardavidaId)
            ->where(function($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_inicio', [$inicio, $fin])
                ->orWhereBetween('fecha_fin', [$inicio, $fin])
                ->orWhere(function ($q2) use ($inicio, $fin) {
                    $q2->where('fecha_inicio', '<=', $inicio)
                        ->where('fecha_fin', '>=', $fin);
                });
            })
            ->get();

        // Construcción del historial día x día
        $historial = [];

        for ($fecha = $inicio->copy(); $fecha->lte($fin); $fecha->addDay()) {

            $dateString = $fecha->toDateString();

            // 1. Verificamos si hay licencia ese día
            $licencia = $licencias->first(function ($l) use ($fecha) {
                return $fecha->between($l->fecha_inicio, $l->fecha_fin);
            });

            if ($licencia) {
                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'LICENCIA',
                    'detalle' => $licencia->tipo_licencia,
                    'ingreso' => null,
                    'egreso' => null,
                    'puesto' => $licencia->puesto->nombre ?? '-'
                ];
                continue;
            }

            // 2. Verificamos si hay asistencia ese día
            $asis = $asistencias->get($dateString);

            if ($asis) {
                $historial[] = [
                    'fecha' => $dateString,
                    'estado' => 'ASISTIÓ',
                    'ingreso' => $asis->first()->fecha_hora,
                    'egreso' => $asis->last()->fecha_hora,
                    'puesto' => $asis->first()->puesto->nombre ?? '-',
                ];
                continue;
            }

            // 3. Si no hay ni licencia ni asistencia → FALTÓ
            $historial[] = [
                'fecha' => $dateString,
                'estado' => 'FALTA',
                'ingreso' => null,
                'egreso' => null,
                'puesto' => '-',
            ];
        }

        // 🔵 AGREGO PAGINACIÓN
        $page = request()->input('page', 1);
        $perPage = 10;

        $items = array_slice($historial, ($page - 1) * $perPage, $perPage);

        $paginado = new LengthAwarePaginator(
            $items,
            count($historial),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginado;

    }



}

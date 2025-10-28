<?php

namespace App\Http\Controllers;

use App\Models\Bandera;
use App\Models\BanderaTipo;
use App\Models\Playa;
use App\Models\Puesto;
use App\Http\Requests\StoreBanderaRequest;
use App\Http\Requests\UpdateBanderaRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;


class BanderaController extends Controller
{
    /**
     * Display a listing of the resource. -> Historial
     */
    public function index()
    {
         $user = Auth::user();
        if ($user->hasRole('guardavida')) {
            $registros = Bandera::where('playa_id', $user->guardavida->playa_id)
            ->with(['bandera', 'playa'])
            ->latest()
            ->get();
        }  elseif ($user->hasAnyRole(['admin', 'encargado', 'jefeDePlaya'])) {
             $registros = Bandera::with(['bandera', 'playa'])
            ->latest()->get();
        } else {
            abort (403, 'No tienes permisos para ver este historial de banderas');
        }

        $playas = Playa::all();

        return view('ui.banderas.index')
        ->with('registros', $registros)
        ->with('playas', $playas)
        ->with('user', $user);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;


        $banderas = BanderaTipo::all();
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();

        $bandera = null;

        return view('ui.banderas.fields', compact(
            'guardavidaAuth', 'banderas', 'playas', 'puestos', 'bandera'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBanderaRequest $request)
    {
        $user_id = Auth::id();

        $validated = $request->validated();

        $playa = Playa::findOrFail($validated['playa_id']);
        $viento = $this->obtenerClimaFechaYHora($playa, $validated['fecha']);

        $bandera = Bandera::create([
            ...$validated,
            'user_id'          => $user_id,
            'viento_intensidad' => $viento['viento_intensidad'],
            'viento_direccion' => $viento['viento_direccion'],
            'temperatura' => $viento['temperatura'],
        ]);

        return redirect()->route('bandera.index')
            ->with('success', 'Bandera registrada correctamente');
    }



    /**
     * Display the specified resource.
     */
    public function show(Bandera $bandera)
    {
        return view('ui.banderas.show-fields', compact(
           'bandera'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bandera $bandera)
    {
        $user = Auth::user();
        $guardavidaAuth = $user->guardavida;

        $banderas = BanderaTipo::all();
        $playas = Playa::all();
        $puestos = Puesto::orderBy('nombre')->get();

        return view('ui.banderas.fields', compact(
            'guardavidaAuth', 'banderas', 'playas', 'puestos', 'bandera'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBanderaRequest $request, Bandera $bandera)
    {
        $validated = $request->validated();

        if (!$bandera->viento_intensidad || !$bandera->viento_direccion){

            $playa_id = $validated['playa_id'] ?? $bandera->playa_id;
            $fecha = $validated['fecha'] ?? $bandera->fecha;
            //pido el clima a la api y actualizo
            $playa = Playa::findOrFail($playa_id);
            $viento = $this->obtenerClimaFechaYHora($playa, $fecha);

            $validated['viento_intensidad'] = $viento['viento_intensidad'];
            $validated['viento_direccion'] = $viento['viento_direccion'];
            $validated['temperatura'] = $viento['temperatura'];
        }

        $bandera->update($validated);

        return redirect()->route('bandera.index')
            ->with('success', 'Bandera actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bandera $bandera)
    {
        $user = Auth::user();

        //solo lo puede eliminar el usuaurio que lo creó o el encargado, jefe de playa o admin
        //TODO cuanod haga el control por Policy
        //$this->authorize('delete', $bandera);
        if ($bandera->user_id === $user->id || $user->hasAnyRole(['encargado', 'admin']) ) {
            $bandera->delete();

            return redirect()->route('bandera.index')
            ->with('success', 'Registro de bandera eliminado');
        }

        return redirect()->route('bandera.index')
        ->with('error', 'No tienes permiso para eliminar este registro de bandera.');
    }

    /**
     * Funciones auxiliares
     */

    public function obtenerClima($playa){

        $response = Http::get('https://api.open-meteo.com/v1/forecast?', [
            'latitude' => $playa->lat,
            'longitude' => $playa->lon,
            'current_weather' => true,
            'hourly'  => 'windgusts_10m',
            'timezone'  => 'auto',
        ]);

        //MANEJO ERROR MATEO
        if ($response->successful()) {

            $data = $response->json();
            // 🌡️ Temperatura actual (°C)
            $temperatura = isset($data['current_weather']['temperature'])
            ? round($data['current_weather']['temperature'])
            : null;

            //Viento medio km/h
            $velocidad = round($data['current_weather']['windspeed']); // km/h

            //Direccion en grados
            $windDeg = $data['current_weather']['winddirection'];
            $direccion = $this->gradosACardinal($windDeg);

            // Hora actual del reporte -para encontrar rafagas
            $currentTime = $data['current_weather']['time'];

            //Ráfaga mas cercana
            $rafagas = null;
            if (isset($data['hourly']['time'])) {
                $times = $data['hourly']['time'];
                $gusts = $data['hourly']['windgusts_10m'];

                // Convertir a timestamps
                $currentTs = strtotime($currentTime);
                $closestIndex = null;
                $minDiff = PHP_INT_MAX;

                foreach ($times as $i => $t) {
                    $diff = abs(strtotime($t) - $currentTs);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $closestIndex = $i;
                    }
                }

                if ($closestIndex !== null) {
                    $rafagas = round($gusts[$closestIndex]);
                }
            }

            //Salida
            if ($rafagas) {
                $viento_intensidad = "{$velocidad} - {$rafagas} km/h";
            } else {
                $viento_intensidad = "{$velocidad} km/h";
            }

            return(['viento_intensidad' => $viento_intensidad,
                'viento_direccion' =>  $direccion,
                'temperatura' =>  $temperatura,]);

        } else {
            return null;
        }
    }


    /**
     * TODO Ver si la bandera que cargan tiene que ser actual o pueden carfgar defasada. si cargan para otro dia pasarle parametros fecha-hora
     *
     * @param [type] $playa
     * @param [type] $fechaHora
     * @return [temperatura y viento direccion y velocidad]
     */
    public function obtenerClimaFechaYHora($playa, $fechaHora = null){

        $params = [
            'latitude' => $playa->lat,
            'longitude' => $playa->lon,
            'timezone'  => 'auto',
        ];

        if($fechaHora) {
            $params['hourly'] = 'temperature_2m,windspeed_10m,winddirection_10m,windgusts_10m';
            $params['start_date'] = Carbon::parse($fechaHora)->toDateString();
            $params['end_date'] = Carbon::parse($fechaHora)->addHour()->toDateString();
            $url = "https://archive-api.open-meteo.com/v1/archive";

        } else{
            $params['current_weather'] = true;
            $params['hourly'] = 'windgusts_10m';
            $url = 'https://api.open-meteo.com/v1/forecast';
        }
        $response = Http::get($url, $params);

        //Chequeo que traiga una respuesta satisfactoria
        if (!$response->successful()) {
            // registro el error
            \Log::error('Error al consultar la API del clima', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'temperatura' => null,
                'viento_intensidad' => null,
                'viento_direccion' => null,
            ];
        }

        $data = $response->json();

        //Armo respuesta con/sin fecha hora
        if ($fechaHora && isset($data['hourly'])) {

            $times = $data['hourly']['time'];
            $temps = $data['hourly']['temperature_2m'];
            $winds = $data['hourly']['windspeed_10m'];
            $dirs = $data['hourly']['winddirection_10m'];
            $gusts = $data['hourly']['windgusts_10m'];

            // Buscar la hora más cercana
            $targetTs = strtotime($fechaHora);
            $closestIndex = null;
            $minDiff = PHP_INT_MAX;
            foreach ($times as $i => $t) {
                $diff = abs(strtotime($t) - $targetTs);
                if ($diff < $minDiff) {
                    $minDiff = $diff;
                    $closestIndex = $i;
                }
            }

            $velocidad = round($winds[$closestIndex]);
            $rafagas = isset($gusts[$closestIndex]) ? round($gusts[$closestIndex]) : null;

            //Salida
            if ($rafagas) {
                $viento_intensidad = "{$velocidad}  - {$rafagas} km/h";
            } else {
                $viento_intensidad =  "{$velocidad} km/h";
            }

            return [
                'temperatura' => round($temps[$closestIndex]),
                'viento_intensidad' =>  $viento_intensidad,
                'viento_direccion' => $this->gradosACardinal($dirs[$closestIndex]),
            ];

        } elseif (isset($data['current_weather'])) {

            // 🌡️ Temperatura actual (°C)
            $temperatura = isset($data['current_weather']['temperature'])
            ? round($data['current_weather']['temperature'])
            : null;

            //Viento medio km/h
            $velocidad = round($data['current_weather']['windspeed']); // km/h

            //Direccion en grados
            $windDeg = $data['current_weather']['winddirection'];
            $direccion = $this->gradosACardinal($windDeg);

            // Hora actual del reporte -para encontrar rafagas
            $currentTime = $data['current_weather']['time'];

            //Ráfaga mas cercana
            $rafagas = null;
            if (isset($data['hourly']['time'])) {
                $times = $data['hourly']['time'];
                $gusts = $data['hourly']['windgusts_10m'];

                // Convertir a timestamps
                $currentTs = strtotime($currentTime);
                $closestIndex = null;
                $minDiff = PHP_INT_MAX;

                foreach ($times as $i => $t) {
                    $diff = abs(strtotime($t) - $currentTs);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $closestIndex = $i;
                    }
                }

                if ($closestIndex !== null) {
                    $rafagas = round($gusts[$closestIndex]);
                }
            }

            //Salida
            if ($rafagas) {
                $viento_intensidad = "{$velocidad} - {$rafagas} km/h";
            } else {
                $viento_intensidad = "{$velocidad} km/h";
            }

            return(['viento_intensidad' => $viento_intensidad,
                'viento_direccion' =>  $direccion,
                'temperatura' =>  $temperatura,]);
        }
        return null;
    }

    /**
     * Convierte direccion a puntos cardinales
     */
    public function gradosACardinal($grados) {
        $dirs = ["N", "NE", "E", "SE", "S", "SO", "O", "NO"];
        return $dirs[round($grados / 45) % 8];
    }
}

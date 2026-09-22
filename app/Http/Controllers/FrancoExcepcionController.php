<?php

namespace App\Http\Controllers;

use App\Models\FrancoExcepcion;
use App\Models\Guardavida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrancoExcepcionController extends Controller
{
    /**
     * Carga un cambio puntual de franco para una fecha específica. Solo
     * encargado/admin — el esquema fijo lo configura el propio guardavida
     * desde su perfil.
     *
     * A propósito NO existe la opción de agregar un franco suelto (sin
     * cancelar ningún otro día): nadie está habilitado a "regalar" un
     * franco extra. Solo hay dos acciones posibles:
     *  - mover: corre el franco de un día (tiene que ser su franco fijo
     *    vigente en esa fecha) a otro — cancela uno y agrega el otro en la
     *    misma transacción, así nunca queda una semana con 2 francos ni con 0
     *    por una carga a medias.
     *  - cancelar: da de baja un franco puntual sin reemplazo (ej. vino a
     *    trabajar en su día libre).
     */
    public function store(Request $request, Guardavida $guardavida)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tenés permisos para cargar cambios de franco.');
        }

        $validated = $request->validate([
            'accion' => 'required|in:mover,cancelar',
            'fecha_origen' => 'required_if:accion,mover|nullable|date',
            'fecha_destino' => 'required_if:accion,mover|nullable|date|different:fecha_origen',
            'fecha' => 'required_if:accion,cancelar|nullable|date',
            'motivo' => 'nullable|string|max:255',
        ]);

        if ($validated['accion'] === 'mover') {
            return $this->moverFranco($guardavida, $validated);
        }

        FrancoExcepcion::updateOrCreate(
            ['guardavida_id' => $guardavida->id, 'fecha' => $validated['fecha']],
            [
                'tipo' => 'cancelado',
                'motivo' => $validated['motivo'] ?? null,
                'cargado_por_user_id' => auth()->id(),
            ]
        );

        return back()->with('success', 'Cambio de franco cargado correctamente.');
    }

    private function moverFranco(Guardavida $guardavida, array $validated)
    {
        $fechaOrigen = Carbon::parse($validated['fecha_origen']);

        // Nadie regala francos: el día de origen tiene que ser un franco
        // real de esta persona (su esquema fijo vigente en esa fecha), nunca
        // un día cualquiera.
        $diasFrancoVigentes = $guardavida->diasFrancoVigentesEn($fechaOrigen);
        if (! in_array($fechaOrigen->dayOfWeek, $diasFrancoVigentes, true)) {
            return back()->withErrors([
                'fecha_origen' => 'Esa fecha no es un día de franco fijo de '.$guardavida->nombre.'.',
            ])->withInput();
        }

        DB::transaction(function () use ($guardavida, $validated) {
            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $guardavida->id, 'fecha' => $validated['fecha_origen']],
                [
                    'tipo' => 'cancelado',
                    'motivo' => $validated['motivo'] ?? null,
                    'cargado_por_user_id' => auth()->id(),
                ]
            );

            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $guardavida->id, 'fecha' => $validated['fecha_destino']],
                [
                    'tipo' => 'agregado',
                    'motivo' => $validated['motivo'] ?? null,
                    'cargado_por_user_id' => auth()->id(),
                ]
            );
        });

        return back()->with('success', 'Franco movido correctamente.');
    }

    public function destroy(FrancoExcepcion $francoExcepcion)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tenés permisos para eliminar cambios de franco.');
        }

        $francoExcepcion->delete();

        return back()->with('success', 'Cambio de franco eliminado.');
    }
}

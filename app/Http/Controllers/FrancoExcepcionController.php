<?php

namespace App\Http\Controllers;

use App\Models\FrancoExcepcion;
use App\Models\Guardavida;
use Illuminate\Http\Request;

class FrancoExcepcionController extends Controller
{
    /**
     * Carga un cambio puntual de franco para una fecha específica.
     * Solo encargado/admin — el día franco fijo lo configura el propio
     * guardavida desde su perfil.
     */
    public function store(Request $request, Guardavida $guardavida)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403, 'No tenés permisos para cargar cambios de franco.');
        }

        $validated = $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:cancelado,agregado',
            'motivo' => 'nullable|string|max:255',
        ]);

        FrancoExcepcion::updateOrCreate(
            [
                'guardavida_id' => $guardavida->id,
                'fecha' => $validated['fecha'],
            ],
            [
                'tipo' => $validated['tipo'],
                'motivo' => $validated['motivo'] ?? null,
                'cargado_por_user_id' => auth()->id(),
            ]
        );

        return back()->with('success', 'Cambio de franco cargado correctamente.');
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

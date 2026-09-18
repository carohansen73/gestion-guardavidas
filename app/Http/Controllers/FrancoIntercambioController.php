<?php

namespace App\Http\Controllers;

use App\Models\FrancoExcepcion;
use App\Models\FrancoIntercambio;
use App\Models\Guardavida;
use App\Notifications\FrancoIntercambioRespondidoNotification;
use App\Notifications\FrancoIntercambioSolicitadoNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrancoIntercambioController extends Controller
{
    /**
     * Pantalla del guardavida logueado: solicitudes recibidas (para
     * aceptar/rechazar), enviadas (para ver estado o cancelar), y el
     * formulario para pedirle un cambio a un compañero.
     */
    public function index()
    {
        $guardavida = auth()->user()->guardavida;
        if (! $guardavida) {
            abort(403, 'No tenés un perfil de guardavida asignado.');
        }

        $recibidas = $guardavida->intercambiosFrancoRecibidos()
            ->with('solicitante')
            ->orderByDesc('created_at')
            ->get();

        $enviadas = $guardavida->intercambiosFrancoSolicitados()
            ->with('destinatario')
            ->orderByDesc('created_at')
            ->get();

        // Al entrar a esta pantalla se dan por vistas las notificaciones de franco.
        auth()->user()->unreadNotifications->markAsRead();

        // Compañeros de la misma playa para elegir a quién pedirle el cambio.
        $companeros = Guardavida::where('playa_id', $guardavida->playa_id)
            ->where('id', '!=', $guardavida->id)
            ->orderBy('apellido')
            ->get();

        return view('franco.index', compact('guardavida', 'recibidas', 'enviadas', 'companeros'));
    }

    /**
     * El guardavida logueado le pide a un compañero cambiar el franco:
     * cede su fecha_propia y quiere tomar fecha_deseada en su lugar, por esa
     * única vez. No pasa nada hasta que el compañero lo acepte.
     */
    public function store(Request $request)
    {
        $guardavida = auth()->user()->guardavida;
        if (! $guardavida) {
            abort(403, 'No tenés un perfil de guardavida asignado.');
        }

        $validated = $request->validate([
            'guardavida_destinatario_id' => 'required|exists:guardavidas,id',
            'fecha_propia' => 'required|date|after_or_equal:today',
            'fecha_deseada' => 'required|date|after_or_equal:today|different:fecha_propia',
            'mensaje' => 'nullable|string|max:255',
        ]);

        if ((int) $validated['guardavida_destinatario_id'] === $guardavida->id) {
            return back()->withErrors('No podés pedirte un cambio de franco a vos mismo.');
        }

        $intercambio = FrancoIntercambio::create([
            'guardavida_solicitante_id' => $guardavida->id,
            'guardavida_destinatario_id' => $validated['guardavida_destinatario_id'],
            'fecha_propia' => $validated['fecha_propia'],
            'fecha_deseada' => $validated['fecha_deseada'],
            'mensaje' => $validated['mensaje'] ?? null,
            'estado' => 'pendiente',
        ]);

        $destinatario = Guardavida::find($validated['guardavida_destinatario_id']);
        if ($destinatario?->user) {
            $destinatario->user->notify(new FrancoIntercambioSolicitadoNotification(
                $intercambio->id,
                "{$guardavida->nombre} {$guardavida->apellido}",
                Carbon::parse($validated['fecha_propia'])->format('d/m/Y'),
                Carbon::parse($validated['fecha_deseada'])->format('d/m/Y'),
                $validated['mensaje'] ?? null,
            ));
        }

        return back()->with('success', 'Le pediste el cambio de franco a tu compañero. Te avisamos cuando responda.');
    }

    /**
     * El destinatario acepta: recién acá se cargan las excepciones de franco
     * de los dos, para esa fecha puntual (no toca el dia_franco fijo de nadie).
     */
    public function aceptar(FrancoIntercambio $francoIntercambio)
    {
        $this->autorizarDestinatario($francoIntercambio);

        if ($francoIntercambio->estado !== 'pendiente') {
            return back()->withErrors('Esta solicitud ya fue respondida.');
        }

        DB::transaction(function () use ($francoIntercambio) {
            $motivoSolicitante = "Intercambio de franco con {$francoIntercambio->destinatario->nombre} {$francoIntercambio->destinatario->apellido}";
            $motivoDestinatario = "Intercambio de franco con {$francoIntercambio->solicitante->nombre} {$francoIntercambio->solicitante->apellido}";

            // Solicitante: cede su día, toma el del compañero.
            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $francoIntercambio->guardavida_solicitante_id, 'fecha' => $francoIntercambio->fecha_propia],
                ['tipo' => 'cancelado', 'motivo' => $motivoSolicitante, 'cargado_por_user_id' => auth()->id()]
            );
            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $francoIntercambio->guardavida_solicitante_id, 'fecha' => $francoIntercambio->fecha_deseada],
                ['tipo' => 'agregado', 'motivo' => $motivoSolicitante, 'cargado_por_user_id' => auth()->id()]
            );

            // Destinatario: cede el día que le pidieron, toma el del solicitante.
            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $francoIntercambio->guardavida_destinatario_id, 'fecha' => $francoIntercambio->fecha_deseada],
                ['tipo' => 'cancelado', 'motivo' => $motivoDestinatario, 'cargado_por_user_id' => auth()->id()]
            );
            FrancoExcepcion::updateOrCreate(
                ['guardavida_id' => $francoIntercambio->guardavida_destinatario_id, 'fecha' => $francoIntercambio->fecha_propia],
                ['tipo' => 'agregado', 'motivo' => $motivoDestinatario, 'cargado_por_user_id' => auth()->id()]
            );

            $francoIntercambio->update(['estado' => 'aceptado', 'respondido_at' => now()]);
        });

        $this->notificarRespuesta($francoIntercambio, 'aceptado');

        return back()->with('success', 'Aceptaste el cambio de franco.');
    }

    public function rechazar(FrancoIntercambio $francoIntercambio)
    {
        $this->autorizarDestinatario($francoIntercambio);

        if ($francoIntercambio->estado !== 'pendiente') {
            return back()->withErrors('Esta solicitud ya fue respondida.');
        }

        $francoIntercambio->update(['estado' => 'rechazado', 'respondido_at' => now()]);

        $this->notificarRespuesta($francoIntercambio, 'rechazado');

        return back()->with('success', 'Rechazaste el cambio de franco.');
    }

    private function notificarRespuesta(FrancoIntercambio $francoIntercambio, string $estado): void
    {
        $solicitante = $francoIntercambio->solicitante;
        $destinatario = $francoIntercambio->destinatario;

        if ($solicitante?->user) {
            $solicitante->user->notify(new FrancoIntercambioRespondidoNotification(
                $francoIntercambio->id,
                "{$destinatario->nombre} {$destinatario->apellido}",
                $estado,
                $francoIntercambio->fecha_propia->format('d/m/Y'),
                $francoIntercambio->fecha_deseada->format('d/m/Y'),
            ));
        }
    }

    /**
     * El solicitante puede bajar su propio pedido mientras siga pendiente.
     */
    public function cancelar(FrancoIntercambio $francoIntercambio)
    {
        $guardavida = auth()->user()->guardavida;
        if (! $guardavida || $francoIntercambio->guardavida_solicitante_id !== $guardavida->id) {
            abort(403, 'No podés cancelar esta solicitud.');
        }

        if ($francoIntercambio->estado !== 'pendiente') {
            return back()->withErrors('Esta solicitud ya fue respondida.');
        }

        $francoIntercambio->update(['estado' => 'cancelado', 'respondido_at' => now()]);

        return back()->with('success', 'Cancelaste tu pedido de cambio de franco.');
    }

    private function autorizarDestinatario(FrancoIntercambio $francoIntercambio): void
    {
        $guardavida = auth()->user()->guardavida;
        if (! $guardavida || $francoIntercambio->guardavida_destinatario_id !== $guardavida->id) {
            abort(403, 'No podés responder esta solicitud.');
        }
    }
}

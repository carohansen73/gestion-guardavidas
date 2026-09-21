<?php

namespace App\Http\Controllers;

use App\Models\FrancoExcepcion;
use App\Models\FrancoIntercambio;
use App\Models\Guardavida;
use App\Notifications\FrancoIntercambioRespondidoMailNotification;
use App\Notifications\FrancoIntercambioRespondidoNotification;
use App\Notifications\FrancoIntercambioSolicitadoMailNotification;
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

        // Las de "te respondieron" son solo informativas, se dan por vistas
        // al entrar acá. Las de "te pidieron un cambio" NO se marcan solas —
        // siguen encendidas hasta que esa solicitud puntual se acepta,
        // rechaza o cancela (ver marcarSolicitudComoLeida()).
        auth()->user()->unreadNotifications()
            ->where('type', FrancoIntercambioRespondidoNotification::class)
            ->get()
            ->markAsRead();

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

        $diasFrancoGuardavida = $guardavida->diasFrancoActuales();

        if ($diasFrancoGuardavida === []) {
            return back()->withErrors('Antes de pedir un cambio, configurá tu franco fijo.');
        }

        $validated = $request->validate([
            'guardavida_destinatario_id' => 'required|exists:guardavidas,id',
            'fecha_propia' => 'required|date|after_or_equal:today',
            'fecha_deseada' => 'required|date|after_or_equal:today|different:fecha_propia',
            'mensaje' => 'nullable|string|max:255',
        ], [
            'fecha_propia.after_or_equal' => 'La fecha que ofrecés tiene que ser de hoy en adelante.',
            'fecha_deseada.after_or_equal' => 'La fecha que querés tomar tiene que ser de hoy en adelante.',
            'fecha_deseada.different' => 'La fecha que querés tomar no puede ser la misma que la que ofrecés.',
        ]);

        if ((int) $validated['guardavida_destinatario_id'] === $guardavida->id) {
            return back()->withErrors('No podés pedirte un cambio de franco a vos mismo.')->withInput();
        }

        if (! in_array((int) Carbon::parse($validated['fecha_propia'])->dayOfWeek, $diasFrancoGuardavida, true)) {
            return back()
                ->withErrors('El día que ofrecés tiene que ser tu franco fijo (los '.Guardavida::nombresDeDias($diasFrancoGuardavida).').')
                ->withInput();
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
            $fechaPropiaFmt = Carbon::parse($validated['fecha_propia'])->format('d/m/Y');
            $fechaDeseadaFmt = Carbon::parse($validated['fecha_deseada'])->format('d/m/Y');
            $nombreSolicitante = "{$guardavida->nombre} {$guardavida->apellido}";

            // Aviso en el sistema: inmediato, no depende de la cola.
            $destinatario->user->notify(new FrancoIntercambioSolicitadoNotification(
                $intercambio->id,
                $nombreSolicitante,
                $fechaPropiaFmt,
                $fechaDeseadaFmt,
                $validated['mensaje'] ?? null,
            ));

            // Mail: en cola, puede tardar o fallar sin afectar lo anterior.
            $destinatario->user->notify(new FrancoIntercambioSolicitadoMailNotification(
                $nombreSolicitante,
                $fechaPropiaFmt,
                $fechaDeseadaFmt,
                $validated['mensaje'] ?? null,
            ));
        }

        return back()->with('success', 'Le pediste el cambio de franco a tu compañero. Te avisamos cuando responda.');
    }

    /**
     * El destinatario acepta: recién acá se cargan las excepciones de franco
     * de los dos, para esa fecha puntual (no toca el esquema de franco fijo de nadie).
     */
    public function aceptar(FrancoIntercambio $francoIntercambio)
    {
        $this->autorizarDestinatario($francoIntercambio);

        if ($francoIntercambio->estado !== 'pendiente') {
            return back()->withErrors('Esta solicitud ya fue respondida.');
        }

        $destinatario = $francoIntercambio->destinatario;
        $diasFrancoDestinatario = $destinatario->diasFrancoActuales();

        if ($diasFrancoDestinatario === []) {
            return back()->withErrors('Antes de aceptar, configurá tu franco fijo.');
        }

        if (! in_array((int) $francoIntercambio->fecha_deseada->dayOfWeek, $diasFrancoDestinatario, true)) {
            return back()->withErrors('El día que te piden ceder no es tu franco (tu franco es los '.Guardavida::nombresDeDias($diasFrancoDestinatario).').');
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
        $this->marcarSolicitudComoLeida($francoIntercambio);

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
        $this->marcarSolicitudComoLeida($francoIntercambio);

        return back()->with('success', 'Rechazaste el cambio de franco.');
    }

    /**
     * Apaga el aviso de "te pidieron un cambio" para esta solicitud puntual
     * — recién cuando se acepta, rechaza o cancela, no antes.
     */
    private function marcarSolicitudComoLeida(FrancoIntercambio $francoIntercambio): void
    {
        $destinatario = $francoIntercambio->destinatario;
        if (! $destinatario?->user) {
            return;
        }

        $destinatario->user->unreadNotifications()
            ->where('type', FrancoIntercambioSolicitadoNotification::class)
            ->get()
            ->filter(fn ($n) => (int) ($n->data['intercambio_id'] ?? null) === $francoIntercambio->id)
            ->each->markAsRead();
    }

    private function notificarRespuesta(FrancoIntercambio $francoIntercambio, string $estado): void
    {
        $solicitante = $francoIntercambio->solicitante;
        $destinatario = $francoIntercambio->destinatario;

        if ($solicitante?->user) {
            $nombreDestinatario = "{$destinatario->nombre} {$destinatario->apellido}";
            $fechaPropiaFmt = $francoIntercambio->fecha_propia->format('d/m/Y');
            $fechaDeseadaFmt = $francoIntercambio->fecha_deseada->format('d/m/Y');

            // Aviso en el sistema: inmediato, no depende de la cola.
            $solicitante->user->notify(new FrancoIntercambioRespondidoNotification(
                $francoIntercambio->id,
                $nombreDestinatario,
                $estado,
                $fechaPropiaFmt,
                $fechaDeseadaFmt,
            ));

            // Mail: en cola, puede tardar o fallar sin afectar lo anterior.
            $solicitante->user->notify(new FrancoIntercambioRespondidoMailNotification(
                $nombreDestinatario,
                $estado,
                $fechaPropiaFmt,
                $fechaDeseadaFmt,
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

        $this->marcarSolicitudComoLeida($francoIntercambio);

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

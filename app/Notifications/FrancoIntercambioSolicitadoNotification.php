<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Aviso EN EL SISTEMA (canal database) de que alguien le pide cambiar el
 * franco al destinatario. A propósito NO implementa ShouldQueue: se guarda
 * al toque, en el mismo request en que se crea el pedido, para que el
 * puntito rojo aparezca ya mismo sin depender de que se procese ninguna
 * cola. El mail (que sí puede tardar o fallar) va aparte, en
 * FrancoIntercambioSolicitadoMailNotification.
 */
class FrancoIntercambioSolicitadoNotification extends Notification
{
    public function __construct(
        public int $intercambioId,
        public string $nombreSolicitante,
        public string $fechaPropia,
        public string $fechaDeseada,
        public ?string $mensaje = null,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'franco_intercambio_solicitado',
            'intercambio_id' => $this->intercambioId,
            'nombre_solicitante' => $this->nombreSolicitante,
            'fecha_propia' => $this->fechaPropia,
            'fecha_deseada' => $this->fechaDeseada,
            'mensaje' => "{$this->nombreSolicitante} te pidió cambiar el franco del {$this->fechaPropia} por el {$this->fechaDeseada}.",
        ];
    }
}

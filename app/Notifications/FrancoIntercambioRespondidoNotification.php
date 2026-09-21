<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Aviso EN EL SISTEMA (canal database) de que el compañero aceptó o rechazó
 * el pedido. Igual que la de solicitud: sin ShouldQueue a propósito, para
 * que se guarde de inmediato. El mail va en
 * FrancoIntercambioRespondidoMailNotification.
 */
class FrancoIntercambioRespondidoNotification extends Notification
{
    public function __construct(
        public int $intercambioId,
        public string $nombreDestinatario,
        public string $estado, // 'aceptado' | 'rechazado'
        public string $fechaPropia,
        public string $fechaDeseada,
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
        $verbo = $this->estado === 'aceptado' ? 'aceptó' : 'rechazó';

        return [
            'tipo' => 'franco_intercambio_respondido',
            'intercambio_id' => $this->intercambioId,
            'nombre_destinatario' => $this->nombreDestinatario,
            'estado' => $this->estado,
            'fecha_propia' => $this->fechaPropia,
            'fecha_deseada' => $this->fechaDeseada,
            'mensaje' => "{$this->nombreDestinatario} {$verbo} tu pedido de cambio de franco ({$this->fechaPropia} por {$this->fechaDeseada}).",
        ];
    }
}

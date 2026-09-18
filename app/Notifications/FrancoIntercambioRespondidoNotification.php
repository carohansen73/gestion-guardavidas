<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Se envía al solicitante cuando el compañero acepta o rechaza su pedido de
 * cambio de franco. En cola, igual que la de solicitud.
 */
class FrancoIntercambioRespondidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verbo = $this->estado === 'aceptado' ? 'aceptó' : 'rechazó';

        return (new MailMessage)
            ->subject("Tu pedido de cambio de franco fue {$this->estado}")
            ->greeting('¡Hola!')
            ->line("{$this->nombreDestinatario} {$verbo} tu pedido de cambiar el {$this->fechaPropia} por el {$this->fechaDeseada}.")
            ->action('Ver detalle', route('franco-intercambio.index'));
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

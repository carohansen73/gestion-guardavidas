<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Mail al solicitante avisando que le respondieron el pedido. En cola, igual
 * criterio que FrancoIntercambioSolicitadoMailNotification.
 */
class FrancoIntercambioRespondidoMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
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
        return ['mail'];
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
}

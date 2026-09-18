<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Se envía al compañero (destinatario) cuando alguien le pide cambiar el
 * franco. Se manda en cola (mail vía queue:listen) para no bloquear el
 * guardado del pedido — si el mail falla, el pedido igual queda cargado y
 * visible en el sistema.
 */
class FrancoIntercambioSolicitadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Te pidieron cambiar el franco')
            ->greeting('¡Hola!')
            ->line("{$this->nombreSolicitante} te pidió cambiar el franco.")
            ->line("Te ofrece el {$this->fechaPropia} a cambio de tomar el {$this->fechaDeseada}.");

        if ($this->mensaje) {
            $mail->line("Mensaje: \"{$this->mensaje}\"");
        }

        return $mail
            ->action('Ver pedido', route('franco-intercambio.index'))
            ->line('Podés aceptar o rechazar el pedido desde el sistema.');
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

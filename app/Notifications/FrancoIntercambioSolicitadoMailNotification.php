<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Mail al compañero (destinatario) avisando que le piden cambiar el franco.
 * Va en cola a propósito: si el mail tarda o falla, no bloquea ni afecta el
 * guardado del pedido — el aviso en el sistema lo maneja, por separado y de
 * forma inmediata, FrancoIntercambioSolicitadoNotification.
 */
class FrancoIntercambioSolicitadoMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
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
        return ['mail'];
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
}

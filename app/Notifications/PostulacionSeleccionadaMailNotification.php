<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Mail a quien fue seleccionado para trabajar la temporada, con la playa, el puesto y el turno asignados.
 * En cola (ver PostulacionRevisadaMailNotification): la selección se confirma en lote y no tiene que
 * depender de lo que tarde el servidor de mail.
 */
class PostulacionSeleccionadaMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $temporada,
        public string $playa,
        public ?string $puesto = null,
        public ?string $turno = null,      // 'Mañana' | 'Tarde'
        public ?string $funcion = null,
        public ?string $desde = null,      // d/m/Y: fecha de alta
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
            ->subject("Fuiste seleccionado/a para la temporada {$this->temporada}")
            ->greeting("¡Felicitaciones, {$notifiable->name}!")
            ->line("Fuiste seleccionado/a para trabajar en la temporada {$this->temporada}.")
            ->line("**Playa:** {$this->playa}");

        if ($this->puesto) {
            $mail->line("**Puesto:** {$this->puesto}");
        }
        if ($this->turno) {
            $mail->line("**Turno:** {$this->turno}");
        }
        if ($this->funcion) {
            $mail->line("**Función:** {$this->funcion}");
        }
        if ($this->desde) {
            $mail->line("**Alta desde:** {$this->desde}");
        }

        return $mail
            ->line('Ya podés ingresar al sistema con tu usuario de siempre para ver tu playa y registrar tu asistencia.')
            ->action('Ingresar al sistema', route('home'));
    }
}

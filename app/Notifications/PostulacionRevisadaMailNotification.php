<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Mail al postulante cuando el admin acepta, rechaza o pide correcciones en su inscripción (con el
 * comentario del admin, si lo escribió). Va en cola a propósito, igual que los mails de intercambio de
 * franco: si el mail tarda o falla, no bloquea ni afecta el guardado de la revisión.
 * Solo recibe datos simples (no modelos) para que el trabajo en cola no dependa de que sigan iguales.
 */
class PostulacionRevisadaMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  string  $estado  aceptada | rechazada | incompleta
     * @param  ?string  $fechaInicioTemporada  d/m/Y: hasta cuándo se puede corregir una inscripción incompleta
     */
    public function __construct(
        public string $temporada,
        public string $estado,
        public ?string $observaciones = null,
        public ?string $fechaInicioTemporada = null,
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
        $mail = (new MailMessage)->greeting("¡Hola, {$notifiable->name}!");

        match ($this->estado) {
            'aceptada' => $mail
                ->subject("Tu inscripción a {$this->temporada} fue aceptada")
                ->line("Revisamos tu inscripción a la temporada {$this->temporada} y fue aceptada.")
                ->line('La selección de quienes trabajan la temporada es un paso posterior: cuando estés seleccionado/a te avisamos por este medio.'),
            'rechazada' => $mail
                ->subject("Tu inscripción a {$this->temporada} no fue aceptada")
                ->line("Revisamos tu inscripción a la temporada {$this->temporada} y, lamentablemente, no fue aceptada."),
            default => $mail
                ->subject("Necesitamos que corrijas tu inscripción a {$this->temporada}")
                ->line("Revisamos tu inscripción a la temporada {$this->temporada} y necesitamos que corrijas o completes algo."),
        };

        if (filled($this->observaciones)) {
            $mail->line('**Comentario del equipo:**')->line($this->observaciones);
        }

        if ($this->estado === 'incompleta') {
            if ($this->fechaInicioTemporada) {
                $mail->line("Podés corregirla hasta que empiece la temporada ({$this->fechaInicioTemporada}).");
            }

            return $mail->action('Corregir mi inscripción', route('postulacion.paso', 1));
        }

        return $mail->action('Ver mi inscripción', route('home'));
    }
}

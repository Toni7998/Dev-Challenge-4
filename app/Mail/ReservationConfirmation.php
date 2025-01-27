<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Crear una nueva instancia del Mailable
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Construir el mensaje
     */
    public function build()
    {
        $cancelUrl = url('/cancel-reservation/' . $this->reservation->uuid);

        return $this->subject('Confirmación de tu reserva')
            ->view('emails.reservation_confirmation')
            ->with([
                'reservation' => $this->reservation,
                'cancelUrl' => $cancelUrl,
            ]);
    }

}

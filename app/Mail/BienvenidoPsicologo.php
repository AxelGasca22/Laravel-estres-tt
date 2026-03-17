<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BienvenidoPsicologo extends Mailable
{
    use Queueable, SerializesModels;

    public $psicologo;
    public $passwordPlain;
    public $urlConfirmacion;

    /**
     * Create a new message instance.
     */
    public function __construct(User $psicologo, string $passwordPlain)
    {
        $this->psicologo = $psicologo;
        $this->passwordPlain = $passwordPlain;

        // Genera un enlace seguro que expira en 48 horas
        $this->urlConfirmacion = URL::temporarySignedRoute(
            'psicologo.verify', now()->addHours(48), ['user' => $psicologo->id]
        );
    }

    public function build()
    {
        return $this->subject('¡Bienvenido a Vidazen! Confirma tu cuenta')
                    ->view('emails.psicologos.bienvenido');
    }
}

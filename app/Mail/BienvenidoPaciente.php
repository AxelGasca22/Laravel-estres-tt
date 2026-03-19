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

class BienvenidoPaciente extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $urlConfirmacion;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;

        $this->urlConfirmacion = URL::temporarySignedRoute(
            'paciente.verify', now()->addHours(48), ['user' => $user->id]
        );
    }

    public function build()
    {
        return $this->subject('Bienvenid@ - Vidazen')
                    ->view('emails.pacientes.bienvenido'); 
    }
}

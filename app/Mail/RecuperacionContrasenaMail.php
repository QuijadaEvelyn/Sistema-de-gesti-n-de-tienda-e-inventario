<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperacionContrasenaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia de notificación de recuperación de contraseña.
     */
    public function __construct(
        public Usuario $usuario,
        public string $token,
        public string $resetUrl,
    ) {}

    /**
     * Obtiene el sobre del mensaje con el remitente y asunto institucional.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ARCHIVUM · Restablecer tu Contraseña',
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje en formato HTML Blade curatorial.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperacion-contrasena',
            with: [
                'usuario' => $this->usuario,
                'token' => $this->token,
                'resetUrl' => $this->resetUrl,
            ],
        );
    }

    /**
     * Obtiene los archivos adjuntos para el mensaje.
     *
     * @return array<int, mixed>
     */
    public function attachments(): array
    {
        return [];
    }
}

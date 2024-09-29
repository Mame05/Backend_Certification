<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StructureCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $structure;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($structure,
    $password)
    {
        $this->structure = $structure;
        $this->password = $password;
    }


    /**
     * Get the message content definition.
     */
    public function build()
    {
        return $this->subject('Création de votre compte structure')
                    ->html("<h1>Bonjour,</h1>
                            <p>Nous avons le plaisir de vous informer que votre compte structure a été créé avec succès.</p>
                            <p><strong>Nom de la Commune :</strong> {$this->structure->nom_structure}</p>
                            <p><strong>Email :</strong> {$this->structure->user->email}</p>
                            <p><strong>Mot de passe :</strong> {$this->password}</p>
                            <p>Nous vous recommandons de vous connecter et de changer votre mot de passe dès que possible.</p>
                            <p>Cordialement,</p>");
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

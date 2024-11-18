<?php

namespace App\Notifications;

use App\Models\Annonce;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AnnonceModifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $annonce;

    /**
     * Create a new notification instance.
     */
    public function __construct($annonce)
    {
        $this->annonce = $annonce;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toArray(object $notifiable)
    {
        return [
            'contenu' => "Des modifications sur l'annonce : {$this->annonce->titre}",
            'annonce_id' => $this->annonce->id,
            'user_id' => $notifiable->id,
            'statut' => 'non-lu', 
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
   
}

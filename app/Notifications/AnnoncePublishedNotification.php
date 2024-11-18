<?php

namespace App\Notifications;

use App\Models\Annonce;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class AnnoncePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $annonce;

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
            'contenu' => "Nouvelle annonce : {$this->annonce->titre}",
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


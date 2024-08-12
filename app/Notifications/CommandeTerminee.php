<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\Attachment;
use App\Models\Commande;
use PDF; // Assurez-vous d'avoir la bibliothèque dompdf ou une autre bibliothèque pour générer des PDF

class CommandeTerminee extends Notification implements ShouldQueue
{
    use Queueable;

    protected $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Générer le PDF de la facture
        $pdf = PDF::loadView('emails.facture', ['commande' => $this->commande]);

        return (new MailMessage)
            ->subject('Votre commande est prête')
            ->line('Bonjour ' . $this->commande->nom)
            ->line('Votre commande est prête.')
            ->attachData($pdf->output(), 'facture.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}

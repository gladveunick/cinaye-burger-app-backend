<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;

class CommandeTermineeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }

    public function build()
    {
        $pdf = Pdf::loadView('emails.facture', ['commande' => $this->commande]);

        return $this->view('emails.commande_terminee')
                    ->attachData($pdf->output(), 'facture.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        'message' => 'Votre commande est prête !',
                    ]);
    }
}

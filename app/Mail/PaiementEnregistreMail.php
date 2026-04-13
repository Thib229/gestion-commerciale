<?php

namespace App\Mail;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaiementEnregistreMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Paiement $paiement)
    {
        $this->paiement->loadMissing(['facture.client', 'facture.user']);
    }

    public function envelope(): Envelope
    {
        $numero = $this->paiement->facture->numero_facture ?? '#'.$this->paiement->facture_id;
        return new Envelope(
            subject: 'Paiement enregistré — Facture ' . $numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.paiement-enregistre',
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('PaiementEnregistreMail failed', [
            'paiement_id' => $this->paiement->id,
            'error'       => $exception->getMessage(),
        ]);
    }
}

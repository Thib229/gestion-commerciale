<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FactureCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Facture $facture)
    {
        $this->facture->loadMissing(['client', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle facture créée — ' . ($this->facture->numero_facture ?? '#'.$this->facture->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.facture-created',
        );
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('FactureCreatedMail failed', [
            'facture_id' => $this->facture->id,
            'error'      => $exception->getMessage(),
        ]);
    }
}

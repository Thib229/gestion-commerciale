<?php

namespace App\Observers;

use App\Mail\PaiementEnregistreMail;
use App\Models\ActivityLog;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaiementObserver
{
    public function created(Paiement $paiement): void
    {
        $this->recalculateStatut($paiement->facture);

        // Log d'activité
        $userId = Auth::id() ?? $paiement->facture?->user_id;
        if ($userId) {
            ActivityLog::create([
                'user_id'      => $userId,
                'action'       => 'paiement.created',
                'subject_type' => Paiement::class,
                'subject_id'   => $paiement->id,
                'description'  => "Paiement de {$paiement->montant} F enregistré",
            ]);
        }

        // Dispatch email en queue
        try {
            $paiement->loadMissing(['facture.user', 'facture.client']);
            $user = $paiement->facture?->user;
            if ($user && $user->email) {
                Mail::to($user->email)->queue(new PaiementEnregistreMail($paiement));
            }
        } catch (\Throwable $e) {
            Log::error('PaiementObserver: email dispatch failed', [
                'paiement_id' => $paiement->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function deleted(Paiement $paiement): void
    {
        $this->recalculateStatut($paiement->facture);

        // Log d'activité
        $userId = Auth::id() ?? $paiement->facture?->user_id;
        if ($userId) {
            ActivityLog::create([
                'user_id'      => $userId,
                'action'       => 'paiement.deleted',
                'subject_type' => Paiement::class,
                'subject_id'   => $paiement->id,
                'description'  => "Paiement de {$paiement->montant} F supprimé",
            ]);
        }
    }

    private function recalculateStatut(Facture $facture): void
    {
        $paye = $facture->paiements()->sum('montant');

        $statut = match(true) {
            $paye <= 0               => 'impayée',
            $paye >= $facture->total => 'payée',
            default                  => 'partiellement payée',
        };

        $facture->updateQuietly(['statut' => $statut]);
    }
}

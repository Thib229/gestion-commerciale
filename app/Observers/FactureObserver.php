<?php

namespace App\Observers;

use App\Mail\FactureCreatedMail;
use App\Models\ActivityLog;
use App\Models\Facture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FactureObserver
{
    public function creating(Facture $facture): void
    {
        DB::transaction(function () use ($facture) {
            $year = now()->year;

            // Récupérer le dernier numéro de l'année pour cet utilisateur avec lock
            $last = Facture::where('user_id', $facture->user_id)
                ->whereYear('created_at', $year)
                ->whereNotNull('numero_facture')
                ->lockForUpdate()
                ->orderByDesc('numero_facture')
                ->value('numero_facture');

            $next = $last ? ((int) substr($last, -4)) + 1 : 1;

            $facture->numero_facture = sprintf('FAC-%d-%04d', $year, $next);
            $facture->public_token   = (string) Str::uuid();
            $facture->statut         = 'impayée';
        });
    }

    public function created(Facture $facture): void
    {
        // Log d'activité
        $userId = Auth::id() ?? $facture->user_id;
        if ($userId) {
            ActivityLog::create([
                'user_id'      => $userId,
                'action'       => 'facture.created',
                'subject_type' => Facture::class,
                'subject_id'   => $facture->id,
                'description'  => "Facture créée : {$facture->numero_facture}",
            ]);
        }

        // Dispatch email en queue
        try {
            $facture->loadMissing(['user', 'client']);
            if ($facture->user && $facture->user->email) {
                Mail::to($facture->user->email)->queue(new FactureCreatedMail($facture));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('FactureObserver: email dispatch failed', [
                'facture_id' => $facture->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}

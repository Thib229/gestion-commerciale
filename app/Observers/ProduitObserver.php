<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Produit;
use Illuminate\Support\Facades\Auth;

class ProduitObserver
{
    public function created(Produit $produit): void
    {
        $this->log('produit.created', $produit, "Produit créé : {$produit->nom}");
    }

    public function updated(Produit $produit): void
    {
        $this->log('produit.updated', $produit, "Produit modifié : {$produit->nom}");
    }

    public function deleted(Produit $produit): void
    {
        $this->log('produit.deleted', $produit, "Produit supprimé : {$produit->nom}");
    }

    private function log(string $action, Produit $produit, string $description): void
    {
        $userId = Auth::id() ?? $produit->user_id;
        if (!$userId) return;

        ActivityLog::create([
            'user_id'      => $userId,
            'action'       => $action,
            'subject_type' => Produit::class,
            'subject_id'   => $produit->id,
            'description'  => $description,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Facture;

class FacturePublicController extends Controller
{
    public function show(string $token)
    {
        $facture = Facture::where('public_token', $token)
            ->with(['client', 'produits', 'paiements', 'user.entrepriseProfile'])
            ->first();

        if (!$facture) {
            abort(404, 'Facture introuvable.');
        }

        $entrepriseProfile = $facture->user->entrepriseProfile ?? null;

        return view('factures.public', compact('facture', 'entrepriseProfile'));
    }
}

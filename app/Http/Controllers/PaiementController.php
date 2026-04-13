<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;

class PaiementController extends Controller
{
    /* Liste + formulaire d’ajout */
    public function index()
    {
        $paiements = Paiement::whereHas('facture', fn ($q) =>
                $q->where('user_id', Auth::id()))
            ->with('facture.client')
            ->orderByDesc('date_paiement')
            ->paginate(15)
            ->withQueryString();

        // Uniquement les factures non entièrement soldées
        $factures = Facture::where('user_id', Auth::id())
            ->with(['client', 'paiements'])
            ->get()
            ->filter(fn ($f) => $f->reste_a_regler > 0)
            ->values();

        return view('paiements.index', compact('paiements', 'factures'));
    }

    /* Enregistrement d’un paiement */
    public function store(Request $request)
    {
        $request->validate([
            'facture_id' => 'required|exists:factures,id',
            'montant'    => 'required|numeric|min:1',
        ]);

        $facture = Facture::where('id', $request->facture_id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($facture->reste_a_regler <= 0) {
            return back()->withErrors(['Cette facture est déjà entièrement soldée.'])->withInput();
        }

        if ($request->montant > $facture->reste_a_regler) {
            return back()
                ->withErrors(["Le montant saisi ({$request->montant} F) dépasse le reste à régler ({$facture->reste_a_regler} F)."])
                ->withInput();
        }

        Paiement::create([
            'facture_id'    => $facture->id,
            'montant'       => $request->montant,
            'date_paiement' => now()->toDateString(),
        ]);

        return back()->with('success', 'Paiement enregistré.');
    }
}

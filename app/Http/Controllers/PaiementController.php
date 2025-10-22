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
            ->with('facture')
            ->orderByDesc('date_paiement')
            ->get();

        $factures = Facture::where('user_id', Auth::id())
            ->orderByDesc('date')
            ->get();

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

        Paiement::create([
            'facture_id'   => $facture->id,
            'montant'      => $request->montant,
            'date_paiement'=> now()->toDateString(),
        ]);

        return back()->with('success', 'Paiement enregistré.');
    }
}

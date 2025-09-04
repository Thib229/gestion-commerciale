<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Client;
use App\Models\Produit;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class FactureController extends Controller
{
    public function index()
    {
        $factures = Facture::with('client')
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        return view('factures.index', compact('factures'));
    }

    public function create()
    {
        $clients = Client::where('user_id', Auth::id())->get();
        $produits = Produit::where('user_id', Auth::id())->get();

        return view('factures.create', compact('clients', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        $total = 0;
        $produitsValidés = [];

        foreach ($request->produits as $prod) {
            $produit = Produit::where('id', $prod['id'])
                              ->where('user_id', Auth::id())
                              ->first();

            if (!$produit) {
                return back()->withErrors(['Produit non trouvé.'])->withInput();
            }

            // Vérification du stock
            if ($prod['quantite'] > $produit->stock) {
                return back()
                    ->withErrors(["La quantité demandée pour le produit *{$produit->nom}* dépasse le stock disponible ({$produit->stock})."])
                    ->withInput();
            }

            $produitsValidés[] = [
                'produit' => $produit,
                'quantite' => $prod['quantite'],
                'prix' => $produit->prix_unitaire,
            ];

            $total += $produit->prix_unitaire * $prod['quantite'];
        }

        $facture = Facture::create([
            'client_id' => $request->client_id,
            'user_id' => Auth::id(),
            'total' => $total,
            'date' => now()->toDateString(),
        ]);

        foreach ($produitsValidés as $item) {
            $facture->produits()->attach($item['produit']->id, [
                'quantite' => $item['quantite'],
                'prix' => $item['prix'],
            ]);

            // Mise à jour du stock
            $item['produit']->decrement('stock', $item['quantite']);
        }

        return redirect()->route('dashboard')->with('success', 'Facture créée avec succès.');
    }

    public function show(Facture $facture)
    {
        $this->authorizeAccess($facture);

        $facture->load(['client', 'produits', 'paiements']);
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        $this->authorizeAccess($facture);

        $clients = Client::where('user_id', Auth::id())->get();
        $produits = Produit::where('user_id', Auth::id())->get();
        $facture->load('produits');

        return view('factures.edit', compact('facture', 'clients', 'produits'));
    }

    public function update(Request $request, Facture $facture)
    {
        $this->authorizeAccess($facture);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array',
            'produits.*.id' => 'required|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1',
        ]);

        $total = 0;
        $syncData = [];

        foreach ($request->produits as $prod) {
            $produit = Produit::where('id', $prod['id'])->where('user_id', Auth::id())->first();

            if (!$produit) {
                return back()->withErrors(['Produit invalide.'])->withInput();
            }

            $syncData[$prod['id']] = [
                'quantite' => $prod['quantite'],
                'prix' => $produit->prix_unitaire,
            ];

            $total += $produit->prix_unitaire * $prod['quantite'];
        }

        $facture->update([
            'client_id' => $request->client_id,
            'total' => $total,
            'date' => now()->toDateString(),
        ]);

        $facture->produits()->sync($syncData);

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    public function exportPdf(Facture $facture)
    {
        $this->authorizeAccess($facture);

        $facture->load(['client', 'produits', 'paiements']);
        $pdf = Pdf::loadView('factures.pdf', compact('facture'));
        return $pdf->download('facture-' . $facture->id . '.pdf');
    }

    private function authorizeAccess(Facture $facture)
    {
        if ($facture->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }
    }
}

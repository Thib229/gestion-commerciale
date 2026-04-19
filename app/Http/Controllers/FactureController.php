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
        $search = request('search');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $statut = request('statut');
        $entrepriseId = Auth::user()->entreprise_id;

        $factures = Facture::with('client')
            ->where('entreprise_id', $entrepriseId)
            ->when($search, fn ($q) => $q->filterClient($search))
            ->when($dateFrom || $dateTo, fn ($q) => $q->filterDateRange($dateFrom, $dateTo))
            ->when($statut, fn ($q) => $q->filterStatut($statut))
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('factures.index', compact('factures', 'search', 'dateFrom', 'dateTo', 'statut'));
    }

    public function create()
    {
        $entrepriseId = Auth::user()->entreprise_id;
        $clients = Client::where('entreprise_id', $entrepriseId)->get();
        $produits = Produit::where('entreprise_id', $entrepriseId)->get();

        return view('factures.create', compact('clients', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id'           => 'required|integer|exists:clients,id',
            'produits'            => 'required|array|min:1|max:50',
            'produits.*.id'       => 'required|integer|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1|max:9999',
        ]);

        // Vérifier que le client appartient à l'utilisateur connecté
        $client = Client::where('id', $request->client_id)
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->firstOrFail();

        $total = 0;
        $produitsValidés = [];

        foreach ($request->produits as $prod) {
            $produit = Produit::where('id', $prod['id'])
                              ->where('entreprise_id', Auth::user()->entreprise_id)
                              ->first();

            if (!$produit) {
                return back()->withErrors(['Produit non trouvé ou accès non autorisé.'])->withInput();
            }

            if ($prod['quantite'] > $produit->stock) {
                return back()
                    ->withErrors(["Stock insuffisant pour le produit « {$produit->nom} » (stock disponible : {$produit->stock}). Veuillez augmenter votre stock ou diminuer la quantité."])
                    ->withInput();
            }

            $produitsValidés[] = [
                'produit'  => $produit,
                'quantite' => (int) $prod['quantite'],
                'prix'     => $produit->prix_unitaire,
            ];

            $total += $produit->prix_unitaire * $prod['quantite'];
        }

        $facture = Facture::create([
            'client_id'     => $client->id,
            'user_id'       => Auth::id(),
            'entreprise_id' => Auth::user()->entreprise_id,
            'total'         => $total,
            'date'          => now()->toDateString(),
        ]);

        foreach ($produitsValidés as $item) {
            $facture->produits()->attach($item['produit']->id, [
                'quantite' => $item['quantite'],
                'prix'     => $item['prix'],
            ]);
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

        $entrepriseId = Auth::user()->entreprise_id;
        $clients = Client::where('entreprise_id', $entrepriseId)->get();
        $produits = Produit::where('entreprise_id', $entrepriseId)->get();
        $facture->load('produits');

        return view('factures.edit', compact('facture', 'clients', 'produits'));
    }

    public function update(Request $request, Facture $facture)
    {
        $this->authorizeAccess($facture);

        $request->validate([
            'client_id'           => 'required|integer|exists:clients,id',
            'produits'            => 'required|array|min:1|max:50',
            'produits.*.id'       => 'required|integer|exists:produits,id',
            'produits.*.quantite' => 'required|integer|min:1|max:9999',
        ]);

        // Vérifier que le client appartient à l'utilisateur connecté
        $client = Client::where('id', $request->client_id)
            ->where('entreprise_id', Auth::user()->entreprise_id)
            ->firstOrFail();

        // Restaurer le stock des anciens produits
        foreach ($facture->produits as $oldProduit) {
            $oldProduit->increment('stock', $oldProduit->pivot->quantite);
        }

        $total    = 0;
        $syncData = [];

        foreach ($request->produits as $prod) {
            $produit = Produit::where('id', $prod['id'])
                ->where('entreprise_id', Auth::user()->entreprise_id)
                ->first();

            if (!$produit) {
                foreach ($facture->produits as $oldProduit) {
                    $oldProduit->decrement('stock', $oldProduit->pivot->quantite);
                }
                return back()->withErrors(['Produit invalide ou accès non autorisé.'])->withInput();
            }

            if ($prod['quantite'] > $produit->stock) {
                foreach ($facture->produits as $oldProduit) {
                    $oldProduit->decrement('stock', $oldProduit->pivot->quantite);
                }
                return back()
                    ->withErrors(["Stock insuffisant pour le produit « {$produit->nom} » (stock disponible : {$produit->stock}). Veuillez augmenter votre stock ou diminuer la quantité."])
                    ->withInput();
            }

            $syncData[$prod['id']] = [
                'quantite' => (int) $prod['quantite'],
                'prix'     => $produit->prix_unitaire,
            ];

            $total += $produit->prix_unitaire * $prod['quantite'];
        }

        $facture->update([
            'client_id' => $client->id,
            'total'     => $total,
            'date'      => now()->toDateString(),
        ]);

        $facture->produits()->sync($syncData);

        foreach ($syncData as $produitId => $data) {
            Produit::find($produitId)->decrement('stock', $data['quantite']);
        }

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    public function exportPdf(Facture $facture)
    {
        $this->authorizeAccess($facture);

        if (!Auth::user()->canExportPdf()) {
            return redirect()->route('subscriptions.choose')
                ->with('error', 'L\'export PDF est disponible à partir du plan Pro. Veuillez mettre à niveau votre abonnement.');
        }

        $facture->load(['client', 'produits', 'paiements']);
        $entrepriseProfile = Auth::user()->entreprise;

        $pdf = Pdf::loadView('factures.pdf', compact('facture', 'entrepriseProfile'));
        $filename = 'facture-' . ($facture->numero_facture ?? $facture->id) . '.pdf';
        return $pdf->download($filename);
    }

    public function destroy(Facture $facture)
    {
        abort(403, 'La suppression d\'une facture n\'est pas autorisée.');
    }


    private function authorizeAccess(Facture $facture)
    {
        if ($facture->entreprise_id !== Auth::user()->entreprise_id) {
            abort(403, 'Accès non autorisé.');
        }
    }
}

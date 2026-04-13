<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $search = request('search');

        $produits = Produit::where('user_id', auth()->id())
            ->when($search, fn ($q) => $q->search($search))
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();
        return view('produits.index', compact('produits', 'search'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Produit::create([
            'nom' => $request->nom,
            'prix_unitaire' => $request->prix_unitaire,
            'stock' => $request->stock,
            'user_id' => auth()->id(), // ✅ associer l'utilisateur connecté
        ]);

        return redirect()->route('produits.index')->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Produit $produit)
    {
        // ✅ empêcher l'accès à un produit d'un autre utilisateur
        if ($produit->user_id !== auth()->id()) {
            abort(403);
        }

        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        if ($produit->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prix_unitaire' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $produit->update($request->only('nom', 'prix_unitaire', 'stock'));

        return redirect()->route('produits.index')->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->user_id !== auth()->id()) {
            abort(403);
        }

        $produit->delete();

        return redirect()->route('produits.index')->with('success', 'Produit supprimé.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Affiche la liste des clients appartenant à l'utilisateur connecté.
     */
    public function index()
    {
        $search = request('search');

        $clients = Client::where('user_id', Auth::id())
            ->when($search, fn ($q) => $q->search($search))
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();
        return view('clients.index', compact('clients', 'search'));
    }

    /**
     * Affiche le formulaire d'ajout de client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistre un nouveau client pour l'utilisateur connecté.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->canAddClientToday()) {
            $limit = Auth::user()->clientLimitPerDay();
            return redirect()->route('clients.index')
                ->with('error', "Limite atteinte : votre plan Basique permet d'ajouter {$limit} client(s) par jour. Passez au plan Pro pour un accès illimité.");
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:500',
        ]);

        $validated['user_id'] = Auth::id();

        $client = Client::create($validated);

        return redirect()->route('clients.show_after_create', $client)
            ->with('success', 'Client ajouté avec succès.');
    }

    /**
     * Affiche le formulaire de modification d’un client (sécurité par utilisateur).
     */
    public function edit(Client $client)
    {
        $this->authorizeClient($client);
        return view('clients.edit', compact('client'));
    }

    /**
     * Met à jour les infos d’un client existant (sécurité par utilisateur).
     */
    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:500',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client mis à jour.');
    }

    /**
     * Supprime un client (sécurité par utilisateur).
     */
    public function destroy(Client $client)
    {
        $this->authorizeClient($client);

        if ($client->factures()->exists()) {
            return redirect()->route('clients.index')
                ->with('error', 'Impossible de supprimer ce client car il possède des factures.');
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé.');
    }

    /**
     * Vérifie que le client appartient à l'utilisateur connecté.
     */
    private function authorizeClient(Client $client)
    {
        if ($client->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }
    }
}

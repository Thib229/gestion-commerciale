<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $entrepriseId = Auth::user()->entreprise_id;

        $nbClients = Client::where('entreprise_id', $entrepriseId)->count();
        $nbProduits = Produit::where('entreprise_id', $entrepriseId)->count();
        $nbFactures = Facture::where('entreprise_id', $entrepriseId)->count();
        $nbUtilisateurs = User::where('entreprise_id', $entrepriseId)->count();
        $totalPaiements = Paiement::whereHas('facture', function ($query) use ($entrepriseId) {
            $query->where('entreprise_id', $entrepriseId);
        })->sum('montant');
        $chiffreAffaires = Facture::where('entreprise_id', $entrepriseId)->sum('total');

        $produitsPopulaires = collect();
        $stats = collect();

        if (Auth::user()->canViewStatistics()) {
            $produitsPopulaires = Produit::withCount(['factures' => function ($query) use ($entrepriseId) {
                $query->where('entreprise_id', $entrepriseId);
            }])
            ->where('entreprise_id', $entrepriseId)
            ->orderByDesc('factures_count')
            ->take(5)
            ->get();

            $stats = Facture::where('entreprise_id', $entrepriseId)
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();
        }

        return view('dashboard', compact(
            'nbClients',
            'nbProduits',
            'nbFactures',
            'nbUtilisateurs',
            'totalPaiements',
            'chiffreAffaires',
            'produitsPopulaires',
            'stats',
        ));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Récupère l'ID de l'utilisateur connecté

        $nbClients = Client::where('user_id', $userId)->count();
        $nbProduits = Produit::where('user_id', $userId)->count();
        $nbFactures = Facture::where('user_id', $userId)->count();
        $totalPaiements = Paiement::whereHas('facture', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->sum('montant');
        $chiffreAffaires = Facture::where('user_id', $userId)->sum('total');

        $produitsPopulaires = collect();
        $stats = collect();

        if (Auth::user()->canViewStatistics()) {
            $produitsPopulaires = Produit::withCount(['factures' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->where('user_id', $userId)
            ->orderByDesc('factures_count')
            ->take(5)
            ->get();

            $stats = Facture::where('user_id', $userId)
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
            'totalPaiements',
            'chiffreAffaires',
            'produitsPopulaires',
            'stats',
        ));
    }
}

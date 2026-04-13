<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\EntrepriseProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FacturePublicController;
use App\Http\Controllers\ActivityLogController;

// Rate limiting sur les routes de paiement
RateLimiter::for('payment', function (Request $request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
});

/*
|--------------------------------------------------------------------------
| Pages publiques
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

// Page publique de facture (sans auth)
Route::get('/factures/public/{token}', [FacturePublicController::class, 'show'])->name('factures.public');

/*
|--------------------------------------------------------------------------
| Choix abonnement (accessible après connexion seulement)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/abonnements', [SubscriptionController::class, 'choose'])->name('subscriptions.choose');
    Route::post('/abonnements/souscrire', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe')->middleware('throttle:payment');
    Route::get('/abonnements/callback', [SubscriptionController::class, 'callback'])->name('subscriptions.callback');
    Route::get('/abonnements/historique', [SubscriptionController::class, 'history'])->name('subscriptions.history');
});

/*
|--------------------------------------------------------------------------
| Zone protégée : Auth + Abonnement / Essai
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'check.subscription'])->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /* Profil */
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /* CRUD principaux */
    Route::resource('clients',  ClientController::class);
    Route::resource('produits', ProduitController::class);
    Route::resource('factures', FactureController::class);

    /* Paiements */
    Route::get ('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

    /* Profil entreprise */
    Route::get('/entreprise/profil', [EntrepriseProfileController::class, 'edit'])->name('entreprise.edit');
    Route::put('/entreprise/profil', [EntrepriseProfileController::class, 'update'])->name('entreprise.update');

    /* Export PDF facture */
    Route::get('/factures/{facture}/export-pdf', [FactureController::class, 'exportPdf'])
        ->name('factures.exportPdf');

    /* Logs d'activité (Premium) */
    Route::get('/activite', [ActivityLogController::class, 'index'])->name('activite.index');
});

/*
|--------------------------------------------------------------------------
| Auth (login, register, reset password…)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\PaiementController;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    /* Profil */
    Route::get('/profile',  [ProfileController::class, 'edit'   ])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update' ])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /* CRUD principaux */
    Route::resource('clients',  ClientController::class);
    Route::resource('produits', ProduitController::class);
    Route::resource('factures', FactureController::class);

    /* Paiements : liste + création */
    Route::get ('/paiements', [PaiementController::class, 'index'])->name('paiements.index');
    Route::post('/paiements', [PaiementController::class, 'store'])->name('paiements.store');

    /* Export PDF facture */
    Route::get('/factures/{facture}/export-pdf', [FactureController::class, 'exportPdf'])
        ->name('factures.exportPdf');
});

require __DIR__.'/auth.php';

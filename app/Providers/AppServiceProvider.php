<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Produit;
use App\Observers\ClientObserver;
use App\Observers\FactureObserver;
use App\Observers\PaiementObserver;
use App\Observers\ProduitObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Facture::observe(FactureObserver::class);
        Paiement::observe(PaiementObserver::class);
        Client::observe(ClientObserver::class);
        Produit::observe(ProduitObserver::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <-- Ajoute cette ligne

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fixe la longueur par défaut des chaînes pour éviter l'erreur de clé trop longue MySQL
        Schema::defaultStringLength(191);
    }
}

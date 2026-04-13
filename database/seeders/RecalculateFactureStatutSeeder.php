<?php

namespace Database\Seeders;

use App\Models\Facture;
use Illuminate\Database\Seeder;

class RecalculateFactureStatutSeeder extends Seeder
{
    public function run(): void
    {
        Facture::with('paiements')->get()->each(function (Facture $facture) {
            $paye = $facture->paiements->sum('montant');

            $statut = match(true) {
                $paye <= 0               => 'impayée',
                $paye >= $facture->total => 'payée',
                default                  => 'partiellement payée',
            };

            $facture->updateQuietly(['statut' => $statut]);
            $this->command->info("{$facture->numero_facture} => {$statut}");
        });
    }
}

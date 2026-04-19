<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    public function view(User $user, Paiement $paiement): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $paiement->facture->entreprise_id;
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $paiement->facture->entreprise_id;
    }
}

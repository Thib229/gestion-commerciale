<?php

namespace App\Policies;

use App\Models\Paiement;
use App\Models\User;

class PaiementPolicy
{
    public function view(User $user, Paiement $paiement): bool
    {
        return $user->id === $paiement->facture->user_id;
    }

    public function delete(User $user, Paiement $paiement): bool
    {
        return $user->id === $paiement->facture->user_id;
    }
}

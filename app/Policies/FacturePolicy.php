<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function view(User $user, Facture $facture): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $facture->entreprise_id;
    }

    public function update(User $user, Facture $facture): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $facture->entreprise_id;
    }

    public function exportPdf(User $user, Facture $facture): bool
    {
        return $user->entreprise_id !== null
            && $user->entreprise_id === $facture->entreprise_id
            && $user->canExportPdf();
    }
}

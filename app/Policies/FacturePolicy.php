<?php

namespace App\Policies;

use App\Models\Facture;
use App\Models\User;

class FacturePolicy
{
    public function view(User $user, Facture $facture): bool
    {
        return $user->id === $facture->user_id;
    }

    public function update(User $user, Facture $facture): bool
    {
        return $user->id === $facture->user_id;
    }

    public function exportPdf(User $user, Facture $facture): bool
    {
        return $user->id === $facture->user_id && $user->canExportPdf();
    }
}

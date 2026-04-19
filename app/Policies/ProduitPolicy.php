<?php

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;

class ProduitPolicy
{
    public function view(User $user, Produit $produit): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $produit->entreprise_id;
    }

    public function update(User $user, Produit $produit): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $produit->entreprise_id;
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $user->entreprise_id !== null && $user->entreprise_id === $produit->entreprise_id;
    }
}

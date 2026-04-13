<?php

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;

class ProduitPolicy
{
    public function view(User $user, Produit $produit): bool
    {
        return $user->id === $produit->user_id;
    }

    public function update(User $user, Produit $produit): bool
    {
        return $user->id === $produit->user_id;
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $user->id === $produit->user_id;
    }
}

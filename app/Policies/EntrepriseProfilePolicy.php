<?php

namespace App\Policies;

use App\Models\EntrepriseProfile;
use App\Models\User;

class EntrepriseProfilePolicy
{
    public function view(User $user, EntrepriseProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function update(User $user, EntrepriseProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }
}

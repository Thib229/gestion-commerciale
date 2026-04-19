<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePremiumMultiUsers
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        abort_if(!$user, 401);
        abort_unless($user->isAdmin(), 403, 'Accès réservé à l’administrateur.');
        abort_unless($user->hasPremiumMultiUsersAccess(), 403, 'Fonctionnalité disponible uniquement en Premium.');

        return $next($request);
    }
}

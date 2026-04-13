<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Vérifier la période d'essai (7 jours)
        if ($user->trial_started_at) {
            $trialEnd = $user->trial_started_at->addDays(7);
            if (now()->lessThanOrEqualTo($trialEnd)) {
                return $next($request);
            }
        }

        // 2. Vérifier un abonnement actif non expiré
        $subscription = $user->subscription()
            ->where('is_active', true)
            ->where('is_trial', false)
            ->where('ends_at', '>=', now())
            ->first();

        if ($subscription) {
            // Synchroniser le statut sur le user si nécessaire
            if ($user->subscription_status !== 'active') {
                $user->update(['subscription_status' => 'active']);
            }
            return $next($request);
        }

        // 3. Abonnement expiré → mettre à jour le statut et bloquer
        if ($user->subscription_status === 'active') {
            $user->update(['subscription_status' => 'expired']);
        }

        return redirect()->route('subscriptions.choose')
            ->with('error', 'Votre période d\'essai est terminée. Veuillez choisir un abonnement.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    /**
     * Historique des paiements d'abonnement
     */
    public function history()
    {
        $paiements = Auth::user()
            ->subscriptionPayments()
            ->with('plan')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('abonnements.historique', compact('paiements'));
    }

    public function choose()
    {
        $plans = Plan::all();
        $user  = Auth::user();

        $trialDaysLeft = null;
        if ($user->trial_started_at) {
            $trialEnd      = $user->trial_started_at->addDays(7);
            $trialDaysLeft = max(0, now()->diffInDays($trialEnd, false));
        }

        return view('subscriptions.choose', compact('plans', 'trialDaysLeft'));
    }

    /**
     * Initie le paiement FedaPay et redirige vers la page de paiement
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id'         => 'required|integer|exists:plans,id',
            'duration_months' => 'required|integer|in:1,3,6,12',
        ]);

        $user     = Auth::user();
        $plan     = Plan::findOrFail($request->plan_id);
        $duration = (int) $request->duration_months;
        $price    = self::calculatePrice($plan->price, $duration);

        $apiKey  = config('services.fedapay.secret_key');
        $isLive  = config('services.fedapay.env') === 'live';
        $baseUrl = $isLive ? 'https://api.fedapay.com/v1' : 'https://sandbox-api.fedapay.com/v1';

        $http = Http::withToken($apiKey);
        // Désactiver SSL uniquement en local (jamais en production)
        if (!$isLive && app()->environment('local')) {
            $http = $http->withoutVerifying();
        }

        // 1. Chercher le customer existant ou le créer
        $searchResponse    = $http->get("{$baseUrl}/customers", ['filters[email]' => $user->email]);
        $existingCustomers = $searchResponse->json('v1/customers') ?? [];

        if (!empty($existingCustomers)) {
            $customerId = $existingCustomers[0]['id'];
        } else {
            $customerResponse = $http->post("{$baseUrl}/customers", [
                'firstname' => strip_tags($user->name),
                'lastname'  => '',
                'email'     => $user->email,
            ]);

            if ($customerResponse->failed()) {
                return back()->with('error', 'Impossible de contacter le service de paiement. Réessayez.');
            }

            $customerId = $customerResponse->json('v1/customer.id');
        }

        $durationLabel = match($duration) {
            1  => '1 mois', 3 => '3 mois', 6 => '6 mois', 12 => '1 an',
        };

        // 2. Créer la transaction
        $transactionResponse = $http->post("{$baseUrl}/transactions", [
            'description'  => "Abonnement {$plan->name} - {$durationLabel}",
            'amount'       => $price,
            'currency'     => ['iso' => 'XOF'],
            'callback_url' => route('subscriptions.callback', [
                'plan_id'         => $plan->id,
                'duration_months' => $duration,
            ]),
            'customer' => ['id' => $customerId],
        ]);

        if ($transactionResponse->failed()) {
            return back()->with('error', 'Impossible de créer la transaction. Réessayez.');
        }

        $paymentUrl = $transactionResponse->json('v1/transaction.payment_url');

        if (!$paymentUrl || !filter_var($paymentUrl, FILTER_VALIDATE_URL)) {
            return back()->with('error', 'Lien de paiement invalide. Réessayez.');
        }

        // Vérifier que l'URL appartient bien à FedaPay
        $allowedHosts = ['sandbox-process.fedapay.com', 'process.fedapay.com'];
        $host = parse_url($paymentUrl, PHP_URL_HOST);
        if (!in_array($host, $allowedHosts)) {
            return back()->with('error', 'Redirection de paiement non autorisée.');
        }

        return redirect($paymentUrl);
    }

    /**
     * Calcule le prix selon la durée avec réductions
     */
    public static function calculatePrice(int $monthlyPrice, int $duration): int
    {
        return match($duration) {
            1  => $monthlyPrice,
            3  => (int) round($monthlyPrice * 3 * 0.90),  // -10%
            6  => (int) round($monthlyPrice * 6 * 0.80),  // -20%
            12 => (int) round($monthlyPrice * 12 * 0.70), // -30%
            default => $monthlyPrice,
        };
    }

    /**
     * Callback FedaPay — redirection GET après paiement
     */
    public function callback(Request $request)
    {
        $transactionId = $request->query('id');
        $status        = $request->query('status');
        $planId        = $request->query('plan_id');
        $duration      = (int) $request->query('duration_months', 1);

        // Validation stricte des paramètres
        if (!is_numeric($transactionId) || !is_numeric($planId) || !in_array($duration, [1, 3, 6, 12])) {
            abort(400, 'Paramètres invalides.');
        }

        if ($status !== 'approved') {
            return redirect()->route('subscriptions.choose')
                ->with('error', 'Paiement non complété. Veuillez réessayer.');
        }

        $apiKey  = config('services.fedapay.secret_key');
        $isLive  = config('services.fedapay.env') === 'live';
        $baseUrl = $isLive ? 'https://api.fedapay.com/v1' : 'https://sandbox-api.fedapay.com/v1';

        $http = Http::withToken($apiKey);
        if (!$isLive && app()->environment('local')) {
            $http = $http->withoutVerifying();
        }

        // Vérification du statut réel via l'API FedaPay
        $response    = $http->get("{$baseUrl}/transactions/{$transactionId}");
        $transaction = $response->json('v1/transaction');

        if (!$transaction || $transaction['status'] !== 'approved') {
            return redirect()->route('subscriptions.choose')
                ->with('error', 'Paiement non vérifié. Contactez le support.');
        }

        $user = Auth::user();
        $plan = Plan::find($planId);

        if (!$plan) {
            return redirect()->route('subscriptions.choose')
                ->with('error', 'Plan introuvable. Contactez le support.');
        }

        // Vérifier que la transaction correspond bien à cet utilisateur
        $expectedEmail = $transaction['customer']['email'] ?? null;
        if ($expectedEmail && $expectedEmail !== $user->email) {
            abort(403, 'Transaction non autorisée.');
        }

        // Désactiver l'abonnement actif s'il existe
        Subscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        Subscription::create([
            'user_id'         => $user->id,
            'plan_id'         => $plan->id,
            'duration_months' => $duration,
            'is_trial'        => false,
            'is_active'       => true,
            'reminder_sent'   => false,
            'starts_at'       => now(),
            'ends_at'         => now()->addMonths($duration),
        ]);

        $user->update(['subscription_status' => 'active']);

        // Enregistrer le paiement dans l'historique
        SubscriptionPayment::create([
            'user_id'           => $user->id,
            'plan_id'           => $plan->id,
            'montant'           => $transaction['amount'] ?? self::calculatePrice($plan->price, $duration),
            'devise'            => 'XOF',
            'statut'            => 'réussie',
            'reference_fedapay' => (string) $transactionId,
        ]);

        $durationLabel = match($duration) {
            1 => '1 mois', 3 => '3 mois', 6 => '6 mois', 12 => '1 an',
        };

        return redirect()->route('dashboard')
            ->with('success', "Paiement confirmé ! Abonnement {$plan->name} activé pour {$durationLabel}.");
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringSoon;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    protected $signature   = 'subscriptions:remind';
    protected $description = 'Envoie un rappel aux utilisateurs dont l\'abonnement expire dans 7 jours';

    public function handle(): void
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->where('is_active', true)
            ->where('is_trial', false)
            ->where('reminder_sent', false)
            ->whereBetween('ends_at', [now()->addDays(7)->startOfDay(), now()->addDays(7)->endOfDay()])
            ->get();

        foreach ($subscriptions as $subscription) {
            $subscription->user->notify(new SubscriptionExpiringSoon($subscription));
            $subscription->update(['reminder_sent' => true]);
            $this->info("Rappel envoyé à : {$subscription->user->email}");
        }

        $this->info("Total : {$subscriptions->count()} rappel(s) envoyé(s).");
    }
}

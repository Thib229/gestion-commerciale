<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoon extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $planName  = $this->subscription->plan->name ?? 'votre plan';
        $expiresAt = $this->subscription->ends_at->format('d/m/Y');

        return (new MailMessage)
            ->subject('Votre abonnement expire dans 7 jours')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre abonnement **{$planName}** expire le **{$expiresAt}**.")
            ->line('Renouvelez dès maintenant pour ne pas perdre accès à vos données.')
            ->action('Renouveler mon abonnement', route('subscriptions.choose'))
            ->line('Merci de nous faire confiance.');
    }
}

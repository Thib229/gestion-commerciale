<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'trial_started_at',
        'subscription_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_started_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Retourne l'abonnement actif (essai ou payant)
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('is_active', true)
            ->where('ends_at', '>=', now())
            ->latest();
    }

    public function entrepriseProfile()
    {
        return $this->hasOne(\App\Models\EntrepriseProfile::class);
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(\App\Models\SubscriptionPayment::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class);
    }

    /**
     * Vérifie si l'utilisateur est en période d'essai valide
     */
    public function isOnTrial(): bool
    {
        return $this->trial_started_at
            && now()->lessThanOrEqualTo($this->trial_started_at->addDays(7));
    }

    /**
     * Vérifie si l'utilisateur peut exporter en PDF
     */
    public function canExportPdf(): bool
    {
        if ($this->isOnTrial()) return true;

        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub && $sub->plan && $sub->plan->pdf_enabled;
    }

    /**
     * Retourne le nom du plan actif
     */
    public function getPlanName(): string
    {
        if ($this->isOnTrial()) return 'Essai gratuit';

        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub && $sub->plan ? $sub->plan->name : 'Aucun';
    }

    /**
     * Vérifie si l'utilisateur peut voir les statistiques
     */
    public function canViewStatistics(): bool
    {
        if ($this->isOnTrial()) return true;

        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub && $sub->plan && $sub->plan->statistics_enabled;
    }

    /**
     * Retourne la limite de clients par jour (null = illimité)
     */
    public function clientLimitPerDay(): ?int
    {
        if ($this->isOnTrial()) return null;

        $sub = $this->activeSubscription()->with('plan')->first();
        return $sub && $sub->plan ? $sub->plan->client_limit_per_day : 1;
    }

    /**
     * Vérifie si l'utilisateur peut encore ajouter un client aujourd'hui
     */
    public function canAddClientToday(): bool
    {
        $limit = $this->clientLimitPerDay();
        if ($limit === null) return true;

        $count = \App\Models\Client::where('user_id', $this->id)
            ->whereDate('created_at', today())
            ->count();

        return $count < $limit;
    }
}

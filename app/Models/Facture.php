<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'total',
        'date',
        'user_id',
        'entreprise_id',
    ];

    public function produits()
    {
        return $this->belongsToMany(Produit::class)->withPivot('quantite', 'prix');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseProfile::class, 'entreprise_id');
    }

    // ✅ Montant déjà payé
    public function getMontantPayeAttribute()
    {
        return $this->paiements()->sum('montant');
    }

    // ✅ Reste à payer (total - payé)
    public function getResteAReglerAttribute()
    {
        return $this->total - $this->montant_paye;
    }

    // Scopes de filtrage
    public function scopeFilterClient($query, string $term)
    {
        return $query->whereHas('client', function ($q) use ($term) {
            $q->where('nom', 'like', '%' . $term . '%');
        });
    }

    public function scopeFilterDateRange($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('date', '<=', $to);
        }
        return $query;
    }

    public function scopeFilterStatut($query, string $statut)
    {
        return $query->where('statut', $statut);
    }
}

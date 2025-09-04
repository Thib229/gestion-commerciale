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
        'user_id', // Ajout pour lier à l'utilisateur connecté
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
}

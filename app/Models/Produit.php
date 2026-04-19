<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prix_unitaire', 'stock', 'user_id', 'entreprise_id'];

    public function factures()
    {
        return $this->belongsToMany(Facture::class, 'facture_produit')
                    ->withPivot('quantite', 'prix')
                    ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseProfile::class, 'entreprise_id');
    }

    // Scope de recherche par nom
    public function scopeSearch($query, string $term)
    {
        return $query->where('nom', 'like', '%' . $term . '%');
    }
}

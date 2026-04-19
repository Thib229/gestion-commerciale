<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'email', 'telephone', 'adresse', 'user_id', 'entreprise_id'];

    // 🔁 Un client appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entreprise()
    {
        return $this->belongsTo(EntrepriseProfile::class, 'entreprise_id');
    }

    // 🔁 Un client peut avoir plusieurs factures
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    // Scope de recherche par nom ou email
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nom', 'like', '%' . $term . '%')
              ->orWhere('email', 'like', '%' . $term . '%');
        });
    }
}


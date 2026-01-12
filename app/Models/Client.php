<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Ajoute 'user_id' pour que Laravel puisse l'insérer lors du store()
    protected $fillable = ['nom', 'email', 'telephone', 'adresse', 'user_id'];

    // 🔁 Un client appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔁 Un client peut avoir plusieurs factures
    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
}


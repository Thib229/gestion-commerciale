<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepriseProfile extends Model
{
    protected $fillable = [
        'user_id', 'nom', 'logo_path', 'adresse',
        'telephone', 'email', 'numero_fiscal',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'entreprise_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'entreprise_id');
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'entreprise_id');
    }

    public function factures()
    {
        return $this->hasMany(Facture::class, 'entreprise_id');
    }

    /**
     * Vérifie si le profil est complet (tous les champs obligatoires renseignés)
     */
    public function isComplete(): bool
    {
        return !empty($this->nom)
            && !empty($this->adresse)
            && !empty($this->telephone)
            && !empty($this->email)
            && !empty($this->numero_fiscal);
    }
}

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

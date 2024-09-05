<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'telephone',
        'email',  // Ajout du champ email
        'adresse',
        'user_id',
        'active',
        'photo', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoAttribute()
    {
        // Retourne l'URL de l'avatar par défaut si aucune photo n'est définie
        return $this->attributes['photo'] ?? 'https://url-to-default-avatar';
    }
}

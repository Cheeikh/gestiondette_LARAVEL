<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'prix',
        'qteStock',
        'quantite_seuil',
    ];

    protected $casts = [
        'prix' => 'float',
        'qteStock' => 'integer',
        'quantite_seuil' => 'integer', // Casting de 'quantite_seuil' en entier
    ];
}

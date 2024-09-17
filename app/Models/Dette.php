<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'montant',
        'date',
        'date_echeance'
    ];




    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
            ->withPivot('qte_vente', 'prix_vente');
    }


    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}

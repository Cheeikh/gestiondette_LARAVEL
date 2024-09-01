<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'qte_stock', 'prix_vente'];

    public function dettes()
    {
        return $this->belongsToMany(Dette::class)->withPivot('qte_vente', 'prix_vente')->withTimestamps();
    }
}

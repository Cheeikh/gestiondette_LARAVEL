<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'total_amount',
        'description',
        'status'
    ];


    protected $casts = [
        'total_amount' => 'float',
    ];

    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'demande_article')
            ->withPivot('quantity', 'price');
    }

    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 2);
    }

    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = round($value, 2);
    }
}

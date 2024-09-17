<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'surname', 'telephone', 'email', 'adresse', 'user_id', 'active', 'category_id', 'max_montant'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    public function getPhotoAttribute()
    {
        if ($this->user && $this->user->photo_local) {
            // Adjusts the path for correct web access, assuming photo_local is already a full path
            return asset($this->user->photo_local);
        }
        return asset('images/Profile-Avatar-PNG.png'); // Default image if no photo is associated
    }

}

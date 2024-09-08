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
        'email',
        'adresse',
        'user_id',
        'active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

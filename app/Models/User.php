<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'email',
        'password',
        'role_id',
        'active',
        'photo_cloud',
        'photo_local',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_local) {
            return asset($this->photo_local);
        }
        return asset('images/Profile-Avatar-PNG.png'); // Default image if no photo is associated
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    // Method to retrieve read notifications
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }
}

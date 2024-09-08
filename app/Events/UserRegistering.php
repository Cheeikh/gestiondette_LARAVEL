<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class UserRegistering
{
    use Dispatchable, SerializesModels;

    public $user;
    public $photo;

    public function __construct(User $user, $photo = null)
    {
        $this->user = $user;
        $this->photo = $photo;
    }
}

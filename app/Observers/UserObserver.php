<?php

namespace App\Observers;

use App\Models\User;
use App\Events\UserCreated;

class UserObserver
{
    public function created(User $user)
    {
        event(new UserCreated($user));
    }
}

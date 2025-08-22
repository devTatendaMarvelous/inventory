<?php

namespace App\Traits;

use App\Models\User;

trait Resolvers
{
    function resolveUserName(User $user)
    {
        return $user->first_name.' '.$user->middle_name.' '.$user->last_name;
    }
}

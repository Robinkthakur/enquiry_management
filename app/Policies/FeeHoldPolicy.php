<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeeHold;

class FeeHoldPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasPermissionTo('holds.view');
    }

    public function view($user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }

    public function update($user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }

    public function delete($user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }
}

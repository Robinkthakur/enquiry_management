<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeeHold;

class FeeHoldPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('holds.view');
    }

    public function view(User $user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }

    public function update(User $user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }

    public function delete(User $user, FeeHold $hold): bool
    {
        return $user->hasPermissionTo('holds.manage');
    }
}

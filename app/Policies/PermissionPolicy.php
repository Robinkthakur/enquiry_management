<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function view($user, Permission $permission): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function create($user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update($user, Permission $permission): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete($user, Permission $permission): bool
    {
        return $user->hasRole('Super Admin');
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function view($user, Role $role): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function create($user): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function update($user, Role $role): bool
    {
        return $user->hasRole('Super Admin');
    }

    public function delete($user, Role $role): bool
    {
        return $user->hasRole('Super Admin');
    }
}

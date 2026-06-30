<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function view($user, User $model): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function create($user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function update($user, User $model): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function delete($user, User $model): bool
    {
        if ($model->hasRole('Super Admin')) {
            return false;
        }
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }
}

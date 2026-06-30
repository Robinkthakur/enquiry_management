<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    public function viewAny(Admin $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function view(Admin $user, Admin $model): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function create(Admin $user): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function update(Admin $user, Admin $model): bool
    {
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }

    public function delete(Admin $user, Admin $model): bool
    {
        if ($model->hasRole('Super Admin')) {
            return false;
        }
        return $user->hasRole('Super Admin') || $user->hasRole('Admin');
    }
}

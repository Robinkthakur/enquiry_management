<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeeInstallment;

class FeeInstallmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('fees.view');
    }

    public function view(User $user, FeeInstallment $installment): bool
    {
        return $user->hasPermissionTo('fees.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function update(User $user, FeeInstallment $installment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function delete(User $user, FeeInstallment $installment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }
}

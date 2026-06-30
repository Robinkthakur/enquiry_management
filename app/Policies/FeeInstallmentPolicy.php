<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FeeInstallment;

class FeeInstallmentPolicy
{
    public function viewAny($user): bool
    {
        return $user->hasRole('Student') || $user->hasPermissionTo('fees.view');
    }

    public function view($user, FeeInstallment $installment): bool
    {
        if ($user->hasRole('Student')) {
            return $user->admission?->id === $installment->admission_id;
        }
        return $user->hasPermissionTo('fees.view');
    }

    public function create($user): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function update($user, FeeInstallment $installment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }

    public function delete($user, FeeInstallment $installment): bool
    {
        return $user->hasPermissionTo('fees.manage');
    }
}
